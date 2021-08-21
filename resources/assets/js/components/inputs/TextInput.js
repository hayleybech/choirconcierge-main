import React from 'react';

const TextInput = ({name, type = 'text', otherProps}) => (
    <input
        type={type}
        name={name}
        id={name}
        className="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md"
        {...otherProps}
    />
);

export default TextInput;