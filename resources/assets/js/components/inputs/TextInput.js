import React from 'react';
import classNames from '../../classNames';

const TextInput = ({name, type = 'text', value, updateFn, hasErrors, otherProps}) => (
    <div className="mt-1">
        <input
            type={type}
            name={name}
            id={name}
            className={classNames('' +
                'shadow-sm focus:outline-none block w-full sm:text-sm rounded-md',
                hasErrors
                    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                    : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500'
            )}
            value={value}
            onChange={e => updateFn(e.target.value)}
            {...otherProps}
        />
    </div>
);

export default TextInput;