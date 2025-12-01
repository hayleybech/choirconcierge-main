import React from 'react';
import CheckboxWithLabel from "./CheckboxWithLabel";

const CheckboxGroup = ({ name, options, value, updateFn }) => (
    <div className="mt-4 grid grid-cols-2 md:flex md:flex-wrap">
        {options.map((option, key) => (
            <React.Fragment key={key}>
                <CheckboxWithLabel
                    label={option.name}
                    id={`${name}_${option.id}`}
                    name={`${name}[]`}
                    value={option.id}
                    checked={value.includes(option.id)}
                    onChange={e => updateFn(
                        e.target.checked
                            ? addToArray(option.id, value)
                            : deleteFromArray(option.id, value)
                    )}
                    className="mr-8 mb-4"
                />
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