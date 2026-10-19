<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Actions\MakeImageThumbnail;
use App\Models\Pet;
use App\Models\PetClinicalPhoto;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

/**
 * Adds dated photos to a pet's clinical follow-up (one label per lesion or
 * nodule being monitored), or edits the label, date and note of one photo.
 * The files go to the private disk, never the public one.
 *
 * @property-read list<string> $knownLabels
 */
class ClinicalPhotoForm extends Component
{
    use WithFileUploads;

    public Pet $pet;

    public ?PetClinicalPhoto $clinicalPhoto = null;

    public string $label = '';

    public string $takenAt = '';

    public string $photoNotes = '';

    /**
     * @var array<int, TemporaryUploadedFile>
     */
    public array $photos = [];

    public function mount(Pet $pet, ?PetClinicalPhoto $clinicalPhoto = null): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_unless(Auth::user()->canEditArea('health'), 403);
        abort_if($clinicalPhoto !== null && $clinicalPhoto->pet_id !== $pet->id, 404);

        $this->pet = $pet;
        $this->clinicalPhoto = $clinicalPhoto;
        $this->takenAt = today()->toDateString();

        if ($clinicalPhoto === null) {
            return;
        }

        $this->label = $clinicalPhoto->label;
        $this->takenAt = $clinicalPhoto->taken_at->toDateString();
        $this->photoNotes = (string) $clinicalPhoto->notes;
    }

    /**
     * Labels already used for this pet, offered as suggestions so the same
     * lesion keeps the same name from one photo to the next.
     *
     * @return list<string>
     */
    #[Computed]
    public function knownLabels(): array
    {
        return $this->pet->clinicalPhotos()->reorder()->orderBy('label')->distinct()->pluck('label')->all();
    }

    public function savePhoto(): void
    {
        $isEditing = $this->clinicalPhoto !== null;

        $validated = $this->validate([
            'label' => ['required', 'string', 'max:120'],
            'takenAt' => ['required', 'date', 'before_or_equal:today'],
            'photoNotes' => ['nullable', 'string', 'max:2000'],
            'photos' => $isEditing ? ['array'] : ['required', 'array', 'min:1', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [], [
            'label' => __('What is being followed'),
            'takenAt' => __('Date taken'),
            'photoNotes' => __('Notes'),
            'photos' => __('Photos'),
        ]);

        $attributes = [
            'label' => trim($validated['label']),
            'taken_at' => $validated['takenAt'],
            'notes' => $validated['photoNotes'] !== '' ? $validated['photoNotes'] : null,
        ];

        if ($isEditing) {
            $this->clinicalPhoto->update($attributes);
        } else {
            foreach ($this->photos as $photo) {
                $this->storePhoto($photo, $attributes);
            }
        }

        Flux::toast(
            variant: 'success',
            text: $isEditing ? __('Record updated successfully') : __('Record created successfully'),
        );

        $this->redirect(route('pets.show', ['pet' => $this->pet, 'tab' => 'health']), navigate: true);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function storePhoto(TemporaryUploadedFile $photo, array $attributes): void
    {
        $directory = 'clinical-photos/'.$this->pet->id;
        $imagePath = $photo->store($directory, 'local');
        $thumbnail = app(MakeImageThumbnail::class)->handle($photo->getRealPath());
        $thumbPath = null;

        if ($thumbnail !== null) {
            $thumbPath = $directory.'/thumbs/'.pathinfo($imagePath, PATHINFO_FILENAME).'.jpg';
            Storage::disk('local')->put($thumbPath, $thumbnail);
        }

        $this->pet->clinicalPhotos()->create([
            ...$attributes,
            'image_path' => $imagePath,
            'thumb_path' => $thumbPath,
            'file_size' => $photo->getSize(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.pets.clinical-photo-form')->title(
            ($this->clinicalPhoto !== null ? __('Edit Clinical Photo') : __('New Clinical Photo')).' — '.$this->pet->name,
        );
    }
}
