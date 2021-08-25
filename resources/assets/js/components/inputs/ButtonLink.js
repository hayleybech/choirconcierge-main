import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import buttonStyles from "./buttonStyles";

const ButtonLink = ({ href, primary, children }) => (
    <Link href={href} className={buttonStyles(primary)}>{children}</Link>
);

export default ButtonLink;