@component('mail::message')
# Song Published

"{{ $song->title }}" is now available to learn.

@component('mail::button', ['url' => the_tenant_route('songs.show', $song)])
View Song
@endcomponent

{!! $song->description !!}

{{ $tracking_pixel ?? '' }}

Enjoy!

@if($emailPreferenceLinks ?? false)
@include('emails.partials.email-preference-footer')
@endif
@endcomponent