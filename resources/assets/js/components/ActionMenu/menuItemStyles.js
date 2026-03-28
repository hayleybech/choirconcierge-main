import classNames from '../../classNames';

const menuItemStyles = (variant = 'default', active = false, extra = '', size = 'sm') =>
	classNames(
		'block w-full text-left',
		active ? 'bg-gray-100' : '',
		size === 'sm' && 'py-2 text-sm px-4',
		size === 'xs' && 'py-1.5 text-xs px-3',
		variant === 'danger-outline' ? 'text-red-500' : '',
		variant === 'success-solid' ? 'text-emerald-500' : '',
		variant !== 'danger-outline' && variant !== 'success-solid' ? 'text-gray-700' : '',
		extra
	);

export default menuItemStyles;