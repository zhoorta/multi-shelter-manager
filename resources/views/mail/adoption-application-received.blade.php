<x-mail::message>
# {{ __('Hello, :name!', ['name' => $notifiable->name]) }}

{{ __(':applicant sent an application to adopt :pet (:ref).', ['applicant' => $application->name, 'pet' => $application->pet->name, 'ref' => $application->pet->ref]) }}

<x-mail::button :url="route('pets.applications.index')">
{{ __('View applications') }}
</x-mail::button>

@lang('Regards,')<br>
{{ config('app.name') }}
</x-mail::message>
