import { Link } from '@inertiajs/react';
import React from 'react';

const MailStatusDetail = ({log, event, isBroadcast}) => {
	if (event.status === 'received' && isBroadcast) {
		return (
			<p className="text-sm text-gray-500">
				Broadcast created by{' '}
				<span className="font-medium text-gray-900">{log.from}</span>
			</p>
		)
	}
	if (event.status === 'received' && !isBroadcast) {
		return (
			<p className="text-sm text-gray-500">
				Email delivered to{' '}
				<span className="font-medium text-gray-900">our inbox</span>
			</p>
		);
	}
	if (event.status === 'pending') {
		return (
			<p className="text-sm text-gray-500">
				Email found by {' '}
				<span className="font-medium text-gray-900">our email system</span>
			</p>
		);
	}

	if (event.status === 'group-not-found') {
		return (
			<p className="text-sm text-gray-500">
				Recipient skipped: Mailing group{' '}
				<span className="font-medium text-gray-900">
                    {event.context}
                </span>
				{' '}does not exist.
			</p>
		);
	}
	if (event.status === 'group-found') {
		return (
			<p className="text-sm text-gray-500">
				A recipient matches group{' '}
				<Link
					href={route('groups.show', {group: event.user_group, tenant: event.user_group.tenant_id})}
					className="font-medium text-purple-600 hover:text-purple-800 focus:text-purple-800"
				>
					{event.user_group?.title ?? event.context}
				</Link>
				{' '}in our email system.
			</p>
		);
	}
	if (event.status === 'rejected-sender') {
		return (
			<p className="text-sm text-gray-500">
				Sender rejected: Mailing group{' '}
				<span className="font-medium text-gray-900">
                    {event.context}
                </span>
				{' '}exists but{' '}
				<span className="font-medium text-gray-900">
                    {log.from}
                </span>
				{' '}is not permitted to contact it.
			</p>
		);
	}
	if (event.status === 'malformed-recipient') {
		return (
			<p className="text-sm text-gray-500">
				It looks like
				<span className="font-medium text-gray-900">
                    {event.context}
                </span>
				{' '} is not a valid email address.
			</p>
		);
	}
	if (event.status === 'clones-sent') {
		return (
			<p className="text-sm text-gray-500">
				A copy was sent to {' '}
				<span className="font-medium text-gray-900">
                    all recipients
                </span>
			</p>
		);
	}
	if (event.status === 'send-failed') {
		return (
			<p className="text-sm text-gray-500">
				A fatal error occurred while sending mail to {' '}
				<span className="font-medium text-gray-900">
                    {event.context}
                </span>. Processing has been stopped.
			</p>
		);
	}
	return (
		<p className="text-sm text-gray-500">
			Other Status:{' '}
			<span className="font-medium text-gray-900">
                {event.status}
            </span>
		</p>
	);
};

export default MailStatusDetail