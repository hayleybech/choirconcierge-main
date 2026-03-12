import {useForm} from "@inertiajs/react";
import collect from "collect.js";
import useRoute from "./useRoute";
import * as qs from 'qs';

const useSortFilterForm = (routeParams, filters, sorts, transforms = () => {}) => {
    const { route } = useRoute();

    const { data, setData, get, transform } = useForm({
        sort: getSort(sorts.find(option => option.default)?.id),
        sortDir: getSortDir(),
        ...getFilters(filters),
    });

    function submit(e) {
        e?.preventDefault();

		// if routeParams is array, pull out params
		// @todo refactor this hook to accept the return value from route()
		if(typeof routeParams == 'object') {
			get(route(routeParams[0], routeParams[1]));
		} else {
        	get(route(routeParams));
		}

    }

    transform((data) => ({
        sort: data.sortDir === 'desc' ? `-${data.sort}` : data.sort,
        filter: {
            ...(collect(data).except(['sort', 'sortDir']).items),
            ...transforms(data),
        }
    }));

    return { data, setData, submit };
};

export default useSortFilterForm;


function getFilters(filters){
    const params = qs.parse(location.search, {ignoreQueryPrefix: true});

    return collect(filters)
        .mapWithKeys(({ name, multiple, multipleBool, defaultValue }) => [
            name,
            multiple ? getFilterMultiple(params, name, defaultValue, multipleBool) : getFilterSingle(params, name, defaultValue)
        ]).items;
}

const getFilterSingle = (params, name, defaultValue = '') => params.filter?.[name] ?? defaultValue;

const getFilterMultiple = (params, name, defaultValue = [], bool) => !!params.filter && name in params.filter
    ? params.filter?.[name].map(value => {
        if (bool) {
            return value === 'true';
        }

        const parsed = parseInt(value);

        return isNaN(parsed) || parsed.toString() !== value.toString() ? value : parsed;
    })
    : defaultValue;

function getSort(defaultSort) {
    const params = new qs.parse(location.search, {ignoreQueryPrefix: true});
    return 'sort' in params
        ? params.sort.replace(/^-/, '')
        : defaultSort;
}

function getSortDir() {
    const params = new qs.parse(location.search);
    return params.sort?.startsWith('-') ? 'desc' : 'asc';
}