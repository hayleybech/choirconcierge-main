import React from 'react';
import Badge from './Badge';

const EnsembleBadges = ({ ensembles, className = 'flex flex-wrap gap-1', badgeClassName = '' }) => (
	<div className={className}>
		{ensembles?.length > 0 ? (
			ensembles.map(ensemble => (
				<Badge key={ensemble.id} colour={`bg-purple-100 text-purple-800 ${badgeClassName}`}>
					{ensemble.name}
				</Badge>
			))
		) : (
			<Badge colour={`bg-gray-100 text-gray-800 ${badgeClassName}`}>All Ensembles</Badge>
		)}
	</div>
);

export default EnsembleBadges;