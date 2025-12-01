import React from 'react';
import classNames from "../../classNames";

const SubdomainInput = ({ name, value, updateFn, hasErrors, host, ...otherProps }) => (
    <div className="mt-1 flex rounded-md shadow-sm">
        <input
            type="text"
            name={name}
            id={name}
            value={value}
            onChange={e => updateFn(e.target.value)}
            className={classNames(
                'flex-1 min-w-0 block w-full px-3 py-2 sm:text-sm rounded-none rounded-l-md',
                hasErrors
                    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                    : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500'
            )}
            {...otherProps}
        />
        <span className="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
          .{host}
        </span>
    </div>
);

export default SubdomainInput;