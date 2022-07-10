import React, {Fragment, useState} from 'react'
import { Menu, Transition } from '@headlessui/react'
import Icon from "../../components/Icon";
import classNames from "../../classNames";
import {InertiaLink} from "@inertiajs/inertia-react";
import {DateTime} from "luxon";
import DateTag from "../../components/DateTag";
import Badge from "../../components/Badge";
import EventType from "../../EventType";

const Calendar = ({ days, month }) => {
    const [selectedDay, setSelectedDay] = useState(days[0]);

    return (
        <div className="lg:flex lg:h-full lg:flex-col">
            <header className="relative z-20 flex items-center justify-between border-b border-gray-200 py-4 px-6 lg:flex-none">
                <h1 className="text-lg font-semibold text-gray-900">
                    <time dateTime={month}>{DateTime.fromISO(month).toFormat('LLLL y')}</time>
                </h1>
                <div className="flex items-center">
                    <MonthNavigation currentMonth={month} />
                    {/*<ViewMenu />*/}
                    <CalendarMenuMobile />
                </div>
            </header>
            <div className="shadow ring-1 ring-black ring-opacity-5 lg:flex lg:flex-auto lg:flex-col">
                <DayHeadings />
                <div className="flex bg-gray-200 text-xs leading-6 text-gray-700 lg:flex-auto">
                    <div className="hidden w-full lg:grid lg:grid-cols-7 lg:grid-rows-6 lg:gap-px">
                        {days.map((day) => (
                            <DayEntryDesktop day={day} key={day.date} />
                        ))}
                    </div>
                    <div className="isolate grid w-full grid-cols-7 grid-rows-6 gap-px lg:hidden">
                        {days.map((day) => (
                            <DayEntryMobile day={day} isSelectedDay={day === selectedDay} selectDay={() => setSelectedDay(day)} key={day.date} />
                        ))}
                    </div>
                </div>
            </div>

            <MobileEventList selectedDay={selectedDay} />
        </div>
    );
}

export default Calendar;

const MonthNavigation = ({ currentMonth }) => (
    <div className="flex items-center rounded-md shadow-sm md:items-stretch">
        <InertiaLink
            href={route('events.calendar.month')}
            data={{ month: DateTime.fromISO(currentMonth).minus({ months: 1 }).toFormat('y-MM-dd') }}
            className="flex items-center justify-center rounded-l-md border border-r-0 border-gray-300 bg-white py-2 pl-3 pr-4 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:px-2 md:hover:bg-gray-50"
        >
            <span className="sr-only">Previous month</span>
            <Icon icon="chevron-left" />
        </InertiaLink>
        <InertiaLink
            href={route('events.calendar.month')}
            data={{ month: DateTime.now().toFormat('y-MM-dd') }}
            className="hidden border-t border-b border-gray-300 bg-white px-3.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 focus:relative md:flex items-center"
        >
            Today
        </InertiaLink>
        <span className="relative -mx-px h-5 w-px bg-gray-300 md:hidden" />
        <InertiaLink
            href={route('events.calendar.month')}
            data={{ month: DateTime.fromISO(currentMonth).plus({ months: 1 }).toFormat('y-MM-dd') }}
            className="flex items-center justify-center rounded-r-md border border-l-0 border-gray-300 bg-white py-2 pl-4 pr-3 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:px-2 md:hover:bg-gray-50"
        >
            <span className="sr-only">Next month</span>
            <Icon icon="chevron-right" />
        </InertiaLink>
    </div>
);

const ViewMenu = () => (
    <div className="hidden md:ml-4 md:flex md:items-center">
        <Menu as="div" className="relative">
            <Menu.Button
                type="button"
                className="flex items-center rounded-md border border-gray-300 bg-white py-2 pl-3 pr-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
            >
                Month view
                <Icon icon="chevron-down" className="text-gray-400" ml />
            </Menu.Button>

            <Transition
                as={Fragment}
                enter="transition ease-out duration-100"
                enterFrom="transform opacity-0 scale-95"
                enterTo="transform opacity-100 scale-100"
                leave="transition ease-in duration-75"
                leaveFrom="transform opacity-100 scale-100"
                leaveTo="transform opacity-0 scale-95"
            >
                <Menu.Items className="focus:outline-none absolute right-0 mt-3 w-36 origin-top-right overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                    <div className="py-1">
                        <Menu.Item>
                            {({ active }) => (
                                <a
                                    href="#"
                                    className={classNames(
                                        active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                                        'block px-4 py-2 text-sm'
                                    )}
                                >
                                    Day view
                                </a>
                            )}
                        </Menu.Item>
                        <Menu.Item>
                            {({ active }) => (
                                <a
                                    href="#"
                                    className={classNames(
                                        active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                                        'block px-4 py-2 text-sm'
                                    )}
                                >
                                    Week view
                                </a>
                            )}
                        </Menu.Item>
                        <Menu.Item>
                            {({ active }) => (
                                <a
                                    href="#"
                                    className={classNames(
                                        active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                                        'block px-4 py-2 text-sm'
                                    )}
                                >
                                    Month view
                                </a>
                            )}
                        </Menu.Item>
                        <Menu.Item>
                            {({ active }) => (
                                <a
                                    href="#"
                                    className={classNames(
                                        active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                                        'block px-4 py-2 text-sm'
                                    )}
                                >
                                    Year view
                                </a>
                            )}
                        </Menu.Item>
                    </div>
                </Menu.Items>
            </Transition>
        </Menu>
    </div>
);

const CalendarMenuMobile = () => (
    <Menu as="div" className="relative ml-6 md:hidden">
        <Menu.Button className="-mx-2 flex items-center rounded-full border border-transparent p-2 text-gray-400 hover:text-gray-500">
            <span className="sr-only">Open menu</span>
            <Icon icon="ellipsis-h" />
        </Menu.Button>

        <Transition
            as={Fragment}
            enter="transition ease-out duration-100"
            enterFrom="transform opacity-0 scale-95"
            enterTo="transform opacity-100 scale-100"
            leave="transition ease-in duration-75"
            leaveFrom="transform opacity-100 scale-100"
            leaveTo="transform opacity-0 scale-95"
        >
            <Menu.Items className="focus:outline-none absolute right-0 mt-3 w-36 origin-top-right divide-y divide-gray-100 overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                <div className="py-1">
                    <Menu.Item>
                        {({ active }) => (
                            <InertiaLink
                                href={route('events.calendar.month')}
                                data={{ month: DateTime.now().toFormat('y-MM-dd') }}
                                className={classNames(
                                    active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                                    'block px-4 py-2 text-sm'
                                )}
                            >
                                Go to today
                            </InertiaLink>
                        )}
                    </Menu.Item>
                </div>
                {/*<div className="py-1">*/}
                {/*    <Menu.Item>*/}
                {/*        {({ active }) => (*/}
                {/*            <a*/}
                {/*                href="#"*/}
                {/*                className={classNames(*/}
                {/*                    active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',*/}
                {/*                    'block px-4 py-2 text-sm'*/}
                {/*                )}*/}
                {/*            >*/}
                {/*                Day view*/}
                {/*            </a>*/}
                {/*        )}*/}
                {/*    </Menu.Item>*/}
                {/*    <Menu.Item>*/}
                {/*        {({ active }) => (*/}
                {/*            <a*/}
                {/*                href="#"*/}
                {/*                className={classNames(*/}
                {/*                    active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',*/}
                {/*                    'block px-4 py-2 text-sm'*/}
                {/*                )}*/}
                {/*            >*/}
                {/*                Week view*/}
                {/*            </a>*/}
                {/*        )}*/}
                {/*    </Menu.Item>*/}
                {/*    <Menu.Item>*/}
                {/*        {({ active }) => (*/}
                {/*            <a*/}
                {/*                href="#"*/}
                {/*                className={classNames(*/}
                {/*                    active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',*/}
                {/*                    'block px-4 py-2 text-sm'*/}
                {/*                )}*/}
                {/*            >*/}
                {/*                Month view*/}
                {/*            </a>*/}
                {/*        )}*/}
                {/*    </Menu.Item>*/}
                {/*    <Menu.Item>*/}
                {/*        {({ active }) => (*/}
                {/*            <a*/}
                {/*                href="#"*/}
                {/*                className={classNames(*/}
                {/*                    active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',*/}
                {/*                    'block px-4 py-2 text-sm'*/}
                {/*                )}*/}
                {/*            >*/}
                {/*                Year view*/}
                {/*            </a>*/}
                {/*        )}*/}
                {/*    </Menu.Item>*/}
                {/*</div>*/}
            </Menu.Items>
        </Transition>
    </Menu>
);

const DayHeadings = () => (
    <div className="grid grid-cols-7 gap-px border-b border-gray-300 bg-gray-200 text-center text-xs font-semibold leading-6 text-gray-700 lg:flex-none">
        <div className="bg-white py-2">
            <span className="sm:hidden">M<span className="sr-only">on</span></span>
            <span className="hidden sm:inline">Mon</span>
        </div>
        <div className="bg-white py-2">
            <span className="sm:hidden">T<span className="sr-only">ue</span></span>
            <span className="hidden sm:inline">Tue</span>
        </div>
        <div className="bg-white py-2">
            <span className="sm:hidden">W<span className="sr-only">ed</span></span>
            <span className="hidden sm:inline">Wed</span>
        </div>
        <div className="bg-white py-2">
            <span className="sm:hidden">T<span className="sr-only">hu</span></span>
            <span className="hidden sm:inline">Thu</span>
        </div>
        <div className="bg-white py-2">
            <span className="sm:hidden">F<span className="sr-only">ri</span></span>
            <span className="hidden sm:inline">Fri</span>
        </div>
        <div className="bg-white py-2">
            <span className="sm:hidden">S<span className="sr-only">at</span></span>
            <span className="hidden sm:inline">Sat</span>
        </div>
        <div className="bg-white py-2">
            <span className="sm:hidden">S<span className="sr-only">un</span></span>
            <span className="hidden sm:inline">Sun</span>
        </div>
    </div>
);

const DayEntryDesktop = ({ day }) => (
    <div
        className={classNames(
            day.isCurrentMonth ? 'bg-white' : 'bg-gray-100 text-gray-500',
            'relative py-2 px-3'
        )}
    >
        <time
            dateTime={day.date}
            className={
                day.isToday
                    ? 'flex h-6 w-6 items-center justify-center rounded-full bg-purple-600 font-semibold text-white'
                    : undefined
            }
        >
            {DateTime.fromISO(day.date).toFormat('d')}
        </time>
        {day.events.length > 0 && (
            <ol className="mt-2">
                {day.events.slice(0, 2).map((event) => (
                    <li key={event.id}>
                        <InertiaLink href={route('events.show', event.id)} className={`group flex px-2 mb-1 rounded border ${(new EventType(event.type.title)).borderColour}`}>
                            <p className="flex-auto truncate font-medium text-gray-900 group-hover:text-purple-600">
                                {event.title}
                            </p>
                            <time
                                dateTime={event.call_time}
                                className="ml-3 hidden flex-none text-gray-500 group-hover:text-purple-600 xl:block"
                            >
                                {DateTime.fromISO(event.call_time).toLocaleString(DateTime.TIME_SIMPLE)}
                            </time>
                        </InertiaLink>
                    </li>
                ))}
                {day.events.length > 2 && <li className="text-gray-500">+ {day.events.length - 2} more</li>}
            </ol>
        )}
    </div>
);

const DayEntryMobile = ({ day, isSelectedDay, selectDay, }) => (
    <button
        type="button"
        className={classNames(
            day.isCurrentMonth ? 'bg-white' : 'bg-gray-100',
            (isSelectedDay || day.isToday) && 'font-semibold',
            isSelectedDay && 'text-white',
            !isSelectedDay && day.isToday && 'text-purple-600',
            !isSelectedDay && day.isCurrentMonth && !day.isToday && 'text-gray-900',
            !isSelectedDay && !day.isCurrentMonth && !day.isToday && 'text-gray-500',
            'flex h-14 flex-col py-2 px-3 hover:bg-gray-100 focus:z-10'
        )}
        onClick={selectDay}
    >
        <time
            dateTime={day.date}
            className={classNames(
                isSelectedDay && 'flex h-6 w-6 items-center justify-center rounded-full',
                isSelectedDay && day.isToday && 'bg-purple-600',
                isSelectedDay && !day.isToday && 'bg-gray-900',
                'ml-auto'
            )}
        >
            {DateTime.fromISO(day.date).toFormat('d')}
        </time>
        <span className="sr-only">{day.events.length} events</span>
        {day.events.length > 0 && (
            <span className="-mx-0.5 mt-auto flex flex-wrap-reverse">
                {day.events.map((event) => (
                    <span key={event.id} className={`mx-0.5 mb-1 h-1.5 w-1.5 rounded-full ${(new EventType(event.type.title)).dotColour}`} />
                ))}
            </span>
        )}
    </button>
);

const MobileEventList = ({ selectedDay }) => selectedDay?.events.length > 0 && (
    <div className="pt-6 pb-10 px-4 sm:px-6 lg:hidden">
        <h2 className="text-md font-semibold text-gray-700 mb-4">
            <time dateTime={selectedDay.date}>{DateTime.fromISO(selectedDay.date).toFormat('cccc, LLL d')}</time>
        </h2>

        <ol className="divide-y divide-gray-100 overflow-hidden rounded-lg bg-white text-sm shadow ring-1 ring-black ring-opacity-5">
            {selectedDay.events.map((event) => (
                <li key={event.id} className="">

                    <InertiaLink
                        href={route('events.show', event.id)}
                        className="flex items-center px-4 py-4 sm:px-6 focus-within:bg-gray-50 hover:bg-gray-50"
                    >
                        <div className="flex-1 flex flex-col justify-center justify-between min-w-0 pr-4">

                            <div className="flex items-center justify-between">
                                <p className="flex items-center min-w-0 mr-1.5">
                                    <span className="text-sm font-medium text-purple-600 truncate">{event.title}</span>
                                </p>
                                <div className="text-xs text-gray-500">
                                    {DateTime.fromISO(event.call_time) < DateTime.now()
                                        ? <p>{event.present_count ? `${event.present_count} present` : ''}</p>
                                        : <p>{event.going_count ? `${event.going_count} going` : ''}</p>
                                    }
                                </div>
                            </div>

                            <div className="flex items-center justify-between">
                                <p className="mt-1.5 flex items-center text-xs text-gray-500 min-w-0">
                                    <time dateTime={event.call_time} className="mt-2 flex items-center text-gray-700 text-xs">
                                        <Icon icon="clock" type="regular" className="text-gray-400" mr />
                                        {DateTime.fromISO(event.call_time).toLocaleString(DateTime.TIME_SIMPLE)}
                                    </time>
                                </p>

                                <p className="mt-2 flex items-center text-sm text-gray-500 min-w-0">
                                    <Badge colour={(new EventType(event.type.title)).badgeColour}>{event.type.title}</Badge>
                                </p>
                            </div>

                            {event.location_name && (
                                <div className="mt-2 text-gray-700 text-xs">
                                    <Icon icon="map-marker" type="regular" mr className="text-gray-400" />
                                    {event.location_name}
                                </div>
                            )}
                        </div>
                        <div>
                            <Icon icon="chevron-right" mr className="text-gray-400" />
                        </div>
                    </InertiaLink>
                </li>
            ))}
        </ol>
    </div>
);