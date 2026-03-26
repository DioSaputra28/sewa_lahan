@props(['url'])
@php
    $siteName = get_site_name();
    $siteLogoUrl = get_site_logo_url();
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (filled($siteLogoUrl))
<img src="{{ $siteLogoUrl }}" class="logo" alt="{{ $siteName }} Logo">
@endif
<div class="brand-name">{{ $siteName }}</div>
</a>
</td>
</tr>
