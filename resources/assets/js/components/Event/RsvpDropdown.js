import { Menu, Transition } from "@headlessui/react";
import buttonStyles from "../inputs/buttonStyles";
import Icon from "../Icon";
import React, { Fragment, useState } from "react";
import classNames from "../../classNames";
import { Link, router } from "@inertiajs/react";
import Button from '../inputs/Button';
import TextInput from '../inputs/TextInput';

const items = [
    { label: 'Going', key: 'yes', icon: 'check', colour: 'text-emerald-500' },
    { label: 'Maybe', key: 'maybe', icon: 'question', colour: 'text-amber-500' },
    { label: 'Not Going', key: 'no', icon: 'times', colour: 'text-red-500' },
    { label: 'No RSVP', key: 'unknown', icon: 'circle', colour: 'text-gray-500' },
];

const getItemColour = (label) => items.find(item => item.label === label)?.colour ?? 'text-gray-500';

const RsvpDropdown = ({ event, size = 'sm' }) => {
  const [responseWithDetails, setResponseWithDetails] = useState(null);
  const [details, setDetails] = useState(event.my_rsvp.details || '');
  const [isEditingDetails, setIsEditingDetails] = useState(false);

  const saveResponse = (response, responseDetails = null) => {
    router.visit(event.my_rsvp.id
      ? route('events.rsvps.update', {tenant: event.tenant_id, event, rsvp: event.my_rsvp})
      : route('events.rsvps.store', {tenant: event.tenant_id, event}), {
      method: event.my_rsvp.id ? 'put' : 'post',
      data: {rsvp_response: response, details: responseDetails},
      preserveScroll: true,
      onSuccess: () => {
        setIsEditingDetails(false);
        setResponseWithDetails(null);
      },
    });
  };

  const saveDetails = () => saveResponse(event.my_rsvp.response, details);

  const detailsResponse = responseWithDetails || event.my_rsvp.response;
  const detailsLabel = detailsResponse === 'maybe' ? 'Details' : 'Reason';
  const canHaveDetails = ['maybe', 'no'].includes(detailsResponse);

  return items.length > 0 && (
    <div className="flex flex-wrap items-center gap-1.5">
      <Menu as="div" className="relative inline-block w-full sm:w-auto overflow-visible">
        <Menu.Button className={buttonStyles('secondary', size, '', 'relative z-0 w-full md:w-auto')}>
          <Icon icon={event.my_rsvp.icon} type={event.my_rsvp.icon === 'circle' ? 'regular' : 'solid'} className={getItemColour(event.my_rsvp.label)} />
          <span className={getItemColour(event.my_rsvp.label)}>{event.my_rsvp.label}</span>
          <Icon icon="chevron-down" className="text-gray-500" />
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
          <Menu.Items className="origin-top-right absolute z-10 left-0 mt-2 -mr-1 min-w-full md:min-w-0 md:w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black/5 focus:outline-none">
            {items.filter(item => item.key !== event.my_rsvp.response).map(({ label, key, icon, colour }) =>
              <Menu.Item key={label}>
                {({ active }) => (key === 'maybe' || key === 'no' ? <button
                  type="button"
                  onClick={() => {
                    setDetails('');
                    saveResponse(key);
                  }}
                  className={classNames(
                    'block w-full text-left px-4 py-2 text-sm',
                    colour,
                    active ? 'bg-gray-100' : '',
                  )}
                >
                  <Icon icon={icon} mr type={icon === 'circle' ? 'regular' : 'solid'} className={colour} />
                  {label}
                </button> : <Link
                  href={event.my_rsvp.id
                    ? route('events.rsvps.update', {tenant: event.tenant_id, event, rsvp: event.my_rsvp})
                    : route('events.rsvps.store', {tenant: event.tenant_id, event})
                  }
                  preserveScroll
                  method={event.my_rsvp.id ? 'put' : 'post'}
                  data={{rsvp_response: key}}
                  className={classNames('block w-full text-left px-4 py-2 text-sm', colour, active ? 'bg-gray-100' : '')}
                >
                  <Icon icon={icon} mr type={icon === 'circle' ? 'regular' : 'solid'} className={colour} />
                  {label}
                </Link>
                )}
              </Menu.Item>
            )}
          </Menu.Items>
        </Transition>
      </Menu>

      {canHaveDetails && !isEditingDetails && !event.my_rsvp.details && (
        <Button variant="secondary" size="xs" onClick={() => {
          setResponseWithDetails(event.my_rsvp.response);
          setIsEditingDetails(true);
        }}>
          <Icon icon="plus" mr /> Add {detailsLabel}
        </Button>
      )}

      {canHaveDetails && !isEditingDetails && event.my_rsvp.details && (
        <>
          <div className="text-[11px] text-gray-500 italic max-w-[200px] truncate" title={event.my_rsvp.details}>
            {detailsLabel}: {event.my_rsvp.details}
          </div>
          <Button variant="secondary" size="xs" onClick={() => {
            setResponseWithDetails(event.my_rsvp.response);
            setIsEditingDetails(true);
          }}>
            <Icon icon="pencil" mr /> Edit {detailsLabel.toLowerCase()}
          </Button>
        </>
      )}

      {isEditingDetails && (
        <>
          <TextInput
            name="details"
            value={details}
            updateFn={setDetails}
            placeholder={detailsLabel}
            size="xs"
            autoFocus
            onKeyDown={e => {
              if (e.key === 'Enter') saveDetails();
              if (e.key === 'Escape') setIsEditingDetails(false);
            }}
          />
          <div className="flex gap-1">
            <Button size="xs" onClick={saveDetails} type="button"><Icon icon="check" /></Button>
            <Button variant="secondary" size="xs" onClick={() => setIsEditingDetails(false)} type="button"><Icon icon="times" /></Button>
          </div>
        </>
      )}
    </div>
  );
};

export default RsvpDropdown;
