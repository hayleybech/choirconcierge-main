@component('mail::message')
# Welcome!

Choir Concierge is the system we used to manage the choir's day-to-day operations. Your new account will give you access to many things!

To get started, click the button below to set a password for your account.

@component('mail::button', ['url' => $url])
Set Password
@endcomponent

@endcomponent
