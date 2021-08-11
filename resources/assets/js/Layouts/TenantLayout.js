import React from 'react';
import {Link} from "@inertiajs/inertia-react";

const TenantLayout = ({children}) =>  (
    <div>
        <div className="w-50">
            <a href="/">
                <img src="/img/logo.svg" alt="Choir Concierge" />
            </a>
            <div className="text-red-500">testasg</div>
            <Link  href={route('singers.index')}>singers</Link>
        </div>

        <div>{children}</div>
    </div>
);

export default TenantLayout;