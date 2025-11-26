import { useCallback } from 'react';
import useCookie from 'react-use-cookie';

const useMetricImperialPreference = () => {
	const [_showImperial, _setShowImperial] = useCookie('show-imperial-measurements', 'no');

	const showImperial = _showImperial === 'yes';
	const setShowImperial = useCallback(val => _setShowImperial(val ? 'yes' : 'no'), []);

	return [showImperial, setShowImperial];
};

export default useMetricImperialPreference;
