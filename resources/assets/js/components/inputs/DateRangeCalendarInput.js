import React from 'react';
import { usePage } from '@inertiajs/react';
import { Popover, Transition } from '@headlessui/react';
import { DayPicker } from 'react-day-picker';
import { DateTime } from 'luxon';
import Icon from '../Icon';
import 'react-day-picker/style.css';
import Button from './ButtonV2';

const DateRangeCalendarInput = ({ startDate, endDate, updateFn, startMonth, endMonth, children }) => {
	const { user } = usePage().props;
	const firstDayOfWeek = user?.first_day_of_week ?? (['CA', 'IN', 'JP', 'US'].includes(user?.address_country) ? 0 : 1);
	const selected = {
		from: startDate ? DateTime.fromISO(startDate).toJSDate() : undefined,
		to: endDate ? DateTime.fromISO(endDate).toJSDate() : undefined,
	};
	const [draftRange, setDraftRange] = React.useState(selected);
	React.useEffect(() => setDraftRange(selected), [startDate, endDate]);
	const rangeLabel = [selected.from, selected.to]
		.filter(Boolean)
		.map(date => DateTime.fromJSDate(date).toLocaleString(DateTime.DATE_MED))
		.join(' – ');
	const draftRangeLabel = [draftRange.from, draftRange.to]
		.filter(Boolean)
		.map(date => DateTime.fromJSDate(date).toLocaleString(DateTime.DATE_MED))
		.join(' – ');

	return (
		<Popover as="div" className="relative">
			{({ close, open }) => (
				<>
					<Popover.Button as="div" className="relative rounded-md">
						<div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
							<Icon icon="calendar-day" type="regular" className="text-gray-400" />
						</div>
						<input
							aria-label="Attendance report date range"
							readOnly
							value={rangeLabel}
							placeholder="Select date range"
							className={`block w-full rounded-md border-gray-300 pl-10 text-sm focus:border-purple-500 focus:ring-purple-500 ${
								open ? 'bg-gray-100' : 'bg-white'
							}`}
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
						<Popover.Panel className="absolute right-0 z-20 mt-2 overflow-hidden rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-lg">
							<div
								aria-label="Selected date range"
								className="mb-2 border-b border-gray-200 pb-2 text-sm font-medium text-gray-700"
							>
								{draftRangeLabel || 'Select date range'}
							</div>
							{typeof children === 'function' ? children(setDraftRange) : children}
							<DayPicker
								mode="range"
								weekStartsOn={firstDayOfWeek}
								startMonth={startMonth}
								endMonth={endMonth}
								selected={draftRange}
								onSelect={range => setDraftRange(range ?? {})}
								resetOnSelect
								captionLayout="dropdown"
								navLayout="around"
								style={{ '--rdp-accent-color': '#9333ea', '--rdp-accent-background-color': '#f3e8ff' }}
								classNames={{
									day: 'hover:[&>button]:bg-purple-300! hover:[&>button]:rounded-full! text-gray-700 text-base!',
									today: 'font-bold text-purple-600',
									dropdown: 'absolute inset-0 opacity-0 cursor-pointer',
									dropdown_root: 'relative rounded-sm',
									caption_label: 'flex items-center gap-0.5 text-base text-gray-700',
								}}
							/>
							<div className="flex justify-between gap-2 border-t border-gray-200 pt-2">
								<Button
									intent="secondary"
									weight="clear"
									size="sm"
									onClick={() => {
										setDraftRange({});
									}}
								>
									Reset
								</Button>
								<div className="flex gap-2">
									<Button
										itent="secondary"
										size="sm"
										onClick={() => {
											setDraftRange(selected);
											close();
										}}
									>
										Cancel
									</Button>
									<Button
										intent="primary"
										size="sm"
										onClick={() => {
											updateFn(draftRange);
											close();
										}}
									>
										Apply
									</Button>
								</div>
							</div>
						</Popover.Panel>
					</Transition>
				</>
			)}
		</Popover>
	);
};

export default DateRangeCalendarInput;