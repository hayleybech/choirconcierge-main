@component('mail::message')
# You are not a permitted sender

The mailing list "{{ $group->title }}" exists, but you are not currently permitted to send emails to it.

If you think you should be permitted for this list, please contact the choir's site admin.

@endcomponent
