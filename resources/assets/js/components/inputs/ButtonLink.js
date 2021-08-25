import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import buttonStyles from "./buttonStyles";

const ButtonLink = ({ href, primary, className, children }) => (
    <Link href={href} className={buttonStyles(primary, className)}>{children}</Link>
);

export default ButtonLink;