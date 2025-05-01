import { Menu, Transition } from '@headlessui/react'
import React, {Fragment} from "react";
import Icon from "./Icon";
import classNames from "../classNames";
import {Link} from "@inertiajs/react";
import useRoute from "../hooks/useRoute";
const SwitchChoirMenu = ({ choirs: organisations, tenant }) => {
	const { route } = useRoute();

	return (
		<Menu as="div" className="relative inline-block text-left grow">
			<div className="h-full">
					<Menu.Button
						disabled={tenant && organisations.length < 2}
						className={classNames(
							'inline-flex h-full sm:min-w-[200px] justify-between items-center gap-x-2.5 px-3 py-2',
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