import React from 'react'
import { Head } from '@inertiajs/inertia-react'

const AppHead = ({ title, children }) => (
    <Head>
        <title>{title ? `${title} | Choir Concierge` : 'Choir Concierge'}</title>
        {children}
    </Head>
);

export default AppHead;