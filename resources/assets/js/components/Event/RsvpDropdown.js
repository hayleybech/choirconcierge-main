import { Popover, Transition } from "@headlessui/react";
import buttonStyles from "../inputs/buttonStyles";
import Icon from "../Icon";
import React, { Fragment, useState } from "react";
import classNames from "../../classNames";
import { router } from "@inertiajs/react";
import Button from '../inputs/Button';
import TextInput from '../inputs/TextInput';

const items = [
    { label: 'Going', key: 'yes', icon: 'check', colour: 'text-emerald-500' },
    { label: 'Maybe', key: 'maybe', icon: 'question', colour: 'text-amber-500' },
    { label: 'Not Going', key: 'no', icon: 'times', colour: 'text-red-500' },
];

const getItemColour = (label) => items.find(item => item.label === label)?.colour ?? 'text-gray-500';

const RsvpDropdown = ({ event, size = 'sm' }) => {
  const initialDetailsResponse = ['maybe', 'no'].includes(event.my_rsvp.response) && !event.my_rsvp.details
    ? event.my_rsvp.response
    : null;
  const [responseWithDetails, setResponseWithDetails] = useState(initialDetailsResponse);
  const [details, setDetails] = useState(event.my_rsvp.details || '');
  const [isEditingDetails, setIsEditingDetails] = useState(Boolean(initialDetailsResponse));

  const saveResponse = (response, responseDetails = null, closeDetails = true, closePopover = null) => {
    router.visit(event.my_rsvp.id
      ? route('events.rsvps.update', {tenant: event.tenant_id, event, rsvp: event.my_rsvp})
      : route('events.rsvps.store', {tenant: event.tenant_id, event}), {
      method: event.my_rsvp.id ? 'put' : 'post',
      data: {rsvp_response: response, details: responseDetails},
      only: ['event', 'events'],
      preserveScroll: true,
      onSuccess: () => {
        if (closeDetails) {
          setIsEditingDetails(false);
          setResponseWithDetails(null);
        }
        closePopover?.();
      },
    });
  };

  const saveDetails = (closePopover) => saveResponse(responseWithDetails || event.my_rsvp.response, details, true, closePopover);

  const detailsResponse = responseWithDetails || event.my_rsvp.response;
  const detailsLabel = detailsResponse === 'maybe' ? 'Details' : 'Reason';
  const canHaveDetails = ['maybe', 'no'].includes(detailsResponse);
  const isSavedResponse = (key) => key === event.my_rsvp.response;
  const isPendingResponse = (key) => key === responseWithDetails;
  const isResponseDisabled = (key) => isSavedResponse(key) || isPendingResponse(key);
  const responseClasses = (key, colour) => classNames(
    'block flex-1 w-full text-left px-3 py-2 text-sm transition-colors focus:outline-none',
    isSavedResponse(key) ? 'bg-purple-500 text-white' : colour,
    isResponseDisabled(key) ? '' : 'cursor-pointer hover:bg-gray-100 focus:bg-gray-100 active:bg-gray-200',
  );

  return items.length > 0 && (
    <div className="flex w-full flex-wrap items-center gap-1.5">
      <Popover as="div" className="relative inline-block w-full overflow-visible">
        {({ close }) => <>
        <Popover.Button className={buttonStyles('secondary', size, false, 'relative z-0 flex! w-full')}>
          <Icon icon={event.my_rsvp.icon} type={event.my_rsvp.icon === 'circle' ? 'regular' : 'solid'} className={getItemColour(event.my_rsvp.label)} />
          <span className={getItemColour(event.my_rsvp.label)}>{event.my_rsvp.label}</span>
          <Icon icon="chevron-down" className="text-gray-500" />
        </Popover.Button>

        <Transition
          as={Fragment}
          enter="transition ease-out duration-200"
          enterFrom="opacity-0 scale-95"
          enterTo="opacity-100 scale-100"
          leave="transition ease-in duration-75"
          leaveFrom="opacity-100 scale-100"
          leaveTo="opacity-0 scale-95"
        >
          <Popover.Panel className="origin-top-right absolute z-10 left-0 mt-2 -mr-1 min-w-full md:min-w-0 md:w-64 rounded-md shadow-lg py-1 px-1 bg-white ring-1 ring-black/5 focus:outline-none">
            {items.map(({ label, key, icon, colour }) =>
              <div key={label}>
                <div className={classNames('flex items-center gap-1', isSavedResponse(key) && canHaveDetails ? 'bg-purple-500 text-white' : '')}>
                {key === 'maybe' || key === 'no' ? <button
                  type="button"
                  disabled={isResponseDisabled(key)}
                  onClick={isResponseDisabled(key) ? undefined : () => {
                    setDetails('');
                    setResponseWithDetails(key);
                    setIsEditingDetails(true);
                  }}
                  className={responseClasses(key, colour)}
                >
                  <Icon icon={icon} mr type={icon === 'circle' ? 'regular' : 'solid'} className={isSavedResponse(key) ? 'text-white' : colour} />
                  {label}
                </button> : <button
                  type="button"
                  disabled={isResponseDisabled(key)}
                  onClick={isResponseDisabled(key) ? undefined : () => saveResponse(key, null, true, close)}
                  className={responseClasses(key, colour)}
                >
                  <Icon icon={icon} mr type={icon === 'circle' ? 'regular' : 'solid'} className={isSavedResponse(key) ? 'text-white' : colour} />
                  {label}
                </button>
                }
                </div>
                {key === detailsResponse && canHaveDetails && !isEditingDetails && (
                  <div className={classNames('flex items-center gap-1 px-3 py-1', isSavedResponse(key) ? 'bg-purple-500 text-white' : '')}>
                    {event.my_rsvp.details && (
                      <span className={classNames('max-w-[140px] truncate text-[11px] italic', isSavedResponse(key) ? 'text-white' : 'text-gray-500')} title={event.my_rsvp.details}>
                        {event.my_rsvp.details}
                      </span>
                    )}
                    <button type="button" className={classNames('cursor-pointer px-2 py-1 text-xs transition-colors hover:bg-purple-200 hover:text-gray-700 focus:bg-purple-200 focus:text-gray-700 focus:outline-none active:bg-purple-500', isSavedResponse(key) ? 'text-white' : 'text-gray-500')} onClick={() => {
                      setResponseWithDetails(event.my_rsvp.response);
                      setDetails(event.my_rsvp.details || '');
                      setIsEditingDetails(true);
                    }}>
                      <Icon icon={event.my_rsvp.details ? 'pencil' : 'plus'} />
                      <span className="sr-only">{event.my_rsvp.details ? `Edit ${detailsLabel.toLowerCase()}` : `Add ${detailsLabel}`}</span>
                    </button>
                  </div>
                )}
                {isEditingDetails && responseWithDetails === key && (
                  <div className={classNames('flex gap-1 px-3 pb-2', isSavedResponse(key) ? 'bg-purple-500 text-white' : '')}>
                    <TextInput
                      name="details"
                      value={details}
                      updateFn={setDetails}
                      placeholder={key === 'maybe' ? 'Details' : 'Reason'}
                      size="xs"
                      className="!py-1"
                      autoFocus
                      onKeyDown={e => {
                        if (e.key === 'Enter') saveDetails(close);
                        if (e.key === 'Escape') setIsEditingDetails(false);
                      }}
                    />
                    <Button size="xs" onClick={() => saveDetails(close)} type="button"><Icon icon="check" /></Button>
                    <Button variant="secondary" size="xs" onClick={() => saveResponse(responseWithDetails, null, true, close)} type="button"><Icon icon="times" /></Button>
                  </div>
                )}
              </div>
            )}
          </Popover.Panel>
        </Transition>
        </>}
      </Popover>

    </div>
  );
};

export default RsvpDropdown;
