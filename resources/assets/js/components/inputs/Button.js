import React from 'react';
import buttonStyles from "./buttonStyles";

const Button = ({ primary, className, children, disabled, ...otherProps }) => (
    <button
        className={buttonStyles(primary, disabled, className)}
        disabled={disabled}
        {...otherProps}
    >
        {children}
    </button>
);

export default Button;