import React, { useEffect, useRef } from 'react';
import Icon from './Icon';
import Button from './inputs/Button';
import SectionSubtitle from './SectionSubtitle';
import useRoute from '../hooks/useRoute';

const Filters = ({ routeName, routeParams, form: { submit, data, setData }, render }) => {
	const { route } = useRoute();

	const firstUpdate = useRef(true);
	useEffect(() => {
		if (firstUpdate.current) {
			firstUpdate.current = false;
			return;
		}

		submit();
	}, [data]);

	return (
		<form onSubmit={submit}>
			<SectionSubtitle className="flex justify-between items-center">
				Filter{' '}
				<a
					className="text-xs text-purple-600 hover:text-purple-800 font-medium underline hover:no-underline"
					href={route(routeName, routeParams)}
				>
					Clear All
				</a>
			</SectionSubtitle>

			<div className="flex flex-col items-stretch space-y-4 mb-4">
				{render(data, setData)}
			</div>
		</form>
	);
};

export default Filters;
