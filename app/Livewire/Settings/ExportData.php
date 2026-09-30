<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Actions\ExportShelterData;
use App\Models\DataExport;
use App\Models\Shelter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Lets a manager download all the data of their current shelter as a ZIP of
 * CSV files. Built on request and streamed, so nothing stays on disk.
 */
#[Title('Export data')]
class ExportData extends Component
{
    #[Locked]
    public int $shelterId;

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user->isManagerOfCurrentShelter(), 403);

        $this->shelterId = $user->current_shelter_id;
    }

    public function export(ExportShelterData $exporter): BinaryFileResponse
    {
        $user = Auth::user();

        abort_unless($user->isManagerOf($this->shelterId), 403);

        $shelter = Shelter::query()->findOrFail($this->shelterId);

        DataExport::create(['shelter_id' => $shelter->id, 'user_id' => $user->id, 'ip_address' => request()->ip()]);

        $filename = 'focinhos-'.Str::slug($shelter->name).'-'.now()->format('Y-m-d').'.zip';

        return response()->download($exporter->handle($shelter), $filename)->deleteFileAfterSend();
    }

    /**
     * The latest downloads of this shelter's data, for the audit list.
     *
     * @return Collection<int, DataExport>
     */
    #[Computed]
    public function recentExports(): Collection
    {
        return DataExport::query()->with('user')->where('shelter_id', $this->shelterId)->latest()->limit(10)->get();
    }

    public function render(): View
    {
        return view('livewire.settings.export-data');
    }
}
