@component('mail::message')
Hi {{ $owner->name }},

I hope you've been enjoying your trial of Choir Concierge over the past few weeks. With just 5 days left, I wanted to check in one last time.

How has your experience been? Did you get a chance to try out all the features you were interested in? If there's anything you haven't explored yet or if you've encountered any hurdles along the way, I'd love to help.

@unless($hadDemo)
I'm offering a chance to book a quick demo with me. It's an opportunity for us to:
- Address any questions or concerns you might have
- Explore any features you're curious about
- Discuss your overall experience with Choir Concierge
@endunless

Your feedback is incredibly valuable, whether you're planning to continue with us or not. If you're considering not continuing, I'd be especially grateful to hear why. Your insights help us improve Choir Concierge for everyone.

@unless($hadDemo)
To book a session, [just click here](https://calendly.com/hayleybech/30min).
@endunless

Thank you for giving Choir Concierge a try. I'm looking forward to hearing your thoughts!

Warm regards,

Hayley Bech\
Choir Concierge

@unless($hadDemo)
P.S. Even if you prefer not to book a demo, feel free to reply to this email with any feedback or questions. I'm always here to help!
@endunless

@endcomponent
