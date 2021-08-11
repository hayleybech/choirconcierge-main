import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";

const Show = () => (
    <>
        <h1>Welcome</h1>
    </>
);

Show.layout = page => <TenantLayout children={page} title="Dashboard" />

export default Show;