import React from 'react';
import classNames from '../../classNames';
 
const Label = ({label, forInput, children, className = ''}) => (
    <label htmlFor={forInput} className={classNames('block text-sm text-gray-700', className)}>
        {label ?? children}
    </label>
);

export default Label;