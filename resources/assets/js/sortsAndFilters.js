import collect from "collect.js";

export function getFilters(filters){
    const params = new URLSearchParams(location.search);

    return collect(filters)
        .mapWithKeys(({ name, multiple, multipleBool, defaultValue }) => [
            name,
            multiple ? getMultiple(params, name, defaultValue, multipleBool) : getSingle(params, name, defaultValue)
        ]).items;
}

const getSingle = (params, name, defaultValue = '') => params.get(`filter[${name}]`) ?? defaultValue;

const getMultiple = (params, name, defaultValue = [], bool) => params.has(`filter[${name}][]`)
    ? params.getAll(`filter[${name}][]`).map(value => bool ? value === 'true' : parseInt(value))
    : defaultValue;