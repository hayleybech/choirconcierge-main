import React from 'react';

const CheckboxGroup = ({name, options, value, updateFn}) => (
    <div className="mt-4 grid grid-cols-2 md:flex md:flex-wrap">
        {options.map((option, key) => (
            <React.Fragment key={key}>
                <div className="relative flex items-start mr-8 mb-4">
                    <div className="flex items-center h-5">
                        <input type="checkbox"
                           id={`${name}_${option.id}`}
                           name={`${name}[]`}
                           value={option.id}
                           className="focus:ring-purple-500 h-4 w-4 text-purple-600 border-gray-300 rounded"
                            checked={value.includes(option.id)}
                           onChange={e => updateFn(
                               e.target.checked
                                   ? [...new Set(value).add(option.id)]
                                   : [...new Set(value).delete(option.id)]
                           )}
                        />
                    </div>
                    <div className="ml-3 text-sm">
                        <label htmlFor={`${name}_${option.id}`} className="font-medium text-gray-700">
                            {option.name}
                        </label>
                    </div>
                </div>
            </React.Fragment>
        ))}
    </div>
);

export default CheckboxGroup;