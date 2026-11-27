import { useForm } from '@inertiajs/react';
import collect from 'collect.js';
import useRoute from './useRoute';
import * as qs from 'qs';
import { getDefaultFilterData, getDefaultFilterValue, isMultipleFilter } from '../components/Filters/utils';

const useSortFilterForm = (routeParams, filters, sorts, transforms = () => {}) => {
	const { route } = useRoute();
	const visibleFilters = filters.filter(filter => filter.visible !== false);
	const defaultFilterData = getDefaultFilterData(visibleFilters);
	const searchableFilterNames = visibleFilters
		.filter(filter => ['date', 'text'].includes(filter.type))
		.map(filter => filter.name);

	const { data, setData, get, transform } = useForm({
		sort: getSort(sorts.find(option => option.default)?.id),
		sortDir: getSortDir(sorts.find(option => option.default)?.defaultDirection),
		...getFilters(visibleFilters),
	});

	function submit(e, options = {}) {
		e?.preventDefault();

		// if routeParams is array, pull out params
		// @todo refactor this hook to accept the return value from route()
		if (typeof routeParams == 'object') {
			get(route(routeParams[0], routeParams[1]), options);
		} else {
			get(route(routeParams), options);
		}
	}

	transform(data => ({
		sort: data.sortDir === 'desc' ? `-${data.sort}` : data.sort,
		filter: {
			...collect(data).except(['sort', 'sortDir']).items,
			...transforms(data),
		},
	}));

	return { data, setData, submit, defaultFilterData, searchableFilterNames };
};

export default useSortFilterForm;

function getFilters(filters) {
	const params = qs.parse(location.search, { ignoreQueryPrefix: true });

	return filters.reduce((filterValues, filter) => {
		const defaultValue = getDefaultFilterValue(filter);

		if (filter.type === 'daterange') {
			filterValues[filter.start_name] = getFilterSingle(params, filter.start_name, defaultValue.from);
			filterValues[filter.end_name] = getFilterSingle(params, filter.end_name, defaultValue.to);

			return filterValues;
		}

		const { name, multipleBool } = filter;
		filterValues[name] = isMultipleFilter(filter)
			? getFilterMultiple(params, name, defaultValue, multipleBool)
			: getFilterSingle(params, name, defaultValue);

		return filterValues;
	}, {});
}

const getFilterSingle = (params, name, defaultValue = '') => params.filter?.[name] ?? defaultValue;

const getFilterMultiple = (params, name, defaultValue = [], bool) =>
	!!params.filter && name in params.filter
		? [].concat(params.filter?.[name]).map(value => {
				if (bool) {
					return value === 'true';
				}

				const parsed = parseInt(value);

				return isNaN(parsed) || parsed.toString() !== value.toString() ? value : parsed;
		  })
		: defaultValue;

function getSort(defaultSort) {
	const params = new qs.parse(location.search, { ignoreQueryPrefix: true });
	return 'sort' in params ? params.sort.replace(/^-/, '') : defaultSort;
}

function getSortDir(defaultDirection = 'asc') {
	const params = new qs.parse(location.search);
	return params.sort ? (params.sort.startsWith('-') ? 'desc' : 'asc') : defaultDirection;
}
