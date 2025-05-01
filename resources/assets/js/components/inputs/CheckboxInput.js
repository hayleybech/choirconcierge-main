import React from 'react';

const CheckboxInput = ({ id, name, value, checked, onChange }) => (
    <input
        id={id}
        name={name}
        value={value}
        checked={checked}
        onChange={onChange}
        type="checkbox"
        className="focus:ring-purple-500 h-4 w-4 text-purple-600 border-gray-300 rounded"
    />
);

export default CheckboxInput;