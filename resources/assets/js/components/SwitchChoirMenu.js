import { Dialog, Menu, Transition } from '@headlessui/react'
import React, {Fragment, useState} from "react";
import Icon from "./Icon";
import classNames from "../classNames";
import {Link} from "@inertiajs/react";
import useRoute from "../hooks/useRoute";
const SwitchChoirMenu = ({ choirs: organisations, tenant, mobile = false }) => {
	const { route } = useRoute();
	const [open, setOpen] = useState(false);

	if (mobile) {
		return (
			<>
				<button type="button" disabled={tenant && organisations.length < 2} onClick={() => setOpen(true)} className={classNames(
					'inline-flex h-full w-full justify-between items-center gap-x-2.5 px-3 py-2',
					'bg-white text-sm font-semibold text-gray-900 hover:bg-gray-50'
				)}>
					{tenant ? (
						<>
							{tenant.logo_url ? <img src={tenant.logo_url} alt={tenant.name} className="max-h-10 w-auto" /> : tenant.name}
							{organisations.length > 1 && <Icon icon="chevron-down" className="text-gray-400" />}
						</>
					) : (
						<>
							<div>Switch Choir</div>
							<Icon icon="chevron-down" className="text-gray-400" />
						</>
					)}
				</button>
				<Transition.Root show={open} as={Fragment}>
					<Dialog as="div" className="fixed inset-0 z-50 overflow-y-auto" onClose={setOpen}>
						<div className="flex min-h-screen items-center justify-center px-4 text-center">
							<Transition.Child as={Fragment} enter="ease-out duration-200" enterFrom="opacity-0" enterTo="opacity-100" leave="ease-in duration-150" leaveFrom="opacity-100" leaveTo="opacity-0">
								<Dialog.Overlay className="fixed inset-0 bg-gray-600 bg-opacity-75" />
							</Transition.Child>
							<Transition.Child as={Fragment} enter="ease-out duration-200" enterFrom="opacity-0 scale-95" enterTo="opacity-100 scale-100" leave="ease-in duration-150" leaveFrom="opacity-100 scale-100" leaveTo="opacity-0 scale-95">
								<div className="relative w-full max-w-sm overflow-hidden rounded-lg bg-white text-left shadow-xl">
									<div className="flex items-center justify-between border-b px-4 py-3"><Dialog.Title className="font-semibold text-gray-900">Switch Choir</Dialog.Title><button type="button" onClick={() => setOpen(false)}><Icon icon="times" /></button></div>
									<div className="divide-y divide-gray-200">
										{organisations.map(org => <Link key={org.id} href={route('tenants.switch.start', {newTenant: org.id})} onClick={() => setOpen(false)} className="block px-4 py-3 text-gray-700 hover:bg-gray-100">{org.logo_url ? <img src={org.logo_url} alt={org.name} className="max-h-10 w-auto" /> : org.name}</Link>)}
									</div>
								</div>
							</Transition.Child>
						</div>
					</Dialog>
				</Transition.Root>
			</>
		);
	}

	return (
		<Menu as="div" className="relative inline-block text-left grow w-full">
			<div className="h-full">
					<Menu.Button
						disabled={tenant && organisations.length < 2}
						className={classNames(
							'inline-flex h-full w-full justify-between items-center gap-x-2.5 px-3 py-2',
							'bg-white text-sm font-semibold text-gray-900 hover:bg-gray-50'
							)}
					>
						{tenant ? (
							<>
								{tenant.logo_url
										? <img src={tenant.logo_url} alt={tenant.name} className="max-h-10 w-auto" />
										: tenant.name
								}
								{organisations.length > 1 && <Icon icon="chevron-down" className="text-gray-400" />}
							</>
						) : (
							<>
								<div>Switch Choir</div>
								<Icon icon="chevron-down" className="text-gray-400" />
							</>
						)}
					</Menu.Button>
			</div>

			<Transition
				as={Fragment}
				enter="transition ease-out duration-100"
				enterFrom="transform opacity-0 scale-95"
				enterTo="transform opacity-100 scale-100"
				leave="transition ease-in duration-75"
				leaveFrom="transform opacity-100 scale-100"
				leaveTo="transform opacity-0 scale-95"
			>
				<Menu.Items className="absolute right-0 z-20 mt-2 w-full max-h-[80dvh] sm:max-h-[60dvh] overflow-y-scroll origin-top-left bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
					<div className="py-1 divide-y divide-gray-200">
						{organisations.map(org => (
							<Menu.Item key={org.id}>
								{({ active }) => (
									<Link
										href={route('tenants.switch.start', {newTenant: org.id})}
										className={classNames(
											active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
											'block px-4 py-2 text-sm'
										)}
									>
										{org.logo_url
											? <img src={org.logo_url} alt={org.name} className="max-h-10 w-auto"/>
											: org.name
										}
									</Link>
								)}
							</Menu.Item>
						))}
					</div>
				</Menu.Items>
			</Transition>
		</Menu>
	);
}

export default SwitchChoirMenu;
