<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Adoption;
use App\Models\Pet;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdoptionForm extends Component
{
    public Pet $pet;

    public ?Adoption $adoption = null;

    public string $adopterName = '';

    public string $adopterEmail = '';

    public string $adopterPhone = '';

    public string $adopterAddress = '';

    public string $adopterPostalCode = '';

    public string $adopterCity = '';

    public string $adoptionDate = '';

    public string $returnDate = '';

    public string $adoptionFee = '0.00';

    public string $adoptionNotes = '';

    public string $applicationStatus = 'Approved';

    public string $backRoute = '';

    public string $backLabel = '';

    public function mount(Pet $pet, ?Adoption $adoption = null): void
    {
        abort_unless(in_array(Auth::user()->role, ['manager', 'staff'], true), 403);
        abort_if($adoption === null && $pet->status === 'adopted', 403);
        abort_if($adoption !== null && $adoption->pet_id !== $pet->id, 404);

        $this->pet = $pet;
        $this->adoption = $adoption;

        $fromAdoptionsList = $adoption !== null && request()->query('from') === 'adoptions';
        $this->backRoute = $fromAdoptionsList ? route('pets.adopt.show', [$pet, $adoption]) : route('pets.show', $pet);
        $this->backLabel = $fromAdoptionsList
            ? ((string) $adoption->name !== '' ? $adoption->name : __('Adoption'))
            : $pet->name;

        if ($adoption === null) {
            $this->adoptionDate = now()->toDateString();

            return;
        }

        $this->adopterName = (string) $adoption->name;
        $this->adopterEmail = (string) $adoption->email;
        $this->adopterPhone = (string) $adoption->phone;
        $this->adopterAddress = (string) $adoption->address;
        $this->adopterPostalCode = (string) $adoption->postal_code;
        $this->adopterCity = (string) $adoption->city;
        $this->adoptionDate = $adoption->adoption_date->toDateString();
        $this->returnDate = (string) $adoption->return_date?->toDateString();
        $this->adoptionFee = (string) $adoption->adoption_fee;
        $this->adoptionNotes = (string) $adoption->notes;
        $this->applicationStatus = $adoption->application_status;
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
            'returnDate' => ['nullable', 'date', 'after_or_equal:adoptionDate'],
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
            'returnDate' => __('Return Date'),
            'adoptionFee' => __('Adoption Fee'),
            'adoptionNotes' => __('Notes'),
            'applicationStatus' => __('Application Status'),
        ]);

        $isEditing = $this->adoption !== null;

        $hasAnotherOpenAdoption = $isEditing && $this->pet->adoptions()
            ->whereKeyNot($this->adoption->id)
            ->whereNull('return_date')
            ->exists();

        if ($isEditing && $this->adoption->return_date !== null && $validated['returnDate'] === '' && $hasAnotherOpenAdoption) {
            $this->addError(
                'returnDate',
                __('Cannot clear the return date because the pet has already been adopted again since then.'),
            );

            return;
        }

        DB::transaction(function () use ($validated, $isEditing, $hasAnotherOpenAdoption): void {
            $adoptionAttributes = [
                'name' => $validated['adopterName'],
                'email' => $validated['adopterEmail'] !== '' ? $validated['adopterEmail'] : null,
                'phone' => $validated['adopterPhone'] !== '' ? $validated['adopterPhone'] : null,
                'address' => $validated['adopterAddress'] !== '' ? $validated['adopterAddress'] : null,
                'postal_code' => $validated['adopterPostalCode'] !== '' ? $validated['adopterPostalCode'] : null,
                'city' => $validated['adopterCity'] !== '' ? $validated['adopterCity'] : null,
                'adoption_date' => $validated['adoptionDate'],
                'return_date' => $validated['returnDate'] !== '' ? $validated['returnDate'] : null,
                'adoption_fee' => $validated['adoptionFee'] !== '' ? $validated['adoptionFee'] : 0,
                'notes' => $validated['adoptionNotes'] !== '' ? $validated['adoptionNotes'] : null,
                'application_status' => $validated['applicationStatus'],
            ];

            if ($isEditing) {
                $this->adoption->update($adoptionAttributes);
            } else {
                $this->pet->adoptions()->create($adoptionAttributes);
            }

            if ($hasAnotherOpenAdoption) {
                return;
            }

            $isReturned = $adoptionAttributes['return_date'] !== null;

            $this->pet->update([
                'status' => $isReturned ? 'available' : 'adopted',
                'checkout_date' => $isReturned ? null : $validated['adoptionDate'],
            ]);
        });

        Flux::toast(
            variant: 'success',
            text: $isEditing ? __('Record updated successfully') : __('Record created successfully'),
        );

        $this->redirect(route('pets.show', $this->pet), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.pets.adoption-form')->title(
            ($this->adoption !== null ? __('Edit Adoption') : __('Adoption Registration')).' — '.$this->pet->name,
        );
    }
}
