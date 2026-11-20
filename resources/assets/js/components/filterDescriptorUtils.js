export function getFilterName(filter) {
	return filter.name ?? filter.start_name;
}

export function isMultipleFilter(filter) {
	return Boolean(filter.multiple || filter.type === 'multiple');
}

export function getFilterValue(filter, data) {
	if (filter.type === 'daterange') {
		return {
			from: data[filter.start_name] ?? '',
			to: data[filter.end_name] ?? '',
		};
	}

	return data[filter.name];
}

export function getDefaultFilterValue(filter) {
	if (filter.type === 'daterange') {
		return {
			from: filter.defaultValue?.from ?? '',
			to: filter.defaultValue?.to ?? '',
		};
	}

	const defaultValue = filter.defaultValue ?? (isMultipleFilter(filter) ? [] : '');

	return cloneFilterValue(defaultValue);
}

export function getDefaultFilterData(filters) {
	return filters.filter(filter => filter.visible !== false).reduce((filterData, filter) => {
		Object.assign(filterData, getFilterData(filter, getDefaultFilterValue(filter)));
		return filterData;
	}, {});
}

export function getFilterData(filter, value) {
	if (filter.type === 'daterange') {
		return {
			[filter.start_name]: formatDateInputValue(value?.from),
			[filter.end_name]: formatDateInputValue(value?.to),
		};
	}

	return { [filter.name]: cloneFilterValue(value) };
}

export function getClearedFilterValue(filter) {
	if (Object.prototype.hasOwnProperty.call(filter, 'clearValue')) {
		return cloneFilterValue(filter.clearValue);
	}

	if (filter.type === 'daterange') {
		return { from: '', to: '' };
	}

	if (isMultipleFilter(filter)) {
		return hasSelectedFilterValue(filter, getDefaultFilterValue(filter)) ? [''] : [];
	}

	return '';
}

export function hasSelectedFilterValue(filter, value) {
	if (filter.type === 'daterange') {
		return Boolean(value?.from || value?.to);
	}

	if (isMultipleFilter(filter)) {
		return (
			Array.isArray(value) &&
			value.some(selectedValue => selectedValue !== null && selectedValue !== undefined && selectedValue !== '')
		);
	}

	return value !== null && value !== undefined && String(value).trim() !== '';
}

export function isFilterValueNonDefault(filter, value) {
	return normalizeFilterValue(filter, value) !== normalizeFilterValue(filter, getDefaultFilterValue(filter));
}

export function getSelectedFilterOptions(filter, value) {
	if (['text', 'date', 'daterange'].includes(filter.type)) {
		return [];
	}

	const selectedValues = isMultipleFilter(filter) ? (Array.isArray(value) ? value : []) : [value];
	const options = getFilterOptions(filter);

	return selectedValues
		.filter(selectedValue => selectedValue !== null && selectedValue !== undefined && selectedValue !== '')
		.map(
			selectedValue =>
				options.find(option => String(option.id) === String(selectedValue)) ?? {
					id: selectedValue,
					name: String(selectedValue),
				}
		);
}

export function getFilterOptions(filter, context) {
	const options = typeof filter.options === 'function' ? filter.options(context) : filter.options;

	return options ?? [];
}

export function formatDateInputValue(value) {
	if (!value) {
		return '';
	}

	const stringValue = String(value);
	const isoDate = stringValue.match(/^\d{4}-\d{2}-\d{2}/)?.[0];

	if (isoDate) {
		return isoDate;
	}

	const date = value instanceof Date ? value : new Date(value);

	if (Number.isNaN(date.getTime())) {
		return '';
	}

	const month = String(date.getMonth() + 1).padStart(2, '0');
	const day = String(date.getDate()).padStart(2, '0');

	return `${date.getFullYear()}-${month}-${day}`;
}

function normalizeFilterValue(filter, value) {
	if (filter.type === 'daterange') {
		return `${formatDateInputValue(value?.from)}|${formatDateInputValue(value?.to)}`;
	}

	if (isMultipleFilter(filter)) {
		return (Array.isArray(value) ? value : [])
			.map(selectedValue => String(selectedValue))
			.sort()
			.join(',');
	}

	return String(value ?? '');
}

function cloneFilterValue(value) {
	if (Array.isArray(value)) {
		return [...value];
	}

	if (value && typeof value === 'object') {
		return { ...value };
	}

	return value;
}
