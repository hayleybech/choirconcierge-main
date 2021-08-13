import React from 'react'
import Layout from "../../Layouts/Layout";

const Show = () => (
    <>
        <h1>Welcome</h1>
    </>
);

Show.layout = page => <Layout children={page} title="Dashboard" />

export default Show;