import React from 'react'
import Layout from "../../Layouts/Layout";
import AppHead from "../../components/AppHead";
import UpcomingEventsWidget from "./UpcomingEventsWidget";
import SongsToLearnWidget from "./SongsToLearnWidget";

const Show = ({ events, songs }) => (
    <>
        <AppHead title="Dashboard" />
        <div className="py-6">
            <div className="mx-auto px-4 sm:px-6 lg:px-16">
                <h1 className="text-2xl font-semibold text-gray-900 mb-8">Dashboard</h1>

                <div className="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div className="sm:col-span-2">
                        <UpcomingEventsWidget events={events} />
                    </div>

                    <div className="sm:col-span-2">
                        <SongsToLearnWidget songs={songs} />
                    </div>
                </div>
            </div>
        </div>
    </>
);

Show.layout = page => <Layout children={page} />

export default Show;