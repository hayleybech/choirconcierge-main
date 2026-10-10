import classNames from '../../classNames';

const menuItemStyles = (intent = 'default', active = false, extra = '', size = 'sm') =>
	classNames(
		'block w-full text-left',
		active ? 'bg-gray-100' : '',
		size === 'sm' && 'py-2 text-sm px-4',
		size === 'xs' && 'py-1.5 text-xs px-3',
		intent === 'danger' ? 'text-red-500' : '',
		intent === 'success' ? 'text-emerald-500' : '',
		intent !== 'danger' && intent !== 'success' ? 'text-gray-700' : '',
		extra
	);

export default menuItemStyles;
