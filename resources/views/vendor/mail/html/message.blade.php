@component('mail::layout')
{{-- Header --}}
@slot('header')
@if(tenant())
@component('mail::header', ['url' => '//'.tenant('host')])
@if(tenant('logo_url'))
<img src="{{ tenant('logo_url') }}" alt="{{ tenant('choir_name') ?? 'Choir Name' }}">
@else
{{ tenant('choir_name') }}
@endif
@endcomponent
@else
@component('mail::header', ['url' => config('app.url')])
<img src="{{ global_asset('/img/vibrant/logo-dark.svg') }}" alt="Choir Concierge" height="50"><br>
@endcomponent
@endif
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
<img src="{{ global_asset('/img/vibrant/logo-dark.svg') }}" alt="Choir Concierge" height="30"><br>
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
