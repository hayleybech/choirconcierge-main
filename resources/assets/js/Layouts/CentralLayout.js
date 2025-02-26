import React, {useState} from 'react'
import SidebarDesktop from "../components/SidebarDesktop";
import SidebarMobile from "../components/SidebarMobile";
import {usePage} from '@inertiajs/react';
import LayoutTopBar from "../components/LayoutTopBar";
import ToastFlash from "../components/ToastFlash";
import {useMediaQuery} from "react-responsive";
import centralNavigation from "./centralNavigation";
import useRoute from "../hooks/useRoute";
import SwitchChoirMenu from "../components/SwitchChoirMenu";
import OuterPageErrorFallback from "./OuterPageErrorFallback";
import {ErrorBoundary} from "@sentry/react";

export default function CentralLayout({ children }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const { route } = useRoute();

    const [showImpersonateModal, setShowImpersonateModal] = useState(false);

    const isMobile = useMediaQuery({ query: '(max-width: 1023px)' });

    const { can, userChoirs, errors, flash } = usePage().props;

    const navFiltered = centralNavigation
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
            {isMobile ? (
                <>
                    <SidebarMobile navigation={navFiltered} open={sidebarOpen} setOpen={setSidebarOpen} />
                </>
            ) : (
                <div className="flex shrink-0">
                    <SidebarDesktop navigation={navFiltered} />
                </div>
            )}

            <div className="flex flex-col w-0 flex-1 overflow-hidden">
                <LayoutTopBar
                  setSidebarOpen={setSidebarOpen}
                  setShowImpersonateModal={setShowImpersonateModal}
                  switchChoirMenu={<SwitchChoirMenu choirs={userChoirs} />}
                />

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