@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === config('app.name'))
<img src="{{ asset('apple-touch-icon.png') }}" class="logo" alt="{{ config('app.name') }}" width="64" height="64">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
