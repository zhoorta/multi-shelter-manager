<?php

declare(strict_types=1);

namespace App\Livewire\Volunteers;

use App\Models\Volunteer;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class VolunteerForm extends Component
{
    use WithFileUploads;

    public ?Volunteer $volunteer = null;

    public string $volunteerName = '';

    public string $volunteerGender = '';

    public string $volunteerIdCard = '';

    public string $volunteerTin = '';

    public string $volunteerBirthDate = '';

    public ?string $existingImagePath = null;

    public mixed $volunteerImage = null;

    public string $volunteerProfessionalActivity = '';

    public string $volunteerEmail = '';

    public string $volunteerPhone = '';

    public string $volunteerAddress = '';

    public string $volunteerPostalCode = '';

    public string $volunteerCity = '';

    public string $volunteerTransportMode = '';

    public string $volunteerAttendanceEvaluation = '';

    public string $volunteerPerformanceEvaluation = '';

    public string $volunteerStartDate = '';

    public string $volunteerEndDate = '';

    public bool $volunteerSendNewsletter = false;

    public string $volunteerNotes = '';

    public function mount(?Volunteer $volunteer = null): void
    {
        abort_unless(Auth::user()->role === 'manager', 403);

        if ($volunteer === null) {
            return;
        }

        $this->volunteer = $volunteer;
        $this->volunteerName = $volunteer->name;
        $this->volunteerGender = $volunteer->gender;
        $this->volunteerIdCard = (string) $volunteer->id_card;
        $this->volunteerTin = (string) $volunteer->tin;
        $this->volunteerBirthDate = (string) $volunteer->birth_date?->format('Y-m-d');
        $this->existingImagePath = $volunteer->image_path;
        $this->volunteerProfessionalActivity = (string) $volunteer->professional_activity;
        $this->volunteerEmail = (string) $volunteer->email;
        $this->volunteerPhone = (string) $volunteer->phone;
        $this->volunteerAddress = (string) $volunteer->address;
        $this->volunteerPostalCode = (string) $volunteer->postal_code;
        $this->volunteerCity = (string) $volunteer->city;
        $this->volunteerTransportMode = (string) $volunteer->transport_mode;
        $this->volunteerAttendanceEvaluation = (string) $volunteer->attendance_evaluation;
        $this->volunteerPerformanceEvaluation = (string) $volunteer->performance_evaluation;
        $this->volunteerStartDate = (string) $volunteer->start_date?->format('Y-m-d');
        $this->volunteerEndDate = (string) $volunteer->end_date?->format('Y-m-d');
        $this->volunteerSendNewsletter = $volunteer->send_newsletter;
        $this->volunteerNotes = (string) $volunteer->notes;
    }

    public function saveVolunteer(): void
    {
        $validated = $this->validate([
            'volunteerName' => ['required', 'string', 'max:255'],
            'volunteerGender' => ['required', 'in:male,female'],
            'volunteerIdCard' => ['nullable', 'string', 'max:255'],
            'volunteerTin' => ['nullable', 'string', 'max:255'],
            'volunteerBirthDate' => ['nullable', 'date'],
            'volunteerImage' => ['nullable', 'image', 'max:2048'],
            'volunteerProfessionalActivity' => ['nullable', 'string', 'max:255'],
            'volunteerEmail' => ['nullable', 'string', 'email', 'max:150'],
            'volunteerPhone' => ['nullable', 'string', 'max:30'],
            'volunteerAddress' => ['nullable', 'string', 'max:255'],
            'volunteerPostalCode' => ['nullable', 'string', 'max:20'],
            'volunteerCity' => ['nullable', 'string', 'max:100'],
            'volunteerTransportMode' => ['nullable', 'in:foot,bycicle,hitchhike,public transportation,own vehicule'],
            'volunteerAttendanceEvaluation' => ['nullable', 'in:very low,low,regular,high,very high,excellent'],
            'volunteerPerformanceEvaluation' => ['nullable', 'in:very low,low,regular,high,very high,excellent'],
            'volunteerStartDate' => ['nullable', 'date'],
            'volunteerEndDate' => ['nullable', 'date', 'after_or_equal:volunteerStartDate'],
            'volunteerSendNewsletter' => ['boolean'],
            'volunteerNotes' => ['nullable', 'string'],
        ], [], [
            'volunteerName' => __('Name'),
            'volunteerGender' => __('Gender'),
            'volunteerIdCard' => __('ID Card'),
            'volunteerTin' => __('TIN'),
            'volunteerBirthDate' => __('Birth Date'),
            'volunteerImage' => __('Photo'),
            'volunteerProfessionalActivity' => __('Professional Activity'),
            'volunteerEmail' => __('Email'),
            'volunteerPhone' => __('Phone'),
            'volunteerAddress' => __('Address'),
            'volunteerPostalCode' => __('Postal Code'),
            'volunteerCity' => __('City'),
            'volunteerTransportMode' => __('Transport Mode'),
            'volunteerAttendanceEvaluation' => __('Attendance Evaluation'),
            'volunteerPerformanceEvaluation' => __('Performance Evaluation'),
            'volunteerStartDate' => __('Start Date'),
            'volunteerEndDate' => __('End Date'),
            'volunteerSendNewsletter' => __('Send Newsletter'),
            'volunteerNotes' => __('Notes'),
        ]);

        $attributes = [
            'name' => $validated['volunteerName'],
            'gender' => $validated['volunteerGender'],
            'id_card' => $validated['volunteerIdCard'] !== '' ? $validated['volunteerIdCard'] : null,
            'tin' => $validated['volunteerTin'] !== '' ? $validated['volunteerTin'] : null,
            'birth_date' => $validated['volunteerBirthDate'] !== '' ? $validated['volunteerBirthDate'] : null,
            'professional_activity' => $validated['volunteerProfessionalActivity'] !== '' ? $validated['volunteerProfessionalActivity'] : null,
            'email' => $validated['volunteerEmail'] !== '' ? $validated['volunteerEmail'] : null,
            'phone' => $validated['volunteerPhone'] !== '' ? $validated['volunteerPhone'] : null,
            'address' => $validated['volunteerAddress'] !== '' ? $validated['volunteerAddress'] : null,
            'postal_code' => $validated['volunteerPostalCode'] !== '' ? $validated['volunteerPostalCode'] : null,
            'city' => $validated['volunteerCity'] !== '' ? $validated['volunteerCity'] : null,
            'transport_mode' => $validated['volunteerTransportMode'] !== '' ? $validated['volunteerTransportMode'] : null,
            'attendance_evaluation' => $validated['volunteerAttendanceEvaluation'] !== '' ? $validated['volunteerAttendanceEvaluation'] : null,
            'performance_evaluation' => $validated['volunteerPerformanceEvaluation'] !== '' ? $validated['volunteerPerformanceEvaluation'] : null,
            'start_date' => $validated['volunteerStartDate'] !== '' ? $validated['volunteerStartDate'] : null,
            'end_date' => $validated['volunteerEndDate'] !== '' ? $validated['volunteerEndDate'] : null,
            'send_newsletter' => $validated['volunteerSendNewsletter'],
            'notes' => $validated['volunteerNotes'] !== '' ? $validated['volunteerNotes'] : null,
        ];

        if ($this->volunteerImage !== null) {
            if ($this->existingImagePath !== null) {
                Storage::disk('public')->delete($this->existingImagePath);
            }

            $attributes['image_path'] = $this->volunteerImage->store('volunteers', 'public');
        }

        $isEditing = $this->volunteer !== null;

        if ($isEditing) {
            $this->volunteer->update($attributes);
        } else {
            $this->volunteer = Volunteer::query()->create($attributes);
        }

        Flux::toast(
            variant: 'success',
            text: $isEditing ? __('Record updated successfully') : __('Record created successfully'),
        );

        $this->redirect(route('volunteers.show', $this->volunteer), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.volunteers.volunteer-form')->title(
            $this->volunteer !== null ? __('Edit').' — '.$this->volunteer->name : __('Create').' — '.__('Volunteers'),
        );
    }
}
