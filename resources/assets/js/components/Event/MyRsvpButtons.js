import React from 'react';
import Button from "../inputs/Button";
import Icon from "../Icon";
import useRoute from "../../hooks/useRoute";

const MyRsvpButtons = ({ event, size = 'sm' }) => {
    const { route } = useRoute();

    return (
        <div>
            {event.my_rsvp?.response !== 'yes' && (
                <Button
                    href={event.my_rsvp.id
                        ? route('events.rsvps.update', {tenant: event.tenant_id, event, rsvp: event.my_rsvp})
                        : route('events.rsvps.store', {tenant: event.tenant_id, event})}
                    method={event.my_rsvp.id ? 'put' : 'post'}
                    size={size}
                    variant="success-outline"
                    className="mr-2"
                    data={{rsvp_response: 'yes'}}
                >
                    <Icon icon="check" />
                    Going
                </Button>
            )}
            {event.my_rsvp?.response !== 'no' && (
                <Button
                    href={event.my_rsvp.id
                        ? route('events.rsvps.update', {tenant: event.tenant_id, event, rsvp: event.my_rsvp})
                        : route('events.rsvps.store', {tenant: event.tenant_id, event})}
                    method={event.my_rsvp.id ? 'put' : 'post'}
                    size={size}
                    variant="danger-outline"
                    data={{rsvp_response: 'no'}}
                >
                    <Icon icon="times" />
                    Not Going
                </Button>
            )}
        </div>
    );
}

export default MyRsvpButtons;