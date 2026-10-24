@component('mail::message')
    {!! $body !!}

    @include('emails.partials.email-preference-footer')
@endcomponent