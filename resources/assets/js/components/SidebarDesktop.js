import {Link} from "@inertiajs/react";
import React from "react";
import MainNavigation from "./MainNavigation";
import useRoute from "../hooks/useRoute";

const SidebarDesktop = ({ navigation }) => {
    const { route } = useRoute();

    return (
        <div className="flex flex-col w-64 bg-brand-purple-dark">
            {/* Sidebar component, swap this element with another sidebar if you like */}
            <div className="flex flex-col grow pt-5 pb-4 overflow-y-auto">
                <Link href={route('central.dash')} className="flex px-8 mb-5">
                    <img src="/img/vibrant/logo.svg" alt="Choir Concierge" className="h-12 w-auto" />
                </Link>

                <div className="flex-1 flex flex-col">
                    <MainNavigation navigation={navigation} />
                </div>
            </div>

        </div>
    );
}

export default SidebarDesktop;