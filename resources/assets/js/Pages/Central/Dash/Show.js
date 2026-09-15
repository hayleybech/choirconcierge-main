import React from 'react'
import AppHead from "../../../components/AppHead";
import CentralLayout from "../../../Layouts/CentralLayout";
import ChoirsListWidget from "./ChoirsListWidget";
import CentralUpcomingEventsWidget from "./CentralUpcomingEventsWidget";
import CentralSongsToLearnWidget from "./CentralSongsToLearnWidget";
import TenantStatsWidget from './TenantStatsWidget';
import { PageHeader, PageHeaderContent, PageHeaderTitle } from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../../components/PageTopBar';
import Icon from '../../../components/Icon';

const Show = ({ events, songs, tenantStats, setSidebarOpen }) => (
    <>
        <AppHead title="Dashboard" />
        <PageTopBar setSidebarOpen={setSidebarOpen}>
            <PageTopNavigation title="Central Dashboard" />
        </PageTopBar>
        <PageHeader>
            <PageHeaderContent>
                <PageHeaderTitle>
                    <Icon icon="tachometer-alt" type="solid" className="mr-2" /> Central Dashboard
                </PageHeaderTitle>
            </PageHeaderContent>
        </PageHeader>
        <div className="py-6">
            <div className="mx-auto px-4 sm:px-6 lg:px-16">
                <div className="mb-8">
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
