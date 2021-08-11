import React from 'react';

const TenantLayout = ({children}) =>  (
    <div>
        <div className="w-50">
            <a href="/">
                <img src="/img/logo.svg" alt="Choir Concierge" />
            </a>
            <div className="text-red-500">testasg</div>
        </div>

        <div>{children}</div>
    </div>
);

export default TenantLayout;