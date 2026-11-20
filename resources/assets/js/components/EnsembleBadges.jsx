import React from 'react';
import Badge from './Badge';
import EnsembleBadge from './EnsembleBadge';
 
const EnsembleBadges = ({ ensembles, className = 'flex flex-wrap gap-1', badgeClassName = '' }) => (
	<div className={className}>
		{ensembles?.length > 0 ? (
			ensembles.map(ensemble => (
				<EnsembleBadge key={ensemble.id} ensemble={ensemble} className={badgeClassName} />
			))
		) : (
			<Badge colour={`bg-gray-100 text-gray-800 ${badgeClassName}`}>All Ensembles</Badge>
		)}
	</div>
);

export default EnsembleBadges;