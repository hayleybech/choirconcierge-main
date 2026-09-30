import React, {Fragment} from 'react';
import {Dialog, Popover, Transition} from '@headlessui/react';
import {usePage} from '@inertiajs/react';
import Icon from './Icon';

const NotificationItems = ({onClose}) => {
    const {notifications} = usePage().props;
    const items = notifications?.items ?? [];

    if (items.length === 0) {
        return <p className="px-4 py-8 text-center text-sm text-gray-500">No notifications yet.</p>;
    }

    return (
        <div className="divide-y divide-gray-100">
            {items.map(notification => (
                <button
                    key={notification.id}
                    type="button"
                    onClick={onClose}
                    className={`block w-full px-4 py-3 text-left transition hover:bg-gray-50 ${notification.read_at ? 'bg-white' : 'bg-purple-50'}`}
                >
                    <div className="flex gap-3">
                        <span className={`mt-1 h-2 w-2 shrink-0 rounded-full ${notification.read_at ? 'bg-gray-300' : 'bg-brand-purple'}`} />
                        <span className="min-w-0">
                            <span className="block text-sm font-semibold text-gray-900">{notification.subject}</span>
                            {notification.body && <span className="mt-1 block text-sm text-gray-600">{notification.body}</span>}
                            <span className="mt-1 block text-xs text-gray-400">{new Date(notification.created_at).toLocaleString()}</span>
                        </span>
                    </div>
                </button>
            ))}
        </div>
    );
};

const NotificationsHeader = ({onClose}) => (
    <div className="flex items-center justify-between border-b border-gray-200 px-4 py-3">
        <h2 className="text-sm font-semibold uppercase tracking-wide text-gray-700">Notifications</h2>
        {onClose && (
            <button type="button" onClick={onClose} className="rounded-full p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                <span className="sr-only">Close notifications</span>
                <Icon icon="times" />
            </button>
        )}
    </div>
);

const NotificationButton = React.forwardRef(({onClick, ...props}, ref) => {
    const {notifications} = usePage().props;
    const unreadCount = notifications?.unread_count ?? 0;

    return (
		<button
			ref={ref}
			type="button"
			onClick={onClick}
			{...props}
			className="relative flex w-full shrink-0 items-center gap-3 rounded-md px-2 py-2 text-left text-white hover:bg-white hover:text-brand-purple-dark hover:bg-opacity-10"
		>
			<Icon icon="bell" type="regular" className="w-6" />
			<span className="text-sm font-semibold">Notifications</span>
			{unreadCount > 0 && (
				<span className="ml-auto flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-xs font-bold text-white">
					{unreadCount > 99 ? '99+' : unreadCount}
				</span>
			)}
		</button>
	);
});

NotificationButton.displayName = 'NotificationButton';

export const MobileNotifications = ({open, setOpen}) => (
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

export {NotificationButton};
export default NotificationsMenu;
