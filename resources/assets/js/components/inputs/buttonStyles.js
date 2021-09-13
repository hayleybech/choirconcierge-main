import classNames from '../../classNames';

const buttonStyles = (variant = 'secondary', size = 'md', disabled, extra) => classNames(
    'inline-flex justify-center items-center border shadow-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500',
    variant === 'primary' ? 'bg-purple-600 border-transparent text-white hover:bg-purple-700' : '',
    variant === 'secondary' ? 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100' : '',
    variant === 'danger-outline' ? 'bg-white border-red-300 text-red-500 hover:bg-red-100' : '',
    variant === 'danger-solid' ? 'bg-red-600 border-transparent text-white hover:bg-red-700' : '',
    disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '',
    size === 'md' ? 'py-2 px-4 text-md' : '',
    size === 'sm' ? 'py-1.5 px-3 text-sm' : '',
    size === 'xs' ? 'py-1 px-2 text-xs' : '',
    extra
);

export default buttonStyles;