export function createDocumentFilterDescriptors() {
	return [
		{
			name: 'title',
			label: 'Title',
			type: 'text',
			defaultValue: '',
			placeholder: 'Search folders or documents...',
			showActions: true,
			showDefaultMarker: true,
		},
	];
}
