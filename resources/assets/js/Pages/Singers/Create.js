import React from 'react'
import Layout from "../../Layouts/Layout";
import SingerPageHeader from "./SingerPageHeader";
import {Link} from "@inertiajs/inertia-react";

const Create = () => (
    <>
        <div className="py-6 bg-white border-b border-gray-300">
            <div className="px-4 sm:px-6 md:px-8">
                <SingerPageHeader
                    title={'Create Singer'}
                    icon="fa-users"
                    breadcrumbs={[
                        <Link href={route('dash')} className="text-sm font-medium text-gray-500 hover:text-gray-700">
                            Dashboard
                        </Link>,
                        <Link href={route('singers.index')} className="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                            Singers
                        </Link>,
                        <Link href={route('singers.create')} className="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                            Create
                        </Link>
                    ]}
                />
            </div>
        </div>
    </>
);

Create.layout = page => <Layout children={page} title="Singers" />

export default Create;