import React from 'react';
import { usePage } from '@inertiajs/react';
import classNames from '../../classNames';
import { DayPicker } from "react-day-picker";
import { Popover, Transition } from "@headlessui/react";
import 'react-day-picker/style.css';
import {DateTime} from "luxon";
import Icon from "../Icon";

const DateCalendarInput = ({ name, value, updateFn, hasErrors }) => {
	const { user } = usePage().props;
	const firstDayOfWeek = user?.first_day_of_week ?? (['CA', 'IN', 'JP', 'US'].includes(user?.address_country) ? 0 : 1);
	const selectedDate = value
		? value instanceof Date
			? value
			: DateTime.fromISO(value).toJSDate()
		: undefined;

	return (
	<Popover as="div" className="mt-1 relative">
		<Popover.Button as="div" className="relative rounded-md">
			<div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
				<Icon icon="calendar-day" type="regular" className="text-gray-400" />
			</div>
			<input
				id={name}
				name={name}
				type="text"
				value={selectedDate ? DateTime.fromJSDate(selectedDate).toLocaleString(DateTime.DATE_MED) : ''}
				className={classNames(
					'' + 'focus:outline-none block w-full sm:text-sm rounded-md pl-10',
					hasErrors
						? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
						: 'border-gray-300 focus:ring-purple-500 focus:border-purple-500'
				)}
				onChange={() => {}}
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
			<Popover.Panel className="absolute mt-2 bg-white overflow-hidden shadow-lg rounded-lg z-20 border border-gray-300 px-3 py-2">
				<DayPicker
					mode="single"
					weekStartsOn={firstDayOfWeek}
					selected={selectedDate}
					onDayClick={day => updateFn(day)}
					captionLayout="dropdown"
					navLayout="around"
					style={{
						'--rdp-accent-color': '#9333ea',
						'--rdp-accent-background-color': '#f3e8ff',
					}}
					classNames={{
						day: 'hover:bg-purple-100! hover:rounded-full ',
						selected: 'bg-purple-600/80! rounded-full text-white hover:bg-purple-700!',
						dropdown: 'absolute inset-0 opacity-0 cursor-pointer',
						dropdown_root: 'relative rounded-sm',
						caption_label: 'flex items-center gap-0.5 text-base',
					}}
				/>
			</Popover.Panel>
		</Transition>
	</Popover>
)};

export default DateCalendarInput;
