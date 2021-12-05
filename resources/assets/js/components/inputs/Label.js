import React from 'react';

const Label = ({label, forInput}) => (
    <label htmlFor={forInput} className="block text-sm text-gray-700">
        {label}
    </label>
);

export default Label;