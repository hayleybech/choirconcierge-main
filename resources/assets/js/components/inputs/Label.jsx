import React from 'react';

const Label = ({label, forInput, children}) => (
    <label htmlFor={forInput} className="block text-sm text-gray-700">
        {label ?? children}
    </label>
);

export default Label;