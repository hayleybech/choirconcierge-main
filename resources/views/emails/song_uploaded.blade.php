@component('mail::message')
# New song uploaded!

A new song, "{{ $song->title }}", has been uploaded.

@component('mail::button', ['url' => the_tenant_route('songs.show', $song)])
View Song
@endcomponent

{!! $song->description !!}

{{ $tracking_pixel ?? '' }}

Enjoy!
@endcomponent
