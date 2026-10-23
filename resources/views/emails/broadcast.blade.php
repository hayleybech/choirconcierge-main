@component('mail::message', ['includeLetterhead' => $includeLetterhead])
    {!! $body !!}

    @include('emails.partials.email-preference-footer')
@endcomponent