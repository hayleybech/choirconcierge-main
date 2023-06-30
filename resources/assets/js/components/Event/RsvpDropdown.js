import { Menu, Transition } from "@headlessui/react";
import buttonStyles from "../inputs/buttonStyles";
import Icon from "../Icon";
import React, { Fragment } from "react";
import classNames from "../../classNames";
import { Link } from "@inertiajs/inertia-react";

const RsvpDropdown = ({ event, size = 'sm' }) => {
  let items = [
    { label: 'Going', key: 'yes', icon: 'check', colour: 'emerald' },
    { label: 'Not Going', key: 'no', icon: 'times', colour: 'gray' },
    { label: 'No Response', key: 'unknown', icon: 'question', colour: 'red' },
  ];

  return items.length > 0 && (
    <Menu as="div" className="relative inline-block w-full overflow-visible">
      <Menu.Button className={buttonStyles('secondary', size, '', 'relative z-0 w-full md:w-auto')}>
        <Icon icon={event.my_rsvp.icon} className={`text-${event.my_rsvp.colour}-600`} />
        <span className={`text-${event.my_rsvp.colour}-500`}>{event.my_rsvp.label}</span>
        <Icon icon="chevron-down" className="text-gray-800" />
      </Menu.Button>

      <Transition
        as={Fragment}
        enter="transition ease-out duration-200"
        enterFrom="opacity-0 scale-95"
        enterTo="opacity-100 scale-100"
        leave="transition ease-in duration-75"
        leaveFrom="opacity-100 scale-100"
        leaveTo="opacity-0 scale-95"
      >
        <Menu.Items
          className="origin-top-right absolute z-10 left-0 mt-2 -mr-1 min-w-full md:min-w-0 md:w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
          {items.filter(item => item.key !== event.my_rsvp.response).map(({ label, key, icon, colour }) =>
            <Menu.Item key={label}>
              {({ active }) => (
                <Link
                  href={event.my_rsvp.id
                    ? route('events.rsvps.update', {tenant: event.tenant_id, event, rsvp: event.my_rsvp})
                    : route('events.rsvps.store', {tenant: event.tenant_id, event})
                  }
                  preserveScroll
                  method={event.my_rsvp.id ? 'put' : 'post'}
                  data={{rsvp_response: key}}
                  className={classNames(
                    'block w-full text-left px-4 py-2 text-sm',
                    `text-${colour}-500`,
                    active ? 'bg-gray-100' : '',
                  )}
                >
                  <Icon icon={icon} mr />
                  {label}
                </Link>
              )}
            </Menu.Item>
          )}
        </Menu.Items>
      </Transition>
    </Menu>
  );
};

export default RsvpDropdown;