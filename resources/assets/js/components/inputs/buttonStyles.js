import classNames from '../../classNames';

const buttonStyles = (variant = 'secondary', disabled, extra) => classNames(
    'inline-flex justify-center items-center py-2 px-4 border shadow-sm text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500',
    variant === 'primary' ? 'bg-purple-600 border-transparent text-white hover:bg-purple-700' : '',
    variant === 'secondary' ? 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100' : '',
    variant === 'danger' ? 'bg-white border-red-300 text-red-500 hover:bg-red-100' : '',
    disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '',
    extra
);

export default buttonStyles;