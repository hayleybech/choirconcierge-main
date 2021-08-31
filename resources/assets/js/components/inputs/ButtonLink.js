import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import buttonStyles from "./buttonStyles";

const ButtonLink = ({ href, variant = 'secondary', className, children, disabled, ...otherProps }) => (
    <Link href={href} className={buttonStyles(variant, disabled, className)} {...otherProps}>{children}</Link>
);

export default ButtonLink;