export function createRiserStackFilterDescriptors(ensembles, userEnsemblesCount) {
	return [
		{
			name: 'search',
			label: 'Search',
			type: 'text',
			defaultValue: '',
			placeholder: 'Search by title...',
		},
		...(userEnsemblesCount > 1
			? [
					{
						name: 'ensembles.id',
						label: 'Ensemble',
						type: 'choice',
						multiple: true,
						defaultValue: [],
						options: ensembles.map(ensemble => ({ id: ensemble.id, name: ensemble.name })),
						allValuesLabel: 'All',
						showActions: true,
						showDefaultMarker: true,
					},
			  ]
			: []),
	];
}
