import React from 'react'
import Layout from "../../Layouts/Layout";
import SingerPageHeader from "./SingerPageHeader";
import {Link} from "@inertiajs/inertia-react";

const Index = () => (
    <>
        <div className="py-6 bg-white border-b border-gray-300">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <SingerPageHeader
                    title="Singers"
                    breadcrumbs={[
                        <Link href={route('dash')} className="text-sm font-medium text-gray-500 hover:text-gray-700">
                            Dashboard
                        </Link>,
                        <Link href={route('singers.index')} className="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                            Singers
                        </Link>
                    ]}
                />
            </div>
        </div>
    </>
);

Index.layout = page => <Layout children={page} title="Singers" />

export default Index;