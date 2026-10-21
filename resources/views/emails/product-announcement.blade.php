<x-mail::message>
{!! new \Illuminate\Support\HtmlString($body) !!}

@if($emailPreferenceLinks)
@include('emails.partials.email-preference-footer')
@endif
</x-mail::message>
