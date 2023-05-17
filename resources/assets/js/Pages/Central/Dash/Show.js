import React from 'react'
import AppHead from "../../../components/AppHead";
import CentralLayout from "../../../Layouts/CentralLayout";
import ChoirsListWidget from "./ChoirsListWidget";
import CentralUpcomingEventsWidget from "./CentralUpcomingEventsWidget";
import CentralSongsToLearnWidget from "./CentralSongsToLearnWidget";

const Show = ({ events, songs }) => (
    <>
        <AppHead title="Dashboard" />
        <div className="py-6">
            <div className="mx-auto px-4 sm:px-6 lg:px-16">
                <h1 className="text-2xl font-semibold text-gray-900 mb-8">Dashboard</h1>

                <div className="grid gap-y-6 gap-x-4 grid-cols-1 sm:grid-cols-2 xl:grid-cols-3">
                    <div className="sm:col-span-2 lx:col-span-1">
                        <ChoirsListWidget />
                    </div>

                    <div>
                        <CentralUpcomingEventsWidget events={events} />
                    </div>

                    <div>
                        <CentralSongsToLearnWidget songs={songs} />
                    </div>
                </div>
            </div>
        </div>
    </>
);

Show.layout = page => <CentralLayout children={page} />

export default Show;