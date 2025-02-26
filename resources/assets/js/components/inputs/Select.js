import React, {useEffect} from 'react';
import classNames from '../../classNames';

const Select = ({ name, options, hasErrors, value, updateFn }) => {
    useEffect(() => {
        if(value === null) {
            updateFn(options[0].key);
        }
    }, [value]);

    return (
        <div className="mt-1">
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
                onChange={e => updateFn(e.target.value)}
            >
                {options.map(({label, key}) => (
                    <option value={key} key={key}>{label}</option>
                ))}
            </select>
        </div>
    );
}

export default Select;