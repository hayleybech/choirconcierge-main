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
		{
			name: 'list_type',
			label: 'Type',
			type: 'choice',
			multiple: true,
			defaultValue: [],
			showActions: true,
			showDefaultMarker: true,
			drawerAllValuesLabel: 'All',
			options: [
				{ id: 'chat', name: 'Chat' },
				{ id: 'public', name: 'Public' },
				{ id: 'distribution', name: 'Mailout' },
			],
		},
	];
}
