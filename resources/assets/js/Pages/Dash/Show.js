import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import AppHead from "../../components/AppHead";
import UpcomingEventsWidget from "./UpcomingEventsWidget";
import SongsToLearnWidget from "./SongsToLearnWidget";
import BirthdaysWidget from "./BirthdaysWidget";
import MemberversariesWidget from "./MemberversariesWidget";
import ErrorAlert from "../../components/ErrorAlert";
import WarningAlert from "../../components/WarningAlert";

const Show = ({ events, songs, birthdays, emptyDobs, memberversaries, feeStatus }) => (
    <>
        <AppHead title="Dashboard" />
        <div className="py-6">
            <div className="mx-auto px-4 sm:px-6 lg:px-16">
                <h1 className="text-2xl font-semibold text-gray-900 mb-8">Dashboard</h1>

                <FeeStatusWarning status={feeStatus} />

                <div className="grid gap-y-6 gap-x-4 grid-cols-1 sm:grid-cols-2 xl:grid-cols-3">
                    <div>
                        <UpcomingEventsWidget events={events} />
                    </div>

                    <div>
                        <SongsToLearnWidget songs={songs} />
                    </div>

                    <div>
                        <BirthdaysWidget birthdays={birthdays} emptyDobs={emptyDobs} />
                        <MemberversariesWidget memberversaries={memberversaries} />
                    </div>
                </div>
            </div>
        </div>
    </>
);

Show.layout = page => <TenantLayout children={page} />

export default Show;

const FeeStatusWarning = ({ status }) => {
    if(status === 'unknown' || status === 'paid') {
        return null;
    }

    return status === 'expires-soon'
        ? (
            <WarningAlert title="Membership fees expiring soon" className="mb-4">
                Your membership will soon expire. Please contact your choir's accounts team to pay your bill.
            </WarningAlert>
        ) : (
            <ErrorAlert title="Membership fees expired" className="mb-4">
                Your membership has expired! Please contact your choir's accounts team to pay your bill.
            </ErrorAlert>
        );
};