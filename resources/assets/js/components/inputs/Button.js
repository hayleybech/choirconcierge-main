import React from 'react';
import buttonStyles from "./buttonStyles";

const Button = ({ variant = 'secondary', size = 'md', className, children, disabled, ...otherProps }) => (
    <button
        className={buttonStyles(variant, size, disabled, className)}
        disabled={disabled}
        {...otherProps}
    >
        {children}
    </button>
);

export default Button;