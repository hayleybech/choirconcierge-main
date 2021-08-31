import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import buttonStyles from "./buttonStyles";

const ButtonLink = ({ href, variant = 'secondary', size = 'md', className, children, disabled, ...otherProps }) => (
    <Link href={href} className={buttonStyles(variant, size, disabled, className)} {...otherProps}>{children}</Link>
);

export default ButtonLink;