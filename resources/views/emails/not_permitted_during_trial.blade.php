@component('mail::message')
# Emails are restricted during trial periods

The mailing list "{{ $group->title }}" exists, but you are not currently permitted to send emails to it.

To protect against spam, mailing lists do not accept real emails during your choir's trial period.

If you think this rejection was sent in error, or if you need to test real emails prior to purchase, please [contact us](https://www.choirconcierge.com).

@endcomponent
