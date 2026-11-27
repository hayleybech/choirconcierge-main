import React, { useContext, useEffect, useRef, useState } from 'react';
import { useMediaQuery } from 'react-responsive';
import SectionSubtitle from '../SectionSubtitle';
import useRoute from '../../hooks/useRoute';
import { FilterDrawerActionsContext } from './FilterDrawer';
import ButtonLinkV2 from '../inputs/ButtonLinkV2';
import FilterFieldRenderer from './FilterFieldRenderer';
import {
	getClearedFilterValue,
	getDefaultFilterData,
	getDefaultFilterValue,
	getFilterData,
	getFilterValue,
} from './utils';

const Filters = ({ routeName, routeParams, form, filters, render, autoSubmit = true, showClearAll = true }) => {
	const { route } = useRoute();
	const { submit, data, setData, defaultFilterData, searchableFilterNames = [] } = form;
	const registerDrawerActions = useContext(FilterDrawerActionsContext);
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });
	const isStagedMobile = !isDesktop && autoSubmit;
	const [draftData, setDraftData] = useState(data);
	const [shouldSubmitDraft, setShouldSubmitDraft] = useState(false);

	const firstUpdate = useRef(true);
	const isDesktopRef = useRef(isDesktop);
	const applyDraftRef = useRef(null);
	const clearDraftRef = useRef(null);
	const hasDefaultFilterData = Boolean(defaultFilterData);

	isDesktopRef.current = isDesktop;

	const updateFilterData = (name, value) => {
		if (!isStagedMobile) {
			if (typeof name === 'object' && name !== null) {
				Object.entries(name).forEach(([filterName, filterValue]) => setData(filterName, filterValue));
			} else {
				setData(name, value);
			}
			return;
		}

		setDraftData(previousData =>
			typeof name === 'object' && name !== null
				? { ...previousData, ...name }
				: { ...previousData, [name]: value }
		);
	};
	const applyDraftFilters = (filterData = draftData, submitRegardless = false) => {
		setData(filterData);
		setShouldSubmitDraft(submitRegardless || !searchableFilterNames.some(name => data[name] !== filterData[name]));
	};
	const clearDraftFilters = () => {
		const defaultValues = filters
			? getDefaultFilterData(filters)
			: Object.fromEntries(
					Object.entries(defaultFilterData ?? {}).map(([name, value]) => [
						name,
						Array.isArray(value) ? [...value] : value,
					])
			  );
		const clearedData = { ...draftData, ...defaultValues };

		setDraftData(clearedData);
		applyDraftFilters(clearedData, true);
	};
	const changeDescriptorValue = (filter, value) => {
		if (filter.onChange) {
			filter.onChange(value, {
				...form,
				data: isStagedMobile ? draftData : data,
				setData: updateFilterData,
			});
			return;
		}

		updateFilterData(getFilterData(filter, value));
	};
	const clearDescriptorValue = filter => changeDescriptorValue(filter, getClearedFilterValue(filter));
	const resetDescriptorValue = filter => changeDescriptorValue(filter, getDefaultFilterValue(filter));
	const drawerFilters = filters
		?.filter(
			filter =>
				filter.visible !== false &&
				(filter.toolbarMode === 'pill' || !['date', 'text'].includes(filter.type))
		)
		.map((filter, index) => ({ filter, index }))
		.sort(
			(first, second) => (first.filter.drawerOrder ?? first.index) - (second.filter.drawerOrder ?? second.index)
		)
		.map(({ filter }) => filter);

	applyDraftRef.current = applyDraftFilters;
	clearDraftRef.current = clearDraftFilters;

	useEffect(() => {
		setDraftData(data);
	}, [data]);

	useEffect(() => {
		if (!registerDrawerActions || !isStagedMobile) {
			return undefined;
		}

		registerDrawerActions({
			apply: () => applyDraftRef.current?.(),
			clearAll: showClearAll && hasDefaultFilterData ? () => clearDraftRef.current?.() : null,
		});

		return () => registerDrawerActions(null);
	}, [registerDrawerActions, isStagedMobile, showClearAll, hasDefaultFilterData]);

	useEffect(() => {
		if (firstUpdate.current) {
			firstUpdate.current = false;
			return;
		}

		if (!autoSubmit || !isDesktopRef.current) {
			return;
		}

		submit();
	}, [autoSubmit, data]);

	useEffect(() => {
		if (!shouldSubmitDraft) {
			return;
		}

		setShouldSubmitDraft(false);
		submit(undefined, { preserveState: true });
	}, [shouldSubmitDraft, data, submit]);

	const handleSubmit = event => {
		event.preventDefault();

		if (!isStagedMobile) {
			submit(event);
		}
	};

	return (
		<form onSubmit={handleSubmit}>
			<SectionSubtitle>Filter </SectionSubtitle>

			{showClearAll && !isStagedMobile && (
				<ButtonLinkV2
					intent="secondary"
					weight="outline"
					size="sm"
					href={route(routeName, routeParams)}
					className="flex! w-full mb-2"
				>
					Clear All
				</ButtonLinkV2>
			)}

			<div className="flex flex-col items-stretch space-y-4 mb-4">
				{drawerFilters
					? drawerFilters.map(filter => (
							<FilterFieldRenderer
								key={filter.name ?? filter.start_name}
								filter={filter}
								value={getFilterValue(filter, isStagedMobile ? draftData : data)}
								onChange={value => changeDescriptorValue(filter, value)}
								onClear={() => clearDescriptorValue(filter)}
								onReset={() => resetDescriptorValue(filter)}
								variant="drawer"
							/>
					  ))
					: render(isStagedMobile ? draftData : data, updateFilterData)}
			</div>
		</form>
	);
};

export default Filters;
