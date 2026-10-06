import React, { useEffect, useRef } from 'react';
import SectionSubtitle from './SectionSubtitle';
import useRoute from '../hooks/useRoute';
import ButtonLink from './inputs/ButtonLink';

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
			<SectionSubtitle>Filter </SectionSubtitle>

			<ButtonLink variant="secondary" size="xs" href={route(routeName, routeParams)} className="flex! w-full mb-2">
				Clear All
			</ButtonLink>

			<div className="flex flex-col items-stretch space-y-4 mb-4">{render(data, setData)}</div>
		</form>
	);
};

export default Filters;
