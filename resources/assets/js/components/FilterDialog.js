import React, { Fragment } from 'react';
import { Dialog as BaseDialog, Transition } from '@headlessui/react';

const FilterDialog = ({ isOpen, setIsOpen, children }) => (
	<Transition.Root show={isOpen} as={Fragment}>
		<BaseDialog as="div" className="fixed inset-0 z-20" onClose={setIsOpen}>
			<Transition.Child
				as={Fragment}
				enter="ease-out duration-300"
				enterFrom="opacity-0"
				enterTo="opacity-100"
				leave="ease-in duration-200"
				leaveFrom="opacity-100"
				leaveTo="opacity-0"
			>
				<BaseDialog.Overlay className="fixed inset-0 bg-gray-500 bg-opacity-75" />
			</Transition.Child>

			<Transition.Child
				as={Fragment}
				enter="transform transition ease-out duration-300"
				enterFrom="translate-x-full"
				enterTo="translate-x-0"
				leave="transform transition ease-in duration-200"
				leaveFrom="translate-x-0"
				leaveTo="translate-x-full"
			>
				<div className="relative z-30 h-full min-h-screen w-full overflow-y-auto bg-white">
					{children}
				</div>
			</Transition.Child>
		</BaseDialog>
	</Transition.Root>
);

export default FilterDialog;
