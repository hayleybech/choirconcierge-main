<x-mail::message>
{!! new \Illuminate\Support\HtmlString($body) !!}

@include('emails.partials.email-preference-footer')
</x-mail::message>
