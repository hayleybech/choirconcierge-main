import classNames from '../classNames';
import Icon from './Icon';
import React from 'react';

export const mailTypeIcons = {
	broadcast: 'satellite-dish',
	inbound: 'envelope',
	notification: 'bell',
};

export const mailIconColours = {
	'received': 'bg-gray-500',
	'pending': 'bg-gray-500',
	'group-not-found': 'bg-amber-500',
	'group-found': 'bg-gray-500',
	'rejected-sender': 'bg-red-500',
	'malformed-recipient': 'bg-red-500',
	'clone-failed': 'bg-red-500',
	'clones-sent': 'bg-emerald-500',
	'notification-sent': 'bg-emerald-500',
	'opened': 'bg-blue-500',
	'send-failed': 'bg-red-500',
};

export const mailIcons = {
	'received': 'inbox-in',
	'pending': 'inbox',
	'group-not-found': 'users-slash',
	'group-found': 'users',
	'rejected-sender': 'user-slash',
	'malformed-recipient': 'exclamation-triangle',
	'clone-failed': 'exclamation-triangle',
	'clones-sent': 'inbox-out',
	'notification-sent': 'inbox-out',
	'opened': 'envelope-open-text',
	'send-failed': 'exclamation-circle',
}

export const mailLabels = {
	'received': 'Pending',
	'pending': 'Pending',
	'group-not-found': 'Group not found',
	'group-found': 'Group found',
	'rejected-sender': 'Sender rejected',
	'malformed-recipient': 'Malformed address',
	'clone-failed': 'Failed to send to user',
	'clones-sent': 'Sent',
	'opened': 'Opened',
	'send-failed': 'Send failed',
}

const MailStatusTag = ({event, showLabel = true}) => (
	<div className="relative flex space-x-3">
		<div>
            <span
				className={classNames(
					mailIconColours[event.status] ?? 'bg-gray-500',
					'flex h-6 w-6 items-center justify-center rounded-full',
				)}
			>
                <Icon icon={mailIcons[event.status] ?? 'question-circle'} type="regular" size="text-xs" className="text-white" />
            </span>
		</div>
		{showLabel && (
			<div className="flex min-w-0 flex-1 justify-between space-x-4 items-center">
				<p className="text-sm text-gray-500">
					{mailLabels[event.status] ?? 'Unknown status'}
				</p>
			</div>
		)}
	</div>
);

export default MailStatusTag;