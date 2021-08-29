import React from 'react';
import classNames from '../../classNames';

const Select = ({ name, options, hasErrors, value, onChange }) => (
    <select
        id={name}
        name={name}
        className={classNames('' +
            'shadow-sm focus:outline-none block w-full sm:text-sm rounded-md',
            hasErrors
                ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500'
        )}
        value={value}
        onChange={onChange}
    >
        {options.map((label, value) => (
            <option value={value} key={value}>{label}</option>
        ))}
    </select>
);

export default Select;