import React from 'react';
import { Fragment } from 'react';
import { Dialog as BaseDialog, Transition } from '@headlessui/react';
import ButtonLink from "./inputs/ButtonLink";
import Button from "./inputs/Button";
import Icon from "./Icon";

const Dialog = ({ title, children, okLabel, okUrl, okVariant, okMethod, data, isOpen, setIsOpen}) => (
    <Transition.Root show={isOpen} as={Fragment}>
        <BaseDialog as="div" className="fixed z-10 inset-0 overflow-y-auto" onClose={setIsOpen}>
            <div className="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <Transition.Child
                    as={Fragment}
                    enter="ease-out duration-300"
                    enterFrom="opacity-0"
                    enterTo="opacity-100"
                    leave="ease-in duration-200"
                    leaveFrom="opacity-100"
                    leaveTo="opacity-0"
                >
                    <BaseDialog.Overlay className="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                </Transition.Child>

                {/* This element is to trick the browser into centering the modal contents. */}
                <span className="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">
                    &#8203;
                </span>
                <Transition.Child
                    as={Fragment}
                    enter="ease-out duration-300"
                    enterFrom="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enterTo="opacity-100 translate-y-0 sm:scale-100"
                    leave="ease-in duration-200"
                    leaveFrom="opacity-100 translate-y-0 sm:scale-100"
                    leaveTo="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div className="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                        <div className="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                            <button
                                type="button"
                                className="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                                onClick={() => setIsOpen(false)}
                            >
                                <span className="sr-only">Close</span>
                                <Icon icon="times" type="light" className="text-xl" />
                            </button>
                        </div>
                        <div className="sm:flex sm:items-start">
                            <div className="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <Icon icon="exclamation-triangle" type="regular" className="text-red-600 text-xl" />
                            </div>
                            <div className="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <BaseDialog.Title as="h3" className="text-lg leading-6 font-medium text-gray-900">
                                    {title}
                                </BaseDialog.Title>
                                <div className="mt-2 text-sm text-gray-500">
                                    {children}
                                </div>
                            </div>
                        </div>
                        <div className="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <ButtonLink variant={okVariant} size="sm" href={okUrl} method={okMethod} data={data} as="button" className="sm:ml-3 w-full" onClick={() => setIsOpen(false)} >{okLabel}</ButtonLink>
                            <Button size="sm" onClick={() => setIsOpen(false)} className="w-full mt-3 sm:mt-0">Cancel</Button>
                        </div>
                    </div>
                </Transition.Child>
            </div>
        </BaseDialog>
    </Transition.Root>
);

export default Dialog;