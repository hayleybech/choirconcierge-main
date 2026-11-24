import EnsembleBadge from '../../components/EnsembleBadge';

export function createDocumentFilterDescriptors(ensembles = [], userEnsemblesCount = 0) {
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
		{
			name: 'ensembles.id',
			label: 'Ensemble',
			type: 'choice',
			multiple: true,
			defaultValue: [],
			visible: userEnsemblesCount > 1,
			options: ensembles.map(({ id, name }) => ({ id, name })),
			allValuesLabel: 'All',
			renderOption: (option, compact) => <EnsembleBadge ensemble={option} compact={compact} />,
			showActions: true,
			showDefaultMarker: true,
		},
	];
}
