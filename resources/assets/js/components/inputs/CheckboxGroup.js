import React from 'react';
import CheckboxInput from "./CheckboxInput";

const CheckboxGroup = ({name, options, value, updateFn}) => (
    <div className="mt-4 grid grid-cols-2 md:flex md:flex-wrap">
        {options.map((option, key) => (
            <React.Fragment key={key}>
                <div className="relative flex items-start mr-8 mb-4">
                    <div className="flex items-center h-5">
                        <CheckboxInput
                           id={`${name}_${option.id}`}
                           name={`${name}[]`}
                           value={option.id}
                           checked={value.includes(option.id)}
                           onChange={e => updateFn(
                               e.target.checked
                                   ? addToArray(option.id, value)
                                   : deleteFromArray(option.id, value)
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

function addToArray(item, array) {
    return [...new Set(array).add(item)];
}

function deleteFromArray(item, array) {
    let set = new Set(array);
    set.delete(item);
    return [...set];
}