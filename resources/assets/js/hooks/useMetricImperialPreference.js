import { usePage } from '@inertiajs/react';

const useMetricImperialPreference = () => {
	const { user } = usePage().props;
	const showImperial = user?.prefers_metric !== null && user?.prefers_metric !== undefined
		? !user.prefers_metric
		: ['LR', 'MM', 'US'].includes(user?.address_country);

	return showImperial;
};

export default useMetricImperialPreference;
