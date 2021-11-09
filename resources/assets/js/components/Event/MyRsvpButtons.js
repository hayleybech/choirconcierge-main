import React from 'react';
import Button from "../inputs/Button";
import Icon from "../Icon";

const MyRsvpButtons = ({ event, size = 'sm' }) => (
    <div>
        {event.my_rsvp?.response !== 'yes' && (
            <Button
                href={event.my_rsvp.id ? route('events.rsvps.update', [event, event.my_rsvp]) : route('events.rsvps.store', event)}
                method={event.my_rsvp.id ? 'put' : 'post'}
                size={size}
                variant="success-outline"
                className="mr-2"
                data={{rsvp_response: 'yes'}}
            >
                <Icon icon="check" mr/>
                Going
            </Button>
        )}
        {event.my_rsvp?.response !== 'no' && (
            <Button
                href={event.my_rsvp.id ? route('events.rsvps.update', [event, event.my_rsvp]) : route('events.rsvps.store', event)}
                method={event.my_rsvp.id ? 'put' : 'post'}
                size={size}
                variant="danger-outline"
                data={{rsvp_response: 'no'}}
            >
                <Icon icon="times" mr/>
                Not Going
            </Button>
        )}
    </div>
);

export default MyRsvpButtons;