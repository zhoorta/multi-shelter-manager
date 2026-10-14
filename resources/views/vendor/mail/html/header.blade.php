@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === config('app.name'))
<img src="{{ asset('apple-touch-icon.png') }}" class="logo" alt="" width="48" height="48" style="vertical-align: middle;">
<span style="display: inline-block; vertical-align: middle; margin-left: 12px; font-size: 22px; font-weight: bold; color: #1c1917;">{{ config('app.name') }}</span>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
