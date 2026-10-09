import React, { useState } from 'react';

const initialPreferences = {
	communications: { email: true, push: false },
	documents: { email: true, push: false },
	events: { email: true, push: false },
	system: { email: true, push: false },
	product: { email: true, push: false },
	promotions: { email: false, push: false },
	community: { email: false, push: false },
};

const notificationGroups = [
	{
		title: 'From My Organisations',
		description: 'Choose how you receive updates from the organisations you belong to.',
		items: [
			{
				key: 'communications',
				title: 'Communications',
				description: 'Important messages from your organisation.',
				mandatory: 'Email or Push is required',
			},
			{
				key: 'documents',
				title: 'Document Created/Updated',
				description: 'When an organisation document is created or updated.',
			},
			{
				key: 'events',
				title: 'Event Created/Updated',
				description: 'When an organisation event is created or updated.',
			},
		],
	},
	{
		title: 'From Us',
		description: 'Updates and information from Choir Concierge.',
		items: [
			{
				key: 'system',
				title: 'System & Security',
				description: 'Critical downtime, terms of service changes, and account security alerts.',
				mandatory: 'Email is required',
			},
			{ key: 'product', title: 'Product Updates', description: 'New features, major updates, and UI changes.' },
			{
				key: 'promotions',
				title: 'Promotions & Offers',
				description: 'Discounts, events, and marketing campaigns.',
			},
			{
				key: 'community',
				title: 'Community & Content',
				description: 'New blog posts, newsletters, or community milestones.',
			},
		],
	},
];

const NotificationSettings = () => {
	const [preferences, setPreferences] = useState(initialPreferences);

	const toggle = (key, channel) => {
		setPreferences(current => {
			if (key === 'system' && channel === 'email') {
				return current;
			}

			if (key === 'communications' && current[key].email && current[key].push && channel) {
				return current;
			}

			return {
				...current,
				[key]: { ...current[key], [channel]: !current[key][channel] },
			};
		});
	};

	return (
		<div className="space-y-8">
			<div>
				<h2 className="text-lg font-medium leading-6 text-gray-900">Notification Preferences</h2>
				<p className="mt-1 text-sm text-gray-500">
					Choose which notifications you would like to receive and where.
				</p>
			</div>

			{notificationGroups.map(group => (
				<section key={group.title} aria-labelledby={`${group.title}-heading`}>
					<div className="mb-4">
						<h3 id={`${group.title}-heading`} className="text-base font-medium text-gray-900">
							{group.title}
						</h3>
						<p className="mt-1 text-sm text-gray-500">{group.description}</p>
					</div>
					<div className="overflow-hidden rounded-lg border border-gray-200 bg-white">
						<div className="hidden border-b border-gray-200 bg-gray-50 px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:grid sm:grid-cols-[1fr_5rem_5rem] sm:gap-4">
							<div>Notification</div>
							<div className="text-center">Email</div>
							<div className="text-center">Push</div>
						</div>
						{group.items.map(item => (
							<div
								key={item.key}
								className="grid grid-cols-1 gap-4 border-b border-gray-200 px-5 py-4 last:border-b-0 sm:grid-cols-[1fr_5rem_5rem] sm:items-center sm:gap-4"
							>
								<div>
									<p className="font-medium text-gray-900">{item.title}</p>
									<p className="mt-1 text-sm text-gray-500">{item.description}</p>
									{item.mandatory && (
										<p className="mt-1 text-xs font-medium text-purple-600">{item.mandatory}</p>
									)}
								</div>
								{['email', 'push'].map(channel => {
									const mandatory = item.key === 'system' && channel === 'email';
									const disabled =
										mandatory ||
										(item.key === 'communications' &&
											channel === 'email' &&
											!preferences.communications.push);

									return (
										<label
											key={channel}
											className="flex items-center justify-between text-sm text-gray-600 sm:justify-center"
											title={mandatory ? 'This notification is mandatory' : undefined}
										>
											<span className="sm:hidden">{channel === 'email' ? 'Email' : 'Push'}</span>
											<input
												type="checkbox"
												checked={preferences[item.key][channel]}
												onChange={() => toggle(item.key, channel)}
												disabled={disabled}
												className="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500 disabled:cursor-not-allowed disabled:opacity-60"
											/>
										</label>
									);
								})}
							</div>
						))}
					</div>
				</section>
			))}
		</div>
	);
};

export default NotificationSettings;
