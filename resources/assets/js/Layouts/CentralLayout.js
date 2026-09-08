import React, {useCallback, useState} from 'react'
import SidebarDesktop from "../components/SidebarDesktop";
import SidebarMobile from "../components/SidebarMobile";
import {usePage} from '@inertiajs/react';
import LayoutTopBar from "../components/LayoutTopBar";
import ToastFlash from "../components/ToastFlash";
import {useMediaQuery} from "react-responsive";
import useRoute from "../hooks/useRoute";
import OuterPageErrorFallback from "./OuterPageErrorFallback";
import {ErrorBoundary} from "@sentry/react";
import { router } from '@inertiajs/react';
import { useRNHandler } from '../lib/reactNative';

export default function CentralLayout({ children }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const { route } = useRoute();

    const [showImpersonateModal, setShowImpersonateModal] = useState(false);

    const handleRNNavigation = useCallback(payload => router.get(payload.url), []);
    useRNHandler('navigation', handleRNNavigation);

    const isMobileOrTablet = useMediaQuery({ query: '(max-width: 1279px)' });

    const { can, userChoirs, errors, flash, navigation, isWebView } = usePage().props;

    const navFiltered = navigation
        .filter((item) => can[item.can])
        .map((item) => {
            item.active = item.showAsActiveForRoutes.some((routeName) => route().current(routeName));
            item.items = item.items
                .filter((subItem) => can[subItem.can])
                .map((subItem) => {
                    subItem.active = subItem.showAsActiveForRoutes.some((routeName) => route().current(routeName));
                    return subItem;
                });
            return item;
        })

    return (
        <div className="h-screen flex overflow-hidden bg-gray-100">
            {isMobileOrTablet ? (
                <>
                    <SidebarMobile navigation={navFiltered} open={sidebarOpen} setOpen={setSidebarOpen} choirs={userChoirs} setShowImpersonateModal={setShowImpersonateModal} />
                </>
            ) : (
                <div className="flex shrink-0">
                    <SidebarDesktop navigation={navFiltered} choirs={userChoirs} setShowImpersonateModal={setShowImpersonateModal} />
                </div>
            )}

            <div className="flex flex-col w-0 flex-1 overflow-hidden">
                {!isWebView && (
                    <LayoutTopBar
                        setSidebarOpen={setSidebarOpen}
                    />
                )}

                <main className="flex-1 flex flex-col justify-stretch relative overflow-y-auto focus:outline-none" scroll-region="true">
                    <ErrorBoundary fallback={() => <OuterPageErrorFallback />} key={route().current()}>
                        {children}
                    </ErrorBoundary>
                </main>
            </div>

            <ToastFlash errors={errors} flash={flash} />

            {/*<ImpersonateUserModal isOpen={showImpersonateModal} setIsOpen={setShowImpersonateModal} />*/}
        </div>
    )
}
