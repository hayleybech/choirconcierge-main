import React, { useEffect, useRef } from 'react';
import SectionSubtitle from './SectionSubtitle';
import useRoute from '../hooks/useRoute';
import ButtonLinkV2 from './inputs/ButtonLinkV2';

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

			<ButtonLinkV2 intent="secondary" weight="outline" size="sm" href={route(routeName, routeParams)} className="flex! w-full mb-2">
				Clear All
			</ButtonLinkV2>

			<div className="flex flex-col items-stretch space-y-4 mb-4">{render(data, setData)}</div>
		</form>
	);
};

export default Filters;
