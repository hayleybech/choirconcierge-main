import React from 'react';
import buttonStyles from "./buttonStyles";

const Button = ({ primary, className, children, otherProps }) => (
    <button
        className={buttonStyles(primary, className)}
        {...otherProps}
    >
        {children}
    </button>
);

export default Button;