import React from 'react';
import classNames from '../../classNames';

const TextareaInput = ({name, type = 'text', value, updateFn, hasErrors, rows = 5, otherProps}) => (
    <div className="mt-1">
        <textarea
            name={name}
            id={name}
            className={classNames('' +
                'shadow-sm focus:outline-none block w-full sm:text-sm rounded-md',
                hasErrors
                    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                    : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500'
            )}
            rows={rows}
            value={value}
            onChange={e => updateFn(e.target.value)}
            {...otherProps}
        />
    </div>
);

export default TextareaInput;