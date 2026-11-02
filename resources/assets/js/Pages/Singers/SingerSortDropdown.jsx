import React, { useEffect, useRef } from 'react';
import { Popover } from '@headlessui/react';
import Button from '../../components/inputs/ButtonV2';
import Icon from '../../components/Icon';
import { PositionedPanel } from './SingerPositionedPanel';

const SingerSortDropdown = ({ sorts, form, autoSubmit = false }) => {
	const activeSort = sorts.find(sort => sort.id === form.data.sort)?.name ?? 'Sort';
	const buttonRef = useRef(null);
	const panelRef = useRef(null);
	const firstUpdate = useRef(true);

	useEffect(() => {
		if (!autoSubmit) {
			return undefined;
		}

		if (firstUpdate.current) {
			firstUpdate.current = false;
			return undefined;
		}

		const timeout = window.setTimeout(() => form.submit(undefined, { preserveState: true }), 300);

		return () => window.clearTimeout(timeout);
	}, [autoSubmit, form.data.sort, form.data.sortDir]);

	return (
		<Popover as="div" className="relative">
			{({ open }) => (
				<>
					<Popover.Button
						ref={buttonRef}
						as={Button}
						size="sm"
						weight="outline"
						className="h-8"
						aria-label={`Sort: ${activeSort}`}
					>
						<Icon icon="sort" type="regular" />
						Sort
						<Icon icon="chevron-down" type="regular" />
					</Popover.Button>
					<PositionedPanel
						component={Popover.Panel}
						open={open}
						referenceRef={buttonRef}
						panelRef={panelRef}
						placement="bottom-end"
						className="max-h-[calc(100vh-1rem)] w-64 max-w-[calc(100vw-1rem)] overflow-y-auto rounded-md border border-gray-200 bg-white p-3 shadow-lg"
					>
						<p className="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Sort by</p>
						<div className="space-y-1">
							{sorts.map(sort => (
								<label
									key={sort.id}
									className="flex cursor-pointer items-center gap-2 rounded px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-50"
								>
									<input
										type="radio"
										name="singer-sort"
										checked={form.data.sort === sort.id}
										onChange={() => form.setData('sort', sort.id)}
										className="border-gray-300 text-purple-600 focus:ring-purple-500"
									/>
									{sort.name}
								</label>
							))}
						</div>
						<div className="mt-3 flex gap-2 border-t border-gray-200 pt-3">
							{[
								{ value: 'asc', label: 'Ascending', icon: 'sort-amount-up' },
								{ value: 'desc', label: 'Descending', icon: 'sort-amount-down' },
							].map(direction => (
								<Button
									key={direction.value}
									size="sm"
									weight="outline"
									intent={form.data.sortDir === direction.value ? 'primary' : 'secondary'}
									aria-pressed={form.data.sortDir === direction.value}
									onClick={() => form.setData('sortDir', direction.value)}
									className="grow"
								>
									<Icon icon={direction.icon} type="regular" />
									{direction.label}
								</Button>
							))}
						</div>
					</PositionedPanel>
				</>
			)}
		</Popover>
	);
};

export default SingerSortDropdown;