@component('mail::message')
# Updated song

The song, "{{ $song->title }}", has recently been modified.

@if($message)
Message:

{{ $message }}

@endif
@component('mail::button', ['url' => the_tenant_route('songs.show', $song)])
View Song
@endcomponent

{!! $song->description !!}

{{ $tracking_pixel ?? '' }}

Enjoy!
@endcomponent
