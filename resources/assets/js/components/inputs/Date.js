import React from 'react';
import classNames from '../../classNames';
import DayPicker from "react-day-picker";
import { Popover, Transition } from "@headlessui/react";
import 'react-day-picker/lib/style.css';
import {DateTime} from "luxon";
import Icon from "../Icon";

const DateInput = ({ name, value, updateFn, hasErrors }) => (
    <Popover as="div" className="mt-1 relative">
        <Popover.Button as="div" className="relative rounded-md shadow-sm">
            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Icon icon="calendar-day" type="regular" className="text-gray-400" />
            </div>
            <input
                id={name}
                name={name}
                type="text"
                value={value ? DateTime.fromJSDate(new Date(value)).toLocaleString(DateTime.DATE_MED) : ''}
                className={classNames('' +
                    'shadow-sm focus:outline-none block w-full sm:text-sm rounded-md pl-10',
                    hasErrors
                        ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                        : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500'
                )}
            />
        </Popover.Button>
        <Transition
            enter="transition duration-100 ease-out"
            enterFrom="scale-95 opacity-0"
            enterTo="scale-100 opacity-100"
            leave="transition duration-75 ease-out"
            leaveFrom="scale-100 opacity-100"
            leaveTo="scale-95 opacity-0"
        >
            <Popover.Panel className="absolute mt-2 bg-white overflow-hidden shadow-lg rounded-lg z-10 border border-gray-300">
                <DayPicker onDayClick={day => updateFn(day)} style={{ day: { color: 'blue' } }} />
            </Popover.Panel>
        </Transition>
    </Popover>
);

export default DateInput;