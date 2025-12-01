import React from 'react'
import AppHead from "../../../components/AppHead";
import CentralLayout from "../../../Layouts/CentralLayout";
import ChoirsListWidget from "./ChoirsListWidget";
import CentralUpcomingEventsWidget from "./CentralUpcomingEventsWidget";
import CentralSongsToLearnWidget from "./CentralSongsToLearnWidget";
import TenantStatsWidget from "./TenantStatsWidget";

const Show = ({ events, songs, tenantStats }) => (
    <>
        <AppHead title="Dashboard" />
        <div className="py-6">
            <div className="mx-auto px-4 sm:px-6 lg:px-16">
                <div className="mb-8">
                    <h1 className="text-2xl font-semibold text-gray-900 mb-2">Central Dashboard</h1>
                    <p className="text-sm text-gray-700">This combined dashboard shows upcoming events and songs for all of your choirs. Click one of your choirs below to go to individual Dashboards. </p>
                </div>

                <div className="grid gap-y-6 gap-x-4 grid-cols-1 sm:grid-cols-2 xl:grid-cols-3">
                    {!!tenantStats && (
                        <div className="sm:col-span-2 xl:col-span-3">
                            <TenantStatsWidget {...tenantStats} />
                        </div>
                    )}
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