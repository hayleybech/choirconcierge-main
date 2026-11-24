import React from 'react';
import Icon from '../../../components/Icon';
import MailingListSelect from '../../../components/inputs/MailingListSelect';

const renderMailingListSelect = ({ value, onChange, options, surface }) => {
	const selectedValues = Array.isArray(value) ? value : value ? [value] : [];
	const selectOptions = options.map(option => ({
		label: option.name,
		value: option.id,
		email: option.email,
		type_icon: option.type_icon,
	}));

	return (
		<MailingListSelect
			options={selectOptions}
			value={selectOptions.filter(option => selectedValues.includes(option.value))}
			multiple
			isClearable={false}
			constrainWidth
			truncateSelectedLabels
			className={`mt-1 mb-3 w-full min-w-0 max-w-full${surface === 'drawer' ? '' : ' px-3'}`}
			updateFn={onChange}
		/>
	);
};

const renderToolbarMailingListSelect = ({ value, onChange, options }) => {
	const selectedValues = Array.isArray(value) ? value : value ? [value] : [];
	const selectOptions = options.map(option => ({
		label: option.name,
		value: option.id,
		email: option.email,
		type_icon: option.type_icon,
	}));

	return (
		<MailingListSelect
			options={selectOptions}
			value={selectOptions.filter(option => selectedValues.includes(option.value))}
			multiple
			isClearable
			prefixLabel="To:"
			ariaLabel="To"
			toolbarPill
			className="w-auto shrink-0"
			updateFn={onChange}
		/>
	);
};

export function createMailingListMailLogFilterDescriptors({ mailingLists, mailTypes, defaultMailTypes, inboxEnabled }) {
	const mailingListOptions = mailingLists.map(({ title, email, type_icon }) => ({
		id: email,
		name: title,
		email,
		type_icon,
	}));

	return [
		{
			name: 'subject',
			label: 'Subject',
			type: 'text',
			defaultValue: '',
			showActions: true,
			showDefaultMarker: true,
		},
		{
			name: 'to',
			label: 'To',
			type: 'choice',
			multiple: true,
			toolbarMode: 'select',
			toolbarOnlyWhenNonDefault: true,
			defaultValue: '',
			showActions: true,
			showDefaultMarker: true,
			drawerAllValuesLabel: 'All',
			options: mailingListOptions,
			renderOption: (option, compact) => (
				<span className="inline-flex min-w-0 items-center gap-1.5">
					<Icon icon={option.type_icon} size={compact ? 'text-xs' : 'text-sm'} />
					<span className="truncate">{option.name}</span>
					{!compact && <span className="text-xs text-gray-500">{option.email}</span>}
				</span>
			),
			renderField: renderMailingListSelect,
			renderToolbarField: renderToolbarMailingListSelect,
		},
		{
			name: 'from',
			label: 'From',
			type: 'text',
			toolbarMode: 'pill',
			toolbarOnlyWhenNonDefault: true,
			defaultValue: '',
			showActions: true,
			showDefaultMarker: true,
		},
		{
			name: 'type',
			label: 'Mail Type',
			type: 'choice',
			multiple: true,
			defaultValue: defaultMailTypes,
			showActions: true,
			showDefaultMarker: true,
			drawerAllValuesLabel: 'All',
			options: mailTypes,
		},
		{
			name: 'read',
			label: 'Read Status',
			type: 'choice',
			defaultValue: '',
			visible: inboxEnabled,
			showActions: true,
			showDefaultMarker: true,
			emptyOptionLabel: 'All',
			options: [
				{ id: 'read', name: 'Read', icon: 'envelope-open-text' },
				{ id: 'unread', name: 'Unread', icon: 'envelope' },
			],
			renderOption: option => (
				<span className="inline-flex items-center gap-1.5">
					<Icon icon={option.icon} />
					{option.name}
				</span>
			),
		},
	];
}
