import React from 'react';
import { useForm } from '@inertiajs/react';
import useRoute from '../../hooks/useRoute';
import Button from '../../components/inputs/Button';
import Error from '../../components/inputs/Error';
import FormFooter from '../../components/FormFooter';

const notificationGroups = [
	{
		title: 'From My Organisations',
		description: 'Choose how you receive updates from the organisations you belong to.',
		items: [
			{
				key: 'communications',
				title: 'Communications',
				description: 'Important messages from your organisation.',
				mandatory: 'Email is required and cannot be turned off.',
			},
			{
				key: 'songs',
				title: 'Song Created/Updated',
				description: 'When an organisation song is created or updated.',
			},
			{
				key: 'events',
				title: 'Event Created/Updated',
				description: 'When an organisation event is created or updated.',
			},
			{
				key: 'documents',
				title: 'Document Created/Updated',
				description: 'When an organisation document is created or updated.',
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
				mandatory: 'Email is required and cannot be turned off.',
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

const NotificationSettings = ({ preferences, accountRoute }) => {
	const { route } = useRoute();
	const { data, setData, put, processing, errors } = useForm({ email_preferences: preferences });

	const toggle = key => {
		setData(current => ({
			...current,
			email_preferences: {
				...current.email_preferences,
				[key]: !current.email_preferences[key],
			},
		}));
	};

	const submit = event => {
		event.preventDefault();
		put(route(`${accountRoute}.notifications.update`), { preserveScroll: true });
	};

	return (
		<form className="space-y-8" onSubmit={submit}>
			<div>
				<h2 className="text-lg font-medium leading-6 text-gray-900">Notification Preferences</h2>
				<p className="mt-1 text-sm text-gray-500">
					Choose which email notifications you would like to receive. These settings apply across all your organisations.
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
						<div className="hidden border-b border-gray-200 bg-gray-50 px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:grid sm:grid-cols-[1fr_5rem] sm:gap-4">
							<div>Notification</div>
							<div className="text-center">Email</div>
						</div>
						{group.items.map(item => (
							<div
								key={item.key}
								className="grid grid-cols-1 gap-4 border-b border-gray-200 px-5 py-4 last:border-b-0 sm:grid-cols-[1fr_5rem] sm:items-center sm:gap-4"
							>
								<div>
									<p className="font-medium text-gray-900">{item.title}</p>
									<p className="mt-1 text-sm text-gray-500">{item.description}</p>
									{item.mandatory && (
										<p className="mt-1 text-xs font-medium text-purple-600">{item.mandatory}</p>
									)}
								</div>
								<label
									className="flex items-center justify-between text-sm text-gray-600 sm:justify-center"
									title={item.mandatory ? 'This notification is mandatory' : undefined}
								>
									<span className="sm:hidden">Email</span>
									<input
										type="checkbox"
										checked={data.email_preferences[item.key]}
										onChange={() => toggle(item.key)}
										disabled={!!item.mandatory}
										className="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500 disabled:cursor-not-allowed disabled:opacity-60"
									/>
								</label>
							</div>
						))}
					</div>
					{errors[`email_preferences.${group.items[0].key}`] && (
						<Error>{errors[`email_preferences.${group.items[0].key}`]}</Error>
					)}
				</section>
			))}
			<FormFooter>
				<Button variant="primary" type="submit" disabled={processing}>
					{processing ? 'Saving...' : 'Save'}
				</Button>
			</FormFooter>
		</form>
	);
};

export default NotificationSettings;
