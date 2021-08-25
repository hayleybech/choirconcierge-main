import React from 'react';
import buttonStyles from "./buttonStyles";

const Button = ({ primary, children, otherProps }) => (
    <button
        className={buttonStyles(primary)}
        {...otherProps}
    >
        {children}
    </button>
);

export default Button;