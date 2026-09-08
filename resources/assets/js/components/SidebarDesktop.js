import {Link} from "@inertiajs/react";
import React from "react";
import MainNavigation from "./MainNavigation";
import useRoute from "../hooks/useRoute";
import SwitchChoirMenu from "./SwitchChoirMenu";
import UserMenu from "./UserMenu";

const SidebarDesktop = ({ navigation, choirs, tenant, setShowImpersonateModal }) => {
    const { route } = useRoute();

    return (
        <div className="flex flex-col w-64 bg-brand-purple-dark">
            {/* Sidebar component, swap this element with another sidebar if you like */}
            <div className="flex flex-col grow pt-5 pb-4 overflow-y-auto">
                <Link href={route('central.dash')} className="flex px-8 mb-5">
                    <img src="/img/vibrant/logo.svg" alt="Choir Concierge" className="h-12 w-auto" />
                </Link>

                <div className="mb-5"><SwitchChoirMenu choirs={choirs} tenant={tenant} /></div>

                <div className="flex-1 flex flex-col">
                    <MainNavigation navigation={navigation} />
                    <div className="px-4"><UserMenu setShowImpersonateModal={setShowImpersonateModal} /></div>
                </div>
            </div>

        </div>
    );
}

export default SidebarDesktop;
