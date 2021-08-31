import React from 'react';
import buttonStyles from "./buttonStyles";

const Button = ({ variant = 'secondary', className, children, disabled, ...otherProps }) => (
    <button
        className={buttonStyles(variant, disabled, className)}
        disabled={disabled}
        {...otherProps}
    >
        {children}
    </button>
);

export default Button;