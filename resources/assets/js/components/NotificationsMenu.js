import React, { Fragment } from 'react';
import { Dialog, Popover, Transition } from '@headlessui/react';
import { router, usePage } from '@inertiajs/react';
import { DateTime } from 'luxon';
import Button from './inputs/Button';
import Icon from './Icon';

const NotificationItems = ({ onClose }) => {
	const { notifications } = usePage().props;
	const items = notifications?.items ?? [];

	if (items.length === 0) {
		return <p className="px-4 py-8 text-center text-sm text-gray-500">No notifications yet.</p>;
	}

	return (
		<div className="divide-y divide-gray-100">
			{items.map(notification => (
				<div
					key={notification.id}
					className={`group relative ${notification.read_at ? 'bg-white' : 'bg-purple-50'}`}
				>
					<div className="flex w-full">
						<button
							type="button"
							aria-label={`Mark as ${notification.read_at ? 'unread' : 'read'}`}
							title={`Mark as ${notification.read_at ? 'unread' : 'read'}`}
							onClick={() =>
								router.patch(
									route('notifications.update', notification.id),
									{},
									{
										only: ['notifications'],
										preserveScroll: true,
										preserveState: true,
									}
								)
							}
							className={`flex pl-3 pr-3 shrink-0 items-center justify-center self-stretch py-3 text-xs transition hover:cursor-pointer focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500 ${
								notification.read_at
									? 'text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200'
									: 'text-purple-500 hover:text-purple-700 bg-purple-100 hover:bg-purple-200'
							}`}
						>
							<Icon icon={notification.read_at ? 'envelope' : 'envelope-open'} type="regular" />
						</button>
						<a
							href={notification.url ?? '#'}
							onClick={event => {
								if (!notification.url) {
									event.preventDefault();
									return;
								}

								if (!notification.read_at) {
									event.preventDefault();
									router.post(
										route('notifications.read', notification.id),
										{},
										{
											only: ['notifications'],
											preserveScroll: true,
											preserveState: true,
											onSuccess: () => {
												onClose?.();
												router.visit(notification.url);
											},
										}
									);
								} else {
									onClose?.();
								}
							}}
							className="block min-w-0 flex-1 px-4 py-3 text-left transition hover:bg-gray-50"
						>
							<div className="flex w-full">
								<span className="min-w-0">
									{notification.tenant_name && (
										<span className="block text-xs font-medium text-purple-600 group-hover:cursor-pointer">
											{notification.tenant_name}
										</span>
									)}
									<span className="block text-sm font-semibold text-gray-900 group-hover:cursor-pointer">
										{notification.subject}
									</span>
									{notification.body && (
										<span className="mt-1 line-clamp-2 block text-sm text-gray-600 group-hover:cursor-pointer">
											{notification.body}
										</span>
									)}
									<span
										className="mt-1 block text-xs text-gray-400 group-hover:cursor-pointer"
										title={new Date(notification.created_at).toLocaleString()}
									>
										{DateTime.fromJSDate(new Date(notification.created_at)).toRelative()}
									</span>
								</span>
							</div>
						</a>
					</div>
				</div>
			))}
		</div>
	);
};

const NotificationsHeader = ({ onClose }) => {
	const { notifications } = usePage().props;

	return (
		<>
			<div className="flex items-center justify-between border-b border-gray-200 px-4 py-3 gap-1">
				<div className="flex items-center gap-2 grow justify-between">
					<h2 className="text-sm font-semibold uppercase tracking-wide text-gray-700">Notifications</h2>
					{notifications?.unread_count > 0 && (
						<button
							type="button"
							onClick={() =>
								router.post(
									route('notifications.read-all'),
									{},
									{
										only: ['notifications'],
										preserveScroll: true,
										preserveState: true,
									}
								)
							}
							className="text-xs text-purple-500 hover:underline hover:cursor-pointer"
						>
							<Icon icon="envelope-open" type="regular" mr />
							Mark all as read
						</button>
					)}
				</div>
				{onClose && (
					<button
						type="button"
						onClick={onClose}
						className="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
					>
						<span className="sr-only">Close notifications</span>
						<Icon icon="times" />
					</button>
				)}
			</div>
			<div className="flex gap-4 border-b border-gray-200 px-4 py-2 text-xs">
				{['all', 'unread'].map(filter =>
					notifications?.filter === filter ? (
						<span key={filter} className="capitalize text-gray-700">
							{filter}
						</span>
					) : (
						<button
							key={filter}
							type="button"
							onClick={() =>
								router.reload({
									data: { notification_filter: filter },
									only: ['notifications'],
									preserveScroll: true,
									preserveState: true,
								})
							}
							className="text-purple-500 hover:underline hover:cursor-pointer"
						>
							<Icon icon={filter === 'unread' ? 'envelope' : 'inbox'} type="regular" mr />
							<span className="capitalize">{filter}</span>
						</button>
					)
				)}
			</div>
		</>
	);
};

const NotificationButton = React.forwardRef(({ onClick, ...props }, ref) => {
	const { notifications } = usePage().props;
	const unreadCount = notifications?.unread_count ?? 0;

	return (
		<Button
			ref={ref}
			type="button"
			onClick={onClick}
			{...props}
			variant="clear-inverse"
			size="xs"
			className="relative w-full shrink-0 rounded-md px-2 py-2 text-left text-white hover:bg-white hover:text-brand-purple-dark hover:bg-opacity-10"
		>
			<div className="flex gap-x-4 justify-between items-center w-full">
				<div className="flex items-center gap-x-4">
					<Icon icon="bell" type="regular" className="text-base" />
					<span className="text-base font-semibold uppercase">Notifications</span>
				</div>
				{unreadCount > 0 && (
					<span className="flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-xs font-bold text-white">
						{unreadCount > 99 ? '99+' : unreadCount}
					</span>
				)}
			</div>
		</Button>
	);
});

NotificationButton.displayName = 'NotificationButton';

export const MobileNotifications = ({ open, setOpen }) => (
	<Transition.Root show={open} as={Fragment}>
		<Dialog as="div" className="fixed inset-0 z-50 flex" onClose={setOpen}>
			<Transition.Child
				as={Fragment}
				enter="transition-opacity ease-linear duration-300"
				enterFrom="opacity-0"
				enterTo="opacity-100"
				leave="transition-opacity ease-linear duration-300"
				leaveFrom="opacity-100"
				leaveTo="opacity-0"
			>
				<Dialog.Overlay className="fixed inset-0 bg-gray-600 bg-opacity-75" />
			</Transition.Child>
			<Transition.Child
				as={Fragment}
				enter="transition ease-in-out duration-300 transform"
				enterFrom="-translate-x-full"
				enterTo="translate-x-0"
				leave="transition ease-in-out duration-300 transform"
				leaveFrom="translate-x-0"
				leaveTo="-translate-x-full"
			>
				<div className="relative z-10 flex w-full max-w-xs flex-col bg-white shadow-xl">
					<NotificationsHeader onClose={() => setOpen(false)} />
					<div className="min-h-0 flex-1 overflow-y-auto">
						<NotificationItems onClose={() => setOpen(false)} />
					</div>
				</div>
			</Transition.Child>
		</Dialog>
	</Transition.Root>
);

const NotificationsMenu = () => (
	<Popover className="relative">
		<Popover.Button as={React.Fragment}>
			<NotificationButton />
		</Popover.Button>
		<Transition
			as={Fragment}
			enter="transition ease-out duration-100"
			enterFrom="opacity-0 scale-95"
			enterTo="opacity-100 scale-100"
			leave="transition ease-in duration-75"
			leaveFrom="opacity-100 scale-100"
			leaveTo="opacity-0 scale-95"
		>
			<Popover.Panel className="absolute top-0 left-full ml-2 z-20 mb-2 w-96 max-w-[calc(100vw-2rem)] origin-top-left overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">
				<NotificationsHeader />
				<div className="max-h-96 overflow-y-auto">
					<NotificationItems />
				</div>
			</Popover.Panel>
		</Transition>
	</Popover>
);

export { NotificationButton };
export default NotificationsMenu;
