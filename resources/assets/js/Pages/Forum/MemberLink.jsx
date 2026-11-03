import React from 'react';
import { Link } from '@inertiajs/react';
import useRoute from '../../hooks/useRoute';

const MemberLink = ({ membershipId, children, className = '' }) => {
	const { route } = useRoute();

	if (!membershipId) {
		return <span className={className}>{children}</span>;
	}

	return (
		<Link
			href={route('singers.show', { singer: membershipId })}
			className={`text-purple-700 hover:text-purple-900 hover:underline focus-visible:underline ${className}`}
		>
			{children}
		</Link>
	);
};

export default MemberLink;
