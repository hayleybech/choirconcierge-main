import React, {useEffect} from 'react'
import AppHead from "../../../components/AppHead";
import useRoute from "../../../hooks/useRoute";
import CentralLayout from "../../../Layouts/CentralLayout";
import LoadingSpinner from "../../../components/LoadingSpinner";
import {router} from "@inertiajs/react";

const Onboarding = ({ tenant }) => {
    const { route } = useRoute();

    const TIME_BETWEEN_CHECKS = 3000;

    useEffect(() => {
        const timer = setInterval(() => {
            if(tenant.setup_done) {
                window.location.assign(route('dash', {'tenant': tenant}));
                return;
            }

            router.reload({ only: ['tenant'] })
        }, TIME_BETWEEN_CHECKS);

        return () => clearInterval(timer);
    }, [tenant]);

    return (
        <>
            <AppHead title={`Almost Ready! - ${tenant.name}`} />

            <div className="bg-white grow flex items-center">
                <div className="mx-auto max-w-3xl text-center">
                    <h1 className="text-base font-medium text-purple-600">Organisation Created!</h1>
                    <p className="mt-4 text-4xl font-bold tracking-tight">Getting you set up...</p>
                    <p className="mt-6 text-base text-gray-500">Your organisation has been created and the workspace will be ready in a few moments.</p>

                    <div className="text-5xl mt-8">
                        <LoadingSpinner />
                    </div>
                </div>
            </div>
        </>
    );
}

Onboarding.layout = page => <CentralLayout children={page} />

export default Onboarding;