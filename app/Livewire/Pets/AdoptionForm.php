<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Pet;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdoptionForm extends Component
{
    public Pet $pet;

    public string $adopterName = '';

    public string $adopterEmail = '';

    public string $adopterPhone = '';

    public string $adopterAddress = '';

    public string $adopterPostalCode = '';

    public string $adopterCity = '';

    public string $adoptionDate = '';

    public string $adoptionFee = '0.00';

    public string $adoptionNotes = '';

    public string $applicationStatus = 'Approved';

    public function mount(Pet $pet): void
    {
        abort_unless(in_array(Auth::user()->role, ['manager', 'staff'], true), 403);
        abort_if($pet->status === 'adopted', 403);

        $this->pet = $pet;
        $this->adoptionDate = now()->toDateString();
    }

    public function saveAdoption(): void
    {
        $validated = $this->validate([
            'adopterName' => ['required', 'string', 'max:255'],
            'adopterEmail' => ['nullable', 'email', 'max:150'],
            'adopterPhone' => ['nullable', 'string', 'max:30'],
            'adopterAddress' => ['nullable', 'string', 'max:255'],
            'adopterPostalCode' => ['nullable', 'string', 'max:20'],
            'adopterCity' => ['nullable', 'string', 'max:100'],
            'adoptionDate' => ['required', 'date'],
            'adoptionFee' => ['nullable', 'numeric', 'min:0'],
            'adoptionNotes' => ['nullable', 'string'],
            'applicationStatus' => ['required', 'in:Pending,Approved,Rejected'],
        ], [], [
            'adopterName' => __('Name'),
            'adopterEmail' => __('Email'),
            'adopterPhone' => __('Phone'),
            'adopterAddress' => __('Address'),
            'adopterPostalCode' => __('Postal Code'),
            'adopterCity' => __('City'),
            'adoptionDate' => __('Adoption Date'),
            'adoptionFee' => __('Adoption Fee'),
            'adoptionNotes' => __('Notes'),
            'applicationStatus' => __('Application Status'),
        ]);

        DB::transaction(function () use ($validated): void {
            $this->pet->adoptions()->create([
                'name' => $validated['adopterName'],
                'email' => $validated['adopterEmail'] !== '' ? $validated['adopterEmail'] : null,
                'phone' => $validated['adopterPhone'] !== '' ? $validated['adopterPhone'] : null,
                'address' => $validated['adopterAddress'] !== '' ? $validated['adopterAddress'] : null,
                'postal_code' => $validated['adopterPostalCode'] !== '' ? $validated['adopterPostalCode'] : null,
                'city' => $validated['adopterCity'] !== '' ? $validated['adopterCity'] : null,
                'adoption_date' => $validated['adoptionDate'],
                'adoption_fee' => $validated['adoptionFee'] !== '' ? $validated['adoptionFee'] : 0,
                'notes' => $validated['adoptionNotes'] !== '' ? $validated['adoptionNotes'] : null,
                'application_status' => $validated['applicationStatus'],
            ]);

            $this->pet->update([
                'status' => 'adopted',
                'checkout_date' => $validated['adoptionDate'],
            ]);
        });

        Flux::toast(
            variant: 'success',
            text: __('Record created successfully'),
        );

        $this->redirect(route('pets.show', $this->pet), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.pets.adoption-form')->title(__('Adoption Registration').' — '.$this->pet->name);
    }
}
