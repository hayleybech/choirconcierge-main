import React from 'react';
import classNames from "../../classNames";

const TimeInput = ({ name, value, updateFn, hasErrors, disabled }) => (
    <div className="mt-1">
        <input
            type="time"
            name={name}
            id={name}
            className={classNames('' +
                'shadow-sm focus:outline-none block w-full sm:text-sm rounded-md',
                hasErrors
                    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                    : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500',
				disabled && 'bg-gray-200 text-gray-700 border-gray-300',
            )}
            onChange={e => updateFn(e.target.value)}
            value={value}
			disabled={disabled}
            step={5*60}
        />
    </div>
);

export default TimeInput;