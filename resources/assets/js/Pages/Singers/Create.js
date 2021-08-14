import React from 'react'
import Layout from "../../Layouts/Layout";
import SingerPageHeader from "./SingerPageHeader";

const Create = () => (
    <>
        <div className="py-6 bg-white border-b border-gray-300">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                <SingerPageHeader
                    title={'Create Singer'}
                    actions={(
                        <>
                        </>
                    )}
                />
            </div>
        </div>
    </>
);

Create.layout = page => <Layout children={page} title="Singers" />

export default Create;