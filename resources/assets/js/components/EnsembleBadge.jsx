import React from 'react';
import Badge from './Badge';

const EnsembleBadge = ({ ensemble, compact = false, className = '' }) => (
	<Badge colour={`bg-purple-100 text-purple-800 ${className}`} compact={compact}>
		{ensemble.name}
	</Badge>
);

export default EnsembleBadge;