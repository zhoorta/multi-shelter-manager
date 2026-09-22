<?php

declare(strict_types=1);

namespace App\Livewire\Pets;

use App\Models\Pet;
use App\Models\Sponsorship;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SponsorshipForm extends Component
{
    public Pet $pet;

    public ?Sponsorship $sponsorship = null;

    public string $sponsorName = '';

    public string $sponsorEmail = '';

    public string $sponsorPhone = '';

    public string $sponsorAddress = '';

    public string $sponsorPostalCode = '';

    public string $sponsorCity = '';

    public bool $sendFeedback = true;

    public bool $sendNewsletter = false;

    public string $sponsorshipNotes = '';

    public function mount(Pet $pet, ?Sponsorship $sponsorship = null): void
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_unless($pet->is_sponsorable, 403);
        abort_if($sponsorship !== null && $sponsorship->pet_id !== $pet->id, 404);

        $this->pet = $pet;
        $this->sponsorship = $sponsorship;

        if ($sponsorship === null) {
            return;
        }

        $this->sponsorName = (string) $sponsorship->name;
        $this->sponsorEmail = (string) $sponsorship->email;
        $this->sponsorPhone = (string) $sponsorship->phone;
        $this->sponsorAddress = (string) $sponsorship->address;
        $this->sponsorPostalCode = (string) $sponsorship->postal_code;
        $this->sponsorCity = (string) $sponsorship->city;
        $this->sendFeedback = $sponsorship->send_feedback;
        $this->sendNewsletter = $sponsorship->send_newsletter;
        $this->sponsorshipNotes = (string) $sponsorship->notes;
    }

    public function saveSponsorship(): void
    {
        $validated = $this->validate([
            'sponsorName' => ['required', 'string', 'max:255'],
            'sponsorEmail' => ['nullable', 'email', 'max:150'],
            'sponsorPhone' => ['nullable', 'string', 'max:30'],
            'sponsorAddress' => ['nullable', 'string', 'max:255'],
            'sponsorPostalCode' => ['nullable', 'string', 'max:20'],
            'sponsorCity' => ['nullable', 'string', 'max:100'],
            'sendFeedback' => ['boolean'],
            'sendNewsletter' => ['boolean'],
            'sponsorshipNotes' => ['nullable', 'string'],
        ], [], [
            'sponsorName' => __('Name'),
            'sponsorEmail' => __('Email'),
            'sponsorPhone' => __('Phone'),
            'sponsorAddress' => __('Address'),
            'sponsorPostalCode' => __('Postal Code'),
            'sponsorCity' => __('City'),
            'sponsorshipNotes' => __('Notes'),
        ]);

        $sponsorshipAttributes = [
            'name' => $validated['sponsorName'],
            'email' => $validated['sponsorEmail'] !== '' ? $validated['sponsorEmail'] : null,
            'phone' => $validated['sponsorPhone'] !== '' ? $validated['sponsorPhone'] : null,
            'address' => $validated['sponsorAddress'] !== '' ? $validated['sponsorAddress'] : null,
            'postal_code' => $validated['sponsorPostalCode'] !== '' ? $validated['sponsorPostalCode'] : null,
            'city' => $validated['sponsorCity'] !== '' ? $validated['sponsorCity'] : null,
            'send_feedback' => $validated['sendFeedback'],
            'send_newsletter' => $validated['sendNewsletter'],
            'notes' => $validated['sponsorshipNotes'] !== '' ? $validated['sponsorshipNotes'] : null,
        ];

        $isEditing = $this->sponsorship !== null;

        if ($isEditing) {
            $this->sponsorship->update($sponsorshipAttributes);
        } else {
            $this->pet->sponsorships()->create($sponsorshipAttributes);
        }

        Flux::toast(
            variant: 'success',
            text: $isEditing ? __('Record updated successfully') : __('Record created successfully'),
        );

        $this->redirect(route('pets.show', $this->pet), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.pets.sponsorship-form')->title(
            ($this->sponsorship !== null ? __('Edit Sponsorship') : __('Sponsorship Registration')).' — '.$this->pet->name,
        );
    }
}
