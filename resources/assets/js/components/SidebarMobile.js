import {Dialog, Transition} from "@headlessui/react";
import React, {Fragment} from "react";
import {Link} from "@inertiajs/react";
import MainNavigation from "./MainNavigation";
import Icon from "./Icon";
import useRoute from "../hooks/useRoute";

const SidebarMobile = ({ navigation, open, setOpen }) => {
    const { route } = useRoute();

    return (
        <Transition.Root show={open} as={Fragment}>
            <Dialog as="div" className="fixed inset-0 flex z-40" onClose={setOpen}>
                <Transition.Child
                    as={Fragment}
                    enter="transition-opacity ease-linear duration-300"
                    enterFrom="opacity-0"
                    enterTo="opacity-100"
                    leave="transition-opacity ease-linear duration-300"
                    leaveFrom="opacity-100"
                    leaveTo="opacity-0"
                >
                    <Dialog.Overlay className="fixed inset-0 bg-gray-600 bg-opacity-75" />
                </Transition.Child>
                <Transition.Child
                    as={Fragment}
                    enter="transition ease-in-out duration-300 transform"
                    enterFrom="-translate-x-full"
                    enterTo="translate-x-0"
                    leave="transition ease-in-out duration-300 transform"
                    leaveFrom="translate-x-0"
                    leaveTo="-translate-x-full"
                >
                    <div className="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-brand-purple-dark">
                        <Transition.Child
                            as={Fragment}
                            enter="ease-in-out duration-300"
                            enterFrom="opacity-0"
                            enterTo="opacity-100"
                            leave="ease-in-out duration-300"
                            leaveFrom="opacity-100"
                            leaveTo="opacity-0"
                        >
                            <div className="absolute top-0 right-0 -mr-12 pt-2">
                                <button
                                    type="button"
                                    className="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                                    onClick={() => setOpen(false)}
                                >
                                    <span className="sr-only">Close sidebar</span>
                                    <Icon icon="times" type="light" className="text-white text-lg" />
                                </button>
                            </div>
                        </Transition.Child>

                        <Link href={route('central.dash')} className="shrink-0 flex justify-center items-center px-4 mb-5">
                            <img src="/img/vibrant/logo.svg" alt="Choir Concierge" className="h-10 w-auto" />
                        </Link>

                        <div className="flex-1 h-0 overflow-y-auto">
                            <MainNavigation navigation={navigation} closeSidebar={() => setOpen(false)} />
                        </div>
                    </div>
                </Transition.Child>
                <div className="shrink-0 w-14" aria-hidden="true">
                    {/* Dummy element to force sidebar to shrink to fit close icon */}
                </div>

            </Dialog>

        </Transition.Root>
    );
}

export default SidebarMobile;