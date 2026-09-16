<x-mail::message>
# {{ __('Hello, :name!', ['name' => $notifiable->name]) }}

{{ __('The following pets have vaccinations due in the next 7 days:') }}

<x-mail::table>
| {{ __('Pet') }} | {{ __('Vaccine') }} | {{ __('Due Date') }} |
| :--- | :--- | :--- |
@foreach ($dueVaccinations as $petVaccine)
| [{{ $petVaccine->pet->name }} ({{ $petVaccine->pet->ref }})]({{ route('pets.show', $petVaccine->pet) }}) | {{ $petVaccine->vaccine->name }} | {{ $petVaccine->due_date->format('d/m/Y') }} |
@endforeach
</x-mail::table>

@lang('Regards,')<br>
{{ config('app.name') }}
</x-mail::message>
