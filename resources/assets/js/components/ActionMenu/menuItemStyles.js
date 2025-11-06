import classNames from '../../classNames';

const menuItemStyles = (variant = 'default', active = false, extra) => classNames(
    'block w-full text-left px-4 py-2 text-sm',
    active ? 'bg-gray-100' : '',
    variant === 'danger-outline' ? 'text-red-500' : '',
    variant === 'success-solid' ? 'text-emerald-500' : '',
    variant !== 'danger-outline' && variant !== 'success-solid' ? 'text-gray-700' : '',
    extra
);

export default menuItemStyles;