import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import buttonStyles from "./buttonStyles";

const ButtonLink = ({ href, primary, className, children, disabled, ...otherProps }) => (
    <Link href={href} className={buttonStyles(primary, disabled, className)} {...otherProps}>{children}</Link>
);

export default ButtonLink;