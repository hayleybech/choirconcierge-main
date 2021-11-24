import {Link} from "@inertiajs/inertia-react";
import route from "ziggy-js";
import React from "react";
import MainNavigation from "./MainNavigation";

const SidebarDesktop = ({navigation}) => (
    <div className="flex flex-col w-64 bg-brand-purple-dark">
        {/* Sidebar component, swap this element with another sidebar if you like */}
        <div className="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
            <Link href={route('dash')} className="flex pb-6 px-8">
                <img src="/img/logo.svg" alt="Choir Concierge" className="h-12 w-auto" />
            </Link>

            <Link href={route('dash')} className="flex justify-center mb-4 py-4 px-6 bg-white border-l border-r border-gray-300">
                <img src="/tenancy/assets/choir-logo.png" className="h-12 w-auto" />
            </Link>
            <div className="mt-4 flex-1 flex flex-col">
                <MainNavigation navigation={navigation} />
            </div>
        </div>
    </div>
);

export default SidebarDesktop;