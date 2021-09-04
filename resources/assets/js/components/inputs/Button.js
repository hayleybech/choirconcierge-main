import React from 'react';
import buttonStyles from "./buttonStyles";
import ButtonLink from "./ButtonLink";

const Button = ({ variant = 'secondary', size = 'md', href, className, children, disabled, ...otherProps }) => (
    href
        ?
        <ButtonLink href={href} variant={variant} size={size} className={className} disabled={disabled} {...otherProps}>
            {children}
        </ButtonLink>
        :
        <button
            className={buttonStyles(variant, size, disabled, className)}
            disabled={disabled}
            {...otherProps}
        >
            {children}
        </button>
);

export default Button;