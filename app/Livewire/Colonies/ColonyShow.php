<?php

declare(strict_types=1);

namespace App\Livewire\Colonies;

use App\Actions\ExportShelterData;
use App\Models\Colony;
use App\Models\Pet;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('Colony')]
class ColonyShow extends Component
{
    public Colony $colony;

    public string $search = '';

    public string $neuteringFilter = '';

    public function mount(Colony $colony): void
    {
        abort_unless(! Auth::user()->is_admin, 403);

        $this->colony = $colony;
    }

    /**
     * Living animals of the colony, newest capture first.
     *
     * @return Builder<Pet>
     */
    protected function livingAnimals(): Builder
    {
        return Pet::query()
            ->where('colony_id', $this->colony->id)
            ->whereNull('date_of_death')
            ->orderByDesc('captured_at')
            ->orderBy('name');
    }

    /**
     * The colony's animals matching the search and the neutering filter.
     *
     * @return Collection<int, Pet>
     */
    #[Computed]
    public function cats(): Collection
    {
        $search = trim($this->search);

        return $this->livingAnimals()
            ->when(
                $search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('ref', 'like', '%'.$search.'%')
                        ->orWhere('chip', 'like', '%'.$search.'%'),
                ),
            )
            ->when($this->neuteringFilter === 'neutered', fn (Builder $query) => $query->where('is_neutered', true))
            ->when($this->neuteringFilter === 'pending', fn (Builder $query) => $query->where('is_neutered', false)->whereNotNull('neutering_status'))
            ->when($this->neuteringFilter === 'unknown', fn (Builder $query) => $query->where('is_neutered', false)->whereNull('neutering_status'))
            ->when($this->neuteringFilter === 'ear_tipped', fn (Builder $query) => $query->where('is_ear_tipped', true))
            ->get();
    }

    /**
     * Totals of the whole colony, whatever the filters show.
     *
     * @return array{total: int, neutered: int, pending: int, unknown: int, earTipped: int}
     */
    #[Computed]
    public function totals(): array
    {
        $animals = $this->livingAnimals()->get(['id', 'is_neutered', 'neutering_status', 'is_ear_tipped']);
        $neutered = $animals->where('is_neutered', true)->count();
        $unknown = $animals->where('is_neutered', false)->whereNull('neutering_status')->count();

        return [
            'total' => $animals->count(),
            'neutered' => $neutered,
            'pending' => $animals->count() - $neutered - $unknown,
            'unknown' => $unknown,
            'earTipped' => $animals->where('is_ear_tipped', true)->count(),
        ];
    }

    /**
     * Spreadsheet of the colony's cats for the CED/CER reports.
     */
    public function exportCsv(): StreamedResponse
    {
        $headings = [
            __('Colony'), __('Reference'), __('Name'), __('Gender'), __('Microchip / Chip'), __('Birth Date'),
            __('Captured on'), __('Is Neutered'), __('Neutering Date'), __('Ear-tipped'), __('Returned on'),
        ];

        $rows = $this->livingAnimals()->get()->map(fn (Pet $pet): array => [
            $this->colony->name,
            $pet->ref,
            $pet->name,
            $pet->gender === 'male' ? __('Male') : __('Female'),
            $pet->chip,
            $pet->birth_date?->format('d/m/Y'),
            $pet->captured_at?->format('d/m/Y'),
            $pet->is_neutered ? __('Yes') : __('No'),
            $pet->neutered_at?->format('d/m/Y'),
            $pet->is_ear_tipped ? __('Yes') : __('No'),
            $pet->returned_at?->format('d/m/Y'),
        ]);

        $csv = ExportShelterData::csv($headings, $rows);

        return response()->streamDownload(
            fn () => print ($csv),
            Str::slug(__('Colony').' '.$this->colony->name.' '.now()->format('Y-m-d')).'.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8'],
        );
    }

    public function render(): View
    {
        return view('livewire.colonies.colony-show');
    }
}
