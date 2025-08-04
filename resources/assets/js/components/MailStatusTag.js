import classNames from '../classNames';
import Icon from './Icon';
import React from 'react';

export const mailIconColours = {
	'received': 'bg-gray-400',
	'pending': 'bg-gray-400',
	'group-not-found': 'bg-amber-400',
	'group-found': 'bg-gray-400',
	'rejected-sender': 'bg-red-400',
	'malformed-recipient': 'bg-red-400',
	'clones-sent': 'bg-emerald-400',
};

export const mailIcons = {
	'received': 'inbox-in',
	'pending': 'inbox',
	'group-not-found': 'users-slash',
	'group-found': 'users',
	'rejected-sender': 'user-slash',
	'malformed-recipient': 'exclamation-triangle',
	'clones-sent': 'inbox-out',
}

export const mailLabels = {
	'received': 'Pending',
	'pending': 'Pending',
	'group-not-found': 'Group not found',
	'group-found': 'Group found',
	'rejected-sender': 'Sender rejected',
	'malformed-recipient': 'Malformed address',
	'clones-sent': 'Sent',
}

const MailStatusTag = ({event, showLabel = true}) => (
	<div className="relative flex space-x-3">
		<div>
            <span
				className={classNames(
					mailIconColours[event.status],
					'flex h-6 w-6 items-center justify-center rounded-full',
				)}
			>
                <Icon icon={mailIcons[event.status]} type="regular" size="text-xs" className="text-white" />
            </span>
		</div>
		{showLabel && (
			<div className="flex min-w-0 flex-1 justify-between space-x-4 items-center">
				<p className="text-sm text-gray-500">
					{mailLabels[event.status]}
				</p>
			</div>
		)}
	</div>
);

export default MailStatusTag;