<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Livewire\Pets\Concerns\ManagesSponsorshipPayments;
use App\Models\Pet;
use App\Models\Sponsorship;
use App\Models\SponsorshipPayment;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SponsorshipShow extends Component
{
    use ManagesSponsorshipPayments;

    public Pet $pet;

    public Sponsorship $sponsorship;

    public function mount(Pet $pet, Sponsorship $sponsorship): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_if($sponsorship->pet_id !== $pet->id, 404);

        $this->pet = $pet;
        $this->sponsorship = $sponsorship->load(['payments' => fn ($query) => $query->orderByDesc('payment_date')]);
    }

    /**
     * The sponsorship has no shelter_id of its own, so scope it to the
     * single sponsorship this page is for — it is already resolved through
     * route model binding and validated in mount() (see
     * .ai/rules/livewire-pets.md).
     */
    protected function scopedSponsorshipQuery(): Builder
    {
        return Sponsorship::query()->whereKey($this->sponsorship->id);
    }

    protected function scopedSponsorshipPaymentQuery(): Builder
    {
        return SponsorshipPayment::query()->where('sponsorship_id', $this->sponsorship->id);
    }

    protected function refreshSponsorships(): void
    {
        $this->sponsorship->load(['payments' => fn ($query) => $query->orderByDesc('payment_date')]);
    }

    public function render(): View
    {
        return view('livewire.pets.sponsorship-show')->title(__('Sponsorship').' — '.$this->pet->name);
    }
}
