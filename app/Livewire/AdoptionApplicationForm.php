<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\AdoptionApplication;
use App\Models\Pet;
use App\Notifications\AdoptionApplicationReceived;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;

/**
 * Public adoption application for a pet published on the portal. Spam is
 * kept out without third parties or cookies: a honeypot field, a minimum
 * fill time, a per-IP rate limit and one pending application per e-mail
 * per pet. The applicant gets no e-mail, so the form cannot be used to send
 * mail to arbitrary addresses.
 */
class AdoptionApplicationForm extends Component
{
    /**
     * Seconds a person needs at least to fill in the form; faster
     * submissions are treated as bots.
     */
    public const MINIMUM_FILL_SECONDS = 3;

    /**
     * Applications accepted per IP address per hour.
     */
    public const MAX_APPLICATIONS_PER_HOUR = 3;

    #[Locked]
    public Pet $pet;

    #[Locked]
    public int $renderedAt = 0;

    public bool $isSubmitted = false;

    public string $applicantName = '';

    public string $applicantEmail = '';

    public string $applicantPhone = '';

    public string $applicantPostalCode = '';

    public string $applicantCity = '';

    public string $housingType = '';

    public bool $hasGarden = false;

    public bool $hasChildren = false;

    public string $otherAnimals = '';

    public string $message = '';

    public bool $hasConsented = false;

    /**
     * Honeypot: hidden from people, so only bots fill it in.
     */
    public string $website = '';

    public function mount(string $petRef): void
    {
        $this->pet = Pet::query()
            ->withoutGlobalScope('shelter')
            ->publishedToPortal()
            ->where('ref', $petRef)
            ->with(['species', 'breed', 'shelter', 'images'])
            ->firstOrFail();

        $this->renderedAt = now()->getTimestamp();
    }

    public function submitApplication(): void
    {
        if ($this->website !== '' || now()->getTimestamp() - $this->renderedAt < self::MINIMUM_FILL_SECONDS) {
            $this->isSubmitted = true;

            return;
        }

        $rateLimitKey = 'adoption-application:'.request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, self::MAX_APPLICATIONS_PER_HOUR)) {
            $this->addError('applicantEmail', __('Too many applications were sent from this connection. Please try again later.'));

            return;
        }

        $validated = $this->validate([
            'applicantName' => ['required', 'string', 'max:255'],
            'applicantEmail' => ['required', 'email', 'max:150'],
            'applicantPhone' => ['required', 'string', 'max:30'],
            'applicantPostalCode' => ['nullable', 'string', 'max:20'],
            'applicantCity' => ['required', 'string', 'max:100'],
            'housingType' => ['required', Rule::in(['apartment', 'house'])],
            'hasGarden' => ['boolean'],
            'hasChildren' => ['boolean'],
            'otherAnimals' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:3000'],
            'hasConsented' => ['accepted'],
        ], [], [
            'applicantName' => __('Name'),
            'applicantEmail' => __('Email'),
            'applicantPhone' => __('Phone'),
            'applicantPostalCode' => __('Postal Code'),
            'applicantCity' => __('City'),
            'housingType' => __('Housing Type'),
            'otherAnimals' => __('Other animals at home'),
            'message' => __('Why do you want to adopt?'),
            'hasConsented' => __('Consent'),
        ]);

        $pet = Pet::query()->withoutGlobalScope('shelter')->publishedToPortal()->whereKey($this->pet->id)->first();

        if ($pet === null) {
            $this->addError('message', __(':name is no longer available for adoption.', ['name' => $this->pet->name]));

            return;
        }

        $hasPendingApplication = $pet->adoptionApplications()
            ->where('email', $validated['applicantEmail'])
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingApplication) {
            $this->addError('applicantEmail', __('You have already applied to adopt :name. The shelter will contact you soon.', ['name' => $pet->name]));

            return;
        }

        RateLimiter::hit($rateLimitKey, 3600);

        $application = $pet->adoptionApplications()->create([
            'name' => $validated['applicantName'],
            'email' => $validated['applicantEmail'],
            'phone' => $validated['applicantPhone'],
            'postal_code' => $validated['applicantPostalCode'] !== '' ? $validated['applicantPostalCode'] : null,
            'city' => $validated['applicantCity'],
            'housing_type' => $validated['housingType'],
            'has_garden' => $validated['hasGarden'],
            'has_children' => $validated['hasChildren'],
            'other_animals' => $validated['otherAnimals'] !== '' ? $validated['otherAnimals'] : null,
            'message' => $validated['message'],
            'consent_at' => now(),
            'ip_address' => request()->ip(),
        ]);

        // Set by hand: a logged-in visitor from another shelter would not
        // resolve the pet through its shelter global scope.
        $application->setRelation('pet', $pet);

        $this->notifyShelter($application);

        $this->isSubmitted = true;
    }

    /**
     * E-mail the shelter's managers and staff who asked to be told about
     * new applications. Viewers never get them (they hold personal data).
     * A mail failure must not lose the application, which is already saved.
     */
    private function notifyShelter(AdoptionApplication $application): void
    {
        $recipients = $application->pet->shelter->users()
            ->wherePivot('adoption_application_notifications', true)
            ->wherePivotIn('role', ['manager', 'staff'])
            ->get();

        try {
            Notification::send($recipients, new AdoptionApplicationReceived($application));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    #[Layout('layouts::public')]
    public function render(): View
    {
        return view('livewire.adoption-application-form')
            ->title(__('Adopt :name', ['name' => $this->pet->name]).' - '.$this->pet->shelter->name)
            ->layoutData(['description' => __(':name is looking for a family!', ['name' => $this->pet->name])]);
    }
}
