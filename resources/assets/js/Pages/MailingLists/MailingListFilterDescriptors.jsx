import { createMailTypeFilterDescriptor } from '../../components/Filters/MailTypeFilterDescriptor';

export function createMailingListFilterDescriptors() {
	return [
		{
			name: 'search',
			label: 'Search',
			type: 'text',
			defaultValue: '',
			placeholder: 'Search title or address...',
			showActions: true,
			showDefaultMarker: true,
		},
		createMailTypeFilterDescriptor({
			name: 'list_type',
			label: 'Type',
			options: [
				{ id: 'chat', name: 'Chat' },
				{ id: 'public', name: 'Public' },
				{ id: 'distribution', name: 'Mailout' },
			],
		}),
	];
}
