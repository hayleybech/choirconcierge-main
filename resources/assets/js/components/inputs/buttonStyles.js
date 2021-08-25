import classNames from '../../classnames';

const buttonStyles = (primary) => classNames(
    'ml-3 inline-flex justify-center py-2 px-4 border shadow-sm text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500',
    primary
        ? 'bg-purple-600 border-transparent text-white hover:bg-purple-700'
        : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'
);

export default buttonStyles;