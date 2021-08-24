import React from 'react';
import classNames from '../../classnames';

const Date = ({ name, placeholder, hasErrors }) => (
    <div className="mt-1 relative rounded-md shadow-sm">
        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i className="far fa-fw fa-calendar-day text-gray-400" />
        </div>
        <input
            id={name}
            name={name}
            type="date"
            placeholder={placeholder}
            className={classNames('' +
                'shadow-sm focus:outline-none block w-full sm:text-sm rounded-md pl-10',
                hasErrors
                    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                    : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500'
            )}
        />
    </div>
);

export default Date;