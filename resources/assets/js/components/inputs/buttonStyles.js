import classNames from '../../classNames';

const buttonStyles = (variant = 'secondary', size = 'md', disabled, extra) => classNames(
    extra,
    'inline-flex justify-center items-center gap-x-1.5 border shadow-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500',
    variant === 'primary' ? 'bg-purple-600 border-transparent text-white hover:bg-purple-700' : '',
    variant === 'secondary' ? 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100' : '',
    variant === 'danger-outline' ? 'bg-white border-red-300 text-red-500 hover:bg-red-100' : '',
    variant === 'danger-solid' ? 'bg-red-600 border-transparent text-white hover:bg-red-700' : '',
    variant === 'danger-clear' ? 'border-transparent shadow-none text-red-500 hover:text-red-700' : '',
    variant === 'warning-outline' ? 'bg-white border-amber-300 text-amber-500 hover:bg-amber-100' : '',
    variant === 'warning-solid' ? 'bg-amber-600 border-transparent text-white hover:bg-amber-700' : '',
    variant === 'success-outline' ? 'bg-white border-emerald-300 text-emerald-500 hover:bg-emerald-100' : '',
    variant === 'success-solid' ? 'bg-emerald-600 border-transparent text-white hover:bg-emerald-700' : '',
    variant === 'clear' ? 'border-transparent shadow-none text-gray-700 hover:text-purple-500' : '',
    disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '',
    size === 'md' ? 'py-2 px-4 text-md rounded-md' : '',
    size === 'sm' && (variant !== 'clear' && variant !== 'danger-clear') ? 'py-1.5 px-3 text-sm rounded' : '',
    size === 'sm' && (variant === 'clear' || variant === 'danger-clear') ? 'py-1.5 px-1.5 text-sm rounded' : '',
    size === 'xs' ? 'py-1 px-1.5 text-xs rounded-sm' : '',
);

export default buttonStyles;