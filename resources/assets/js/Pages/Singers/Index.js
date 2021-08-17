import React from 'react'
import Layout from "../../Layouts/Layout";
import SingerPageHeader from "./SingerPageHeader";
import {Link} from "@inertiajs/inertia-react";
import SingerTableDesktop from "./SingerTableDesktop";
import SingerTableMobile from "./SingerTableMobile";

const Index = ({all_singers}) => (
    <>
        <SingerPageHeader
            title="Singers"
            icon="fa-users"
            breadcrumbs={[
                <Link href={route('dash')} className="text-sm font-medium text-gray-500 hover:text-gray-700">
                    Dashboard
                </Link>,
                <Link href={route('singers.index')} className="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                    Singers
                </Link>
            ]}
            actions={[
                { label: 'Add New', icon: 'user-plus', route: 'singers.create'},
                { label: 'Voice Parts', icon: 'users-class', route: 'voice-parts.index'},
                { label: 'Filter', icon: 'filter', route: 'voice-parts.index'},
            ]}
        />

        {/* Desktop Table */}
        <div className="hidden lg:flex flex-col">
            <SingerTableDesktop singers={all_singers} />
        </div>

        {/* Mobile Table */}
        <div className="bg-white shadow overflow-hidden block lg:hidden">
            <SingerTableMobile singers={all_singers} />
        </div>
    </>
);

Index.layout = page => <Layout children={page} title="Singers" />

export default Index;