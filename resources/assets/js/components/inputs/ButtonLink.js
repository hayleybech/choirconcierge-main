import React from 'react';
import {Link} from "@inertiajs/react";
import buttonStyles from "./buttonStyles";

const ButtonLink = ({ href, variant = 'secondary', size = 'md', className, children, disabled, onClick, external = false, ...otherProps }) => (
    external
        ? <a href={href} onClick={onClick} className={buttonStyles(variant, size, disabled, className)} {...otherProps}>{children}</a>
        : <Link href={href} onClick={onClick} className={buttonStyles(variant, size, disabled, className)} {...otherProps}>{children}</Link>
);

export default ButtonLink;