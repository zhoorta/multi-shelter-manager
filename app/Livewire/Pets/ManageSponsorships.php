<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Actions\ExportShelterData;
use App\Models\Sponsorship;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('Manage Sponsorships')]
class ManageSponsorships extends Component
{
    use WithPagination;

    public string $search = '';

    public function mount(): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_if(Auth::user()->isViewerOfCurrentShelter(), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, Sponsorship>
     */
    #[Computed]
    public function sponsorships(): LengthAwarePaginator
    {
        $search = trim($this->search);

        return Sponsorship::query()
            // whereHas('pet') relies on Pet's MultiShelterTrait global scope
            // to keep this scoped to the acting user's shelter, since
            // Sponsorship itself has no shelter_id (see .ai/rules/pets.md).
            ->whereHas('pet')
            ->with(['pet.species', 'pet.images', 'payments'])
            ->when(
                $search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('notes', 'like', '%'.$search.'%')
                        ->orWhereHas('pet', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%')
                            ->orWhere('ref', 'like', '%'.$search.'%'))
                ),
            )
            ->latest()
            ->paginate(20);
    }

    public function deleteSponsorship(int $sponsorshipId): void
    {
        abort_unless(Auth::user()->canEditArea('sponsorships'), 403);
        Sponsorship::query()->whereHas('pet')->findOrFail($sponsorshipId)->delete();

        unset($this->sponsorships);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    /**
     * Download every sponsorship of the shelter as a spreadsheet for the
     * accounting team. "Sponsorship ID" is the Focinhos id (never reused, as
     * deleted sponsorships are only soft-deleted). Shelters that imported from
     * Portugal Zoófilo (previous_id filled) also get the previous id column;
     * others never see it. The last payment ignores the
     * zero-value placeholders the import creates.
     */
    public function exportXlsx(): StreamedResponse
    {
        abort_if(Auth::user()->isViewerOfCurrentShelter(), 403);

        $sponsorships = Sponsorship::query()
            ->whereHas('pet')
            ->with(['pet', 'payments'])
            ->orderBy('id')
            ->get();
        $hasPreviousIds = $sponsorships->whereNotNull('previous_id')->isNotEmpty();

        $xlsx = ExportShelterData::xlsx(
            [
                __('Pet'),
                __('Pet Number'),
                __('Microchip / Chip'),
                __('Sponsor'),
                __('Sponsorship ID'),
                ...($hasPreviousIds ? [__('Previous PZ Sponsorship ID')] : []),
                __('Last Payment Date'),
                __('Last Payment Value'),
                __('Valid Until'),
                __('Payment Frequency'),
                __('Email'),
                __('Phone'),
            ],
            $sponsorships->map(function (Sponsorship $sponsorship) use ($hasPreviousIds): array {
                $lastPayment = $sponsorship->payments
                    ->filter(fn ($payment): bool => (float) $payment->payment_value > 0)
                    ->sortByDesc('payment_date')
                    ->first();

                return [
                    $sponsorship->pet->name,
                    $sponsorship->pet->ref,
                    $sponsorship->pet->chip,
                    $sponsorship->name,
                    $sponsorship->id,
                    ...($hasPreviousIds ? [$sponsorship->previous_id] : []),
                    $lastPayment?->payment_date->format('d/m/Y'),
                    $lastPayment !== null ? number_format((float) $lastPayment->payment_value, 2, ',', '') : null,
                    $sponsorship->payments->max('end_date')?->format('d/m/Y'),
                    $sponsorship->payment_frequency !== null ? __($sponsorship->payment_frequency) : null,
                    $sponsorship->email,
                    $sponsorship->phone,
                ];
            })->all(),
        );

        return response()->streamDownload(
            fn () => print ($xlsx),
            Str::slug(__('Sponsorships')).'.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        );
    }

    public function render(): View
    {
        return view('livewire.pets.manage-sponsorships');
    }
}
