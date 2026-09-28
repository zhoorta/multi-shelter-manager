<x-mail::message>
# {{ __('Hello, :name!', ['name' => $notifiable->name]) }}

{{ __('The following treatments are due in the next 7 days:') }}

<x-mail::table>
| {{ __('Treatment') }} | {{ __('Next Due Date') }} | {{ __('Animals') }} |
| :--- | :--- | :--- |
@foreach ($treatmentRounds as $round)
| {{ $round['treatment'] }} | {{ $round['due_date'] }} | {{ trans_choice(':count animal|:count animals', count($round['pets']), ['count' => count($round['pets'])]) }}: {{ implode(', ', $round['pets']) }} |
@endforeach
</x-mail::table>

<x-mail::button :url="route('pets.treatments.group')">
{{ __('Group Treatment') }}
</x-mail::button>

@lang('Regards,')<br>
{{ config('app.name') }}
</x-mail::message>
