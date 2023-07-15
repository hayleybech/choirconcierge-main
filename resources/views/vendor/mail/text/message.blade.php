@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @if(tenant())
            @component('mail::header', ['url' => '//'.tenant('host')])
                {{ tenant('name') }}
            @endcomponent
        @else
            @component('mail::header', ['url' => config('app.url')])
                {{ config('app.name') }}
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
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
