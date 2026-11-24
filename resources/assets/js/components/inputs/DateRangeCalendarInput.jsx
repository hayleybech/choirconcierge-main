import React from 'react';
import { usePage } from '@inertiajs/react';
import { Popover, Transition } from '@headlessui/react';
import { DayPicker } from 'react-day-picker';
import { DateTime } from 'luxon';
import Icon from '../Icon';
import 'react-day-picker/style.css';
import Button from './ButtonV2';

const DateRangeCalendarInput = ({
	startDate,
	endDate,
	updateFn,
	startMonth,
	endMonth,
	children,
	ariaLabel = 'Attendance report date range',
	inline = false,
	mobile = false,
	onClose,
	onDraftChange,
	hideActions = false,
	renderActions,
}) => {
	const { user } = usePage().props;
	const firstDayOfWeek =
		user?.first_day_of_week ?? (['CA', 'IN', 'JP', 'US'].includes(user?.address_country) ? 0 : 1);
	const selected = {
		from: parseDate(startDate),
		to: parseDate(endDate),
	};
	const [draftRange, setDraftRange] = React.useState(selected);
	React.useEffect(() => setDraftRange(selected), [startDate, endDate]);
	const updateDraftRange = range => {
		setDraftRange(range);
		onDraftChange?.(range);
	};
	const rangeLabel = [selected.from, selected.to]
		.filter(Boolean)
		.map(date => DateTime.fromJSDate(date).toLocaleString(DateTime.DATE_MED))
		.join(' – ');

	const renderCalendar = close => (
		<>
			{typeof children === 'function' ? children(updateDraftRange) : children}
			<DayPicker
				mode="range"
				weekStartsOn={firstDayOfWeek}
				startMonth={startMonth}
				endMonth={endMonth}
				selected={draftRange}
				onSelect={range => updateDraftRange(range ?? {})}
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
			{!hideActions && renderActions ? (
				renderActions({ draftRange, updateDraftRange, selected, close })
			) : !hideActions ? (
				<div className="flex justify-between gap-2 border-t border-gray-200 pt-2">
					<Button
						intent="primary"
						size="sm"
						onClick={() => {
							updateFn(draftRange);
							close();
						}}
					>
						<Icon icon="check" type="regular" /> Apply
					</Button>
					<div className="flex gap-1">
						<Button
							intent="secondary"
							weight="clear"
							size="sm"
							onClick={() => {
								updateDraftRange({});
							}}
						>
							<Icon icon="undo" type="regular" /> Reset
						</Button>
						<Button
							intent="secondary"
							weight="clear"
							size="sm"
							onClick={() => {
								updateDraftRange(selected);
								close();
							}}
						>
							<Icon icon="times" type="regular" /> Cancel
						</Button>
					</div>
				</div>
			) : null}
		</>
	);

	if (inline) {
		return <div className="px-2 py-2">{renderCalendar(onClose ?? (() => {}))}</div>;
	}

	return (
		<Popover as="div" className="relative">
			{({ close, open }) => (
				<>
					<Popover.Button as="div" className="relative rounded-md">
						<div
							className={`pointer-events-none absolute inset-y-0 flex items-center ${
								mobile ? 'left-2' : 'left-0 pl-3'
							}`}
						>
							<Icon
								icon="calendar-day"
								type="regular"
								className={`text-gray-400 ${mobile ? 'text-xs' : ''}`}
							/>
						</div>
						<input
							aria-label={ariaLabel}
							readOnly
							value={rangeLabel}
							placeholder="Select date range"
							className={`block w-full min-w-0 rounded-md border-gray-300 focus:border-purple-500 focus:ring-purple-500 ${
								mobile ? 'h-6 py-0 pl-7 pr-2 text-xs text-gray-500' : 'pl-10 text-sm'
							} ${open ? 'bg-gray-100' : 'bg-white'}`}
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
						<Popover.Panel className="absolute left-0 mt-2 z-40 rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-lg">
							{renderCalendar(close)}
						</Popover.Panel>
					</Transition>
				</>
			)}
		</Popover>
	);
};

const parseDate = value => {
	if (!value) {
		return undefined;
	}
	if (value instanceof Date) {
		return value;
	}

	const date = DateTime.fromISO(String(value));

	return date.isValid ? date.toJSDate() : undefined;
};

export default DateRangeCalendarInput;
