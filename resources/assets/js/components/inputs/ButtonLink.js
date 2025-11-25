import React from 'react';
import {Link} from "@inertiajs/react";
import buttonStyles from "./buttonStyles";

const ButtonLink = React.forwardRef(({ href, variant = 'secondary', size = 'md', className, children, disabled, onClick, external = false, ...otherProps }, ref) => (
    external
        ? <a href={href} onClick={onClick} className={buttonStyles(variant, size, disabled, className)} ref={ref} {...otherProps}>{children}</a>
        : <Link href={href} onClick={onClick} className={buttonStyles(variant, size, disabled, className)} ref={ref} {...otherProps}>{children}</Link>
));

export default ButtonLink;