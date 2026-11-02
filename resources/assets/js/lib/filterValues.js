export const areFilterValuesEqual = (value, defaultValue) => {
	if (Array.isArray(defaultValue)) {
		const normalize = values => (Array.isArray(values) ? values.map(String).sort() : []);

		return JSON.stringify(normalize(value)) === JSON.stringify(normalize(defaultValue));
	}

	return String(value ?? '') === String(defaultValue ?? '');
};
