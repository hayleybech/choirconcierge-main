import React, { useEffect, useRef, useState } from 'react';
import { Menu, Popover, Portal } from '@headlessui/react';
import Popper from 'popper.js';
import ActionMenuItem from './ActionMenu/ActionMenuItem';
import Badge from './Badge';
import Button from './inputs/ButtonV2';
import Icon from './Icon';
import { useMediaQuery } from 'react-responsive';

const IndexTableControls = ({
	filters = [],
	sorts = [],
	form,
	tableActions = [],
	tableActionContent = null,
	onToggleFilters,
	bulkEdit,
	filterLabel,
	hasChangedFilters: hasChangedFiltersOverride,
}) => {
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });
	const previousData = useRef(form.data);
	const [addedFilters, setAddedFilters] = useState([]);
	const searchableFilters = filters.filter(filter => ['date', 'text'].includes(filter.type));
	const filtersForPills = filters.filter(filter => !['date', 'text'].includes(filter.type));

	useEffect(() => {
		if (!isDesktop) {
			previousData.current = form.data;
			return undefined;
		}

		if (previousData.current === form.data) {
			return undefined;
		}

		previousData.current = form.data;
		const timeout = window.setTimeout(() => form.submit(undefined, { preserveState: true }), 300);

		return () => window.clearTimeout(timeout);
	}, [form.data, isDesktop]);

	const visibleFilters = filtersForPills.filter(
		filter =>
			hasSelectedValue(filter, form.data[filter.name]) ||
			hasSelectedValue(filter, filter.defaultValue) ||
			addedFilters.includes(filter.name)
	);
	const hasChangedFilters =
		hasChangedFiltersOverride ?? filters.some(filter => isNonDefault(filter, form.data[filter.name]));
	const availableFilters = filtersForPills.filter(filter => !visibleFilters.includes(filter));
	const changeFilter = (filter, value) => {
		if (filter.onChange) {
			filter.onChange(value, form);
			return;
		}

		form.setData(filter.name, value);
	};
	const addFilter = (filter, value) => {
		setAddedFilters(previousFilters =>
			previousFilters.includes(filter.name) ? previousFilters : [...previousFilters, filter.name]
		);
		changeFilter(filter, value);
	};
	const removeAddedFilter = filterName =>
		setAddedFilters(previousFilters => previousFilters.filter(addedFilter => addedFilter !== filterName));
	const clearFilter = filter => {
		const emptyValue = filter.multiple ? (hasSelectedValue(filter, filter.defaultValue) ? [''] : []) : '';
		changeFilter(filter, emptyValue);

		if (!hasSelectedValue(filter, filter.defaultValue)) {
			removeAddedFilter(filter.name);
		}
	};
	const resetFilter = filter => {
		const defaultValue = filter.defaultValue ?? (filter.multiple ? [] : '');
		changeFilter(filter, Array.isArray(defaultValue) ? [...defaultValue] : defaultValue);
		removeAddedFilter(filter.name);
	};
	const clearAllFilters = () => filters.forEach(clearFilter);
	const resetAllFilters = () => filters.forEach(resetFilter);

	return (
		<div className="bg-white px-4 py-2 sm:px-6 md:px-8">
			<div
				aria-label="Filters"
				className="hidden items-center gap-3 lg:flex"
				data-testid="desktop-index-table-controls"
			>
				<div className="flex min-w-0 flex-1 flex-wrap items-center gap-2">
					<span className="shrink-0 text-xs font-semibold uppercase tracking-wide text-gray-500">Filters</span>
					{searchableFilters.map(filter => (
						<NameSearchInput
							key={filter.name}
							filter={filter}
							filterLabel={filterLabel}
							value={form.data[filter.name]}
							onChange={value => changeFilter(filter, value)}
						/>
					))}
					<div className="flex min-w-0 flex-wrap items-center gap-2">
						{visibleFilters.map(filter => (
							<FilterPill
								key={filter.name}
								filter={filter}
								value={form.data[filter.name]}
								nonDefault={isNonDefault(filter, form.data[filter.name])}
								onChange={value => changeFilter(filter, value)}
								onClear={() => clearFilter(filter)}
								onReset={() => resetFilter(filter)}
							/>
						))}
					</div>
					<AddFilterButton filters={availableFilters} onAdd={addFilter} />
					<div className="flex shrink-0 items-center gap-1">
						<Button size="sm" weight="clear" onClick={clearAllFilters}>
							<Icon icon="trash" type="regular" />
							Clear all filters
						</Button>
						<Button size="sm" weight="clear" onClick={resetAllFilters}>
							<Icon icon="undo" type="regular" />
							Reset to default
						</Button>
					</div>
				</div>
				<div className="sticky right-0 z-10 ml-auto flex shrink-0 items-center gap-2 bg-white">
					{sorts.length > 0 && <SortDropdown sorts={sorts} form={form} />}
					{tableActions.map(action => (
						<TableActionButton key={action.id ?? action.label} action={action} />
					))}
					{tableActionContent}
				</div>
			</div>

			<div
				aria-label={`${filterLabel || 'Table'} table controls`}
				className="flex items-center gap-2 border-b border-gray-200 lg:hidden"
				data-testid="mobile-index-table-controls"
			>
				{bulkEdit?.isActiveMobile && (
					<input
						ref={bulkEdit.refSelectAll}
						type="checkbox"
						checked={
							bulkEdit.totalItems > 0 &&
							(bulkEdit.selectedIds ?? []).length === bulkEdit.totalItems
						}
						onChange={bulkEdit.toggleAll}
						aria-label={`Select all ${filterLabel || 'items'}`}
						className="shrink-0 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
					/>
				)}
				{bulkEdit?.action && (
					<Button
						size="sm"
						intent={bulkEdit.action.intent ?? (bulkEdit.action.label === 'Cancel Selection' ? 'selected' : 'secondary')}
						weight="outline"
						className="h-8"
						onClick={bulkEdit.action.onClick}
						aria-label={bulkEdit.action.label}
					>
						{bulkEdit.action.icon && <Icon icon={bulkEdit.action.icon} />}
						{bulkEdit.action.label}
					</Button>
				)}
				<div className="ml-auto flex items-center gap-2">
					<Button
						size="sm"
						weight="outline"
						intent={hasChangedFilters ? 'selected' : 'secondary'}
						className="h-8"
						onClick={onToggleFilters}
						aria-label={`Filter ${filterLabel || 'items'}`}
					>
						<Icon icon="filter" type="regular" />
						Filter
					</Button>
					{sorts.length > 0 && <SortDropdown sorts={sorts} form={form} compact autoSubmit={!isDesktop} />}
					{(tableActions.length > 0 || tableActionContent) && (
						<MobileTableActions
							tableActions={tableActions}
							tableActionContent={tableActionContent}
							filterLabel={filterLabel}
						/>
					)}
				</div>
			</div>
		</div>
	);
};

const NameSearchInput = ({ filter, filterLabel, value, onChange }) => {
	const isDateInput = filter.type === 'date';
	const inputLabel = isDateInput
		? `${filter.label}${filterLabel ? ` for ${filterLabel}` : ''}`
		: `Search ${filterLabel ? `${filterLabel} by ${filter.label.toLowerCase()}` : filter.label}`;
	const clearLabel = isDateInput ? `Clear ${inputLabel}` : `Clear ${filterLabel || filter.label} search`;

	return (
		<label className="relative block w-40 shrink-0">
			<span className="sr-only">{inputLabel}</span>
			<span
				aria-hidden="true"
				className="pointer-events-none absolute inset-y-0 left-2.5 flex items-center text-gray-400"
			>
				<Icon icon={isDateInput ? 'calendar-day' : 'search'} size="text-xs" type="regular" />
			</span>
			<input
				type={isDateInput ? 'date' : 'text'}
				role={isDateInput ? 'textbox' : 'searchbox'}
				aria-label={inputLabel}
				placeholder={isDateInput ? '' : `Search ${filterLabel || filter.label}`}
				value={isDateInput ? formatDateInputValue(value) : value ?? ''}
				onChange={event => onChange(event.target.value)}
				className="block h-[26px] w-full rounded-full border-gray-300 py-0 pl-8 pr-8 text-xs shadow-none focus:border-purple-500 focus:ring-purple-500"
			/>
			{value && (
				<button
					type="button"
					aria-label={clearLabel}
					onClick={() => onChange('')}
					className="absolute inset-y-0 right-1 flex w-6 items-center justify-center rounded-full text-gray-400 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500"
				>
					<Icon icon="times" size="text-xs" type="regular" />
				</button>
			)}
		</label>
	);
};

const FilterPill = props => (
	<Popover as="div" className="shrink-0">
		{({ open }) => <FilterPillContent {...props} open={open} />}
	</Popover>
);

const FilterPillContent = ({ filter, value, nonDefault, onChange, onClear, onReset, open }) => {
	const buttonRef = useRef(null);
	const panelRef = useRef(null);

	return (
		<>
			<Popover.Button
				ref={buttonRef}
				as={Button}
				size="sm"
				intent={nonDefault ? 'selected' : 'secondary'}
				weight="outline"
				aria-label={`${filter.label}: ${getFilterSummary(filter, value)}; open filter values`}
				className="h-[26px] rounded"
			>
				<span>{filter.label}:</span>
				<div className="max-w-32 truncate text-xs">
					<FilterValueSummary filter={filter} value={value} />
				</div>
				<Icon icon="chevron-down" type="regular" />
			</Popover.Button>
			<PositionedPanel
				component={Popover.Panel}
				open={open}
				referenceRef={buttonRef}
				panelRef={panelRef}
				placement="bottom-start"
				className="max-h-[calc(100vh-1rem)] w-72 max-w-[calc(100vw-1rem)] overflow-y-auto rounded-md border border-gray-200 bg-white p-3 shadow-lg"
			>
				<div className="mb-3 flex items-center justify-between gap-2">
					<div className="flex min-w-0 items-center gap-2">
						<h3 className="text-sm font-semibold text-gray-900">{filter.label}</h3>
						{filter.multiple && (
							<span className="shrink-0 text-xs text-gray-500">
								{getSelectedFilterOptions(filter, value).length} selected
							</span>
						)}
					</div>
					{!filter.multiple && (
						<div className="text-xs text-gray-500">
							<FilterValueSummary filter={filter} value={value} />
						</div>
					)}
  			{filter.allValuesLabel && filter.multiple && filter.options?.length > 0 && (
						<button
							type="button"
							onClick={() => onChange((filter.options ?? []).map(option => option.id))}
							className="shrink-0 text-xs font-medium text-purple-600 underline hover:text-purple-800 hover:no-underline"
						>
							{filter.allValuesLabel}
						</button>
					)}
				</div>
				<FilterValueOptions filter={filter} value={value} onChange={onChange} />
				<div className="mt-3 flex justify-between gap-2 border-t border-gray-200 pt-3">
					<Button size="sm" weight="clear" onClick={onClear}>
						Clear selected
					</Button>
					<Button size="sm" weight="clear" onClick={onReset}>
						Reset to default
					</Button>
				</div>
			</PositionedPanel>
		</>
	);
};

const FilterValueOptions = ({ filter, value, onChange }) => {
	if (filter.type === 'text') {
		return (
			<label className="block text-sm font-medium text-gray-700">
				{filter.label}
				<input
					type="search"
					value={value ?? ''}
					onChange={event => onChange(event.target.value)}
					className="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500"
				/>
			</label>
		);
	}

	const values = filter.multiple ? (Array.isArray(value) ? value : []) : [value ?? ''];
	const options = filter.multiple ? filter.options ?? [] : [{ id: '', name: 'Any' }, ...(filter.options ?? [])];

	return (
		<div className="max-h-60 space-y-1 overflow-y-auto">
			{options.map(option => {
				const checked = values.some(selectedValue => String(selectedValue) === String(option.id));

				return (
					<label
						key={String(option.id)}
						className="flex cursor-pointer items-center gap-2 rounded px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-50"
					>
						<input
							type={filter.multiple ? 'checkbox' : 'radio'}
							name={`filter-${filter.name}`}
							checked={checked}
							onChange={() => {
								if (filter.multiple) {
									const selectedValues = values.filter(selectedValue => selectedValue !== '');
									const nextValues = checked
										? selectedValues.filter(selectedValue => String(selectedValue) !== String(option.id))
										: [...selectedValues, option.id];
									onChange(nextValues);
								} else {
									onChange(option.id);
								}
							}}
							className="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
						/>
						<FilterOptionDisplay filter={filter} option={option} />
					</label>
				);
			})}
		</div>
	);
};

const AddFilterButton = ({ filters, onAdd }) => {
	const [selectedFilterName, setSelectedFilterName] = useState(null);
	const [draftValues, setDraftValues] = useState([]);
	const [textValue, setTextValue] = useState('');
	const selectedFilter = filters.find(filter => filter.name === selectedFilterName);
	const hasDraftSelection = draftValues.some(selectedValue => selectedValue !== '');

	const chooseFilter = filterName => {
		const nextFilter = filters.find(filter => filter.name === filterName);
		const nextFilterName = selectedFilterName === filterName ? null : filterName;

		setSelectedFilterName(nextFilterName);
		setDraftValues(nextFilterName && !nextFilter.multiple ? [''] : []);
		setTextValue('');
	};
	const toggleDraftValue = (filter, optionId) => {
		setDraftValues(previousValues => {
			const isSelected = previousValues.some(value => String(value) === String(optionId));

			if (!filter.multiple) {
				return isSelected ? [] : [optionId];
			}

			return isSelected
				? previousValues.filter(value => String(value) !== String(optionId))
				: [...previousValues, optionId];
		});
	};
	const selectAllDraftValues = filter => setDraftValues((filter.options ?? []).map(option => option.id));
	const chooseFilterValue = (filter, value) => {
		onAdd(filter, value);
		setSelectedFilterName(null);
		setDraftValues([]);
		setTextValue('');
	};
	const applyDraftValues = filter => {
		const value = filter.multiple ? draftValues : draftValues.find(selectedValue => selectedValue !== '') ?? '';

		chooseFilterValue(filter, value);
	};
	const options = selectedFilter
		? selectedFilter.multiple
			? selectedFilter.options ?? []
			: [{ id: '', name: 'Any' }, ...(selectedFilter.options ?? [])]
		: [];

	return (
		<Menu as="div" className="relative shrink-0">
			{({ open }) => (
				<AddFilterMenuContent
					filters={filters}
					open={open}
					selectedFilter={selectedFilter}
					selectedFilterName={selectedFilterName}
					draftValues={draftValues}
					textValue={textValue}
					options={options}
					hasDraftSelection={hasDraftSelection}
					chooseFilter={chooseFilter}
					chooseFilterValue={chooseFilterValue}
					toggleDraftValue={toggleDraftValue}
					selectAllDraftValues={selectAllDraftValues}
					applyDraftValues={applyDraftValues}
					setTextValue={setTextValue}
				/>
			)}
		</Menu>
	);
};

const AddFilterMenuContent = ({
	filters,
	open,
	selectedFilter,
	selectedFilterName,
	draftValues,
	textValue,
	options,
	hasDraftSelection,
	chooseFilter,
	chooseFilterValue,
	toggleDraftValue,
	selectAllDraftValues,
	applyDraftValues,
	setTextValue,
}) => {
	const buttonRef = useRef(null);
	const panelRef = useRef(null);
	const selectedFilterButtonRef = useRef(null);
	const valuesPanelRef = useRef(null);

	useViewportAwarePopper(
		selectedFilterButtonRef,
		valuesPanelRef,
		open && Boolean(selectedFilter),
		'right-start',
		selectedFilterName
	);

	return (
		<>
			<Menu.Button ref={buttonRef} as={Button} size="sm" weight="clear">
				<Icon icon="plus" type="regular" />
				Add filter
			</Menu.Button>
			<PositionedPanel
				component={Menu.Items}
				open={open}
				referenceRef={buttonRef}
				panelRef={panelRef}
				placement="bottom-start"
				className="w-56 max-w-[calc(100vw-1rem)] rounded-md border border-gray-200 bg-white p-1 shadow-lg"
			>
				{filters.length ? (
					filters.map(filter => (
						<button
							key={filter.name}
							ref={element => {
								if (selectedFilterName === filter.name) {
									selectedFilterButtonRef.current = element;
								}
							}}
							type="button"
							role="menuitem"
							aria-label={filter.label}
							aria-expanded={selectedFilterName === filter.name}
							aria-controls={`add-filter-values-${filter.name}`}
							onClick={() => chooseFilter(filter.name)}
							className="flex w-full cursor-pointer items-center justify-between rounded px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
						>
							{filter.label}
							<Icon icon="chevron-right" type="regular" />
						</button>
					))
				) : (
					<p className="px-3 py-2 text-sm text-gray-500">All filters are shown.</p>
				)}
				{open && selectedFilter && (
					<div
						ref={valuesPanelRef}
						id={`add-filter-values-${selectedFilter.name}`}
						role="group"
						aria-label={`${selectedFilter.label} values`}
						className="fixed z-50 flex max-h-[calc(100vh-1rem)] w-56 max-w-[calc(100vw-1rem)] flex-col overflow-hidden rounded-md border border-gray-200 bg-white p-1 shadow-lg"
					>
						<div className="flex shrink-0 items-center justify-between gap-2 px-3 py-2">
							<p className="text-sm font-semibold text-gray-900">{selectedFilter.label}</p>
							{selectedFilter.allValuesLabel && selectedFilter.multiple && (
								<button
									type="button"
									aria-label={selectedFilter.allValuesLabel}
									onClick={() => selectAllDraftValues(selectedFilter)}
									className="shrink-0 text-xs font-medium text-purple-600 underline hover:text-purple-800 hover:no-underline"
								>
									{selectedFilter.allValuesLabel}
								</button>
							)}
						</div>
						{selectedFilter.type === 'text' ? (
							<div className="space-y-2 px-3 pb-2">
								<input
									aria-label={`Value for ${selectedFilter.label}`}
									autoFocus
									value={textValue}
									onChange={event => setTextValue(event.target.value)}
									className="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500"
								/>
								<Menu.Item
									as="button"
									type="button"
									disabled={!textValue.trim()}
									onClick={() => chooseFilterValue(selectedFilter, textValue)}
									className="block w-full rounded px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
								>
									Apply
								</Menu.Item>
							</div>
						) : (
							<div className="flex min-h-0 flex-1 flex-col">
								<div className="min-h-0 flex-1 space-y-1 overflow-y-auto">
									{options.map(option => (
										<label
											key={String(option.id)}
											className="flex cursor-pointer items-center gap-2 rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
										>
											<input
												type={selectedFilter.multiple ? 'checkbox' : 'radio'}
												name={`new-filter-${selectedFilter.name}`}
												checked={draftValues.some(value => String(value) === String(option.id))}
												onChange={() => toggleDraftValue(selectedFilter, option.id)}
												className="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
											/>
											<FilterOptionDisplay filter={selectedFilter} option={option} />
										</label>
									))}
								</div>
								<div className="shrink-0 border-t border-gray-200 px-2 pt-2">
									<Menu.Item
										as={Button}
										type="button"
										intent={hasDraftSelection ? 'primary' : 'secondary'}
										size="sm"
										onClick={() => applyDraftValues(selectedFilter)}
										className="w-full"
									>
										<Icon icon="fa-check" />
										Apply
									</Menu.Item>
								</div>
							</div>
						)}
					</div>
				)}
			</PositionedPanel>
		</>
	);
};

const FilterValueSummary = ({ filter, value }) => {
	const selectedOptions = getSelectedFilterOptions(filter, value);

	if (selectedOptions.length !== 1) {
		return getFilterSummary(filter, value);
	}

	return <FilterOptionDisplay filter={filter} option={selectedOptions[0]} compact />;
};

const FilterOptionDisplay = ({ filter, option, compact = false }) => {
	if (option.id === '') {
		return option.name;
	}

	if (filter.renderOption) {
		return filter.renderOption(option, compact);
	}

	return (
		<Badge colour="bg-gray-100 text-gray-800" compact={compact}>
			{option.name}
		</Badge>
	);
};

const SortDropdown = ({ sorts, form, compact = false, autoSubmit = false }) => {
	const activeSort = sorts.find(sort => String(sort.id) === String(form.data.sort))?.name ?? 'Sort';
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
						aria-label={compact ? 'Sort options' : `Sort: ${activeSort}`}
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
										name="index-table-sort"
										checked={String(form.data.sort) === String(sort.id)}
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
									intent={form.data.sortDir === direction.value ? 'selected' : 'secondary'}
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

const TableActionButton = ({ action }) => (
	<Button
		href={action.url}
		onClick={action.onClick}
		size="sm"
		weight="outline"
		className="h-8"
		{...(action.download ? { external: true, download: action.download } : {})}
	>
		{action.icon && <Icon icon={action.icon} type="regular" />}
		{action.label}
	</Button>
);

const MobileTableActions = ({ tableActions, tableActionContent, filterLabel }) => (
	<Menu as="div" className="relative">
		<Menu.Button as={Button} size="sm" weight="outline" className="h-8" aria-label={`${filterLabel || 'Table'} table actions`}>
			<Icon icon="cog" />
		</Menu.Button>
		<Menu.Items className="absolute right-0 z-50 mt-2 w-52 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none">
			{tableActions.map(action => (
				<ActionMenuItem
					key={action.id ?? action.label}
					url={action.url}
					onClick={action.onClick}
					download={action.download}
				>
					{action.icon && <Icon icon={action.icon} mr />}
					{action.label}
				</ActionMenuItem>
			))}
			{tableActionContent}
		</Menu.Items>
	</Menu>
);

const PositionedPanel = ({ component: PanelComponent, open, referenceRef, panelRef, placement, className, children }) => {
	useViewportAwarePopper(referenceRef, panelRef, open, placement);

	if (!open) {
		return null;
	}

	return (
		<Portal>
			<PanelComponent ref={panelRef} className={`fixed z-50 ${className}`}>
				{children}
			</PanelComponent>
		</Portal>
	);
};

const useViewportAwarePopper = (referenceRef, panelRef, open, placement, positionKey) => {
	useEffect(() => {
		const reference = referenceRef.current;
		const panel = panelRef.current;

		if (!open || !reference || !panel) {
			return undefined;
		}

		const popper = new Popper(reference, panel, {
			placement,
			positionFixed: true,
			modifiers: {
				offset: { offset: '0, 8' },
				flip: { boundariesElement: 'viewport', padding: 8 },
				preventOverflow: { boundariesElement: 'viewport', padding: 8 },
			},
		});

		return () => popper.destroy();
	}, [open, panelRef, placement, positionKey, referenceRef]);
};

function hasSelectedValue(filter, value) {
	if (filter.multiple) {
		return (
			Array.isArray(value) &&
			value.some(selectedValue => selectedValue !== null && selectedValue !== undefined && selectedValue !== '')
		);
	}

	return value !== null && value !== undefined && String(value).trim() !== '';
}

function isNonDefault(filter, value) {
	return normalizeValue(filter, value) !== normalizeValue(filter, filter.defaultValue ?? (filter.multiple ? [] : ''));
}

function normalizeValue(filter, value) {
	if (filter.multiple) {
		return (Array.isArray(value) ? value : [])
			.map(selectedValue => String(selectedValue))
			.sort()
			.join(',');
	}

	return String(value ?? '');
}

function formatDateInputValue(value) {
	if (!value) {
		return '';
	}

	const stringValue = String(value);
	const isoDate = stringValue.match(/^\d{4}-\d{2}-\d{2}/)?.[0];

	if (isoDate) {
		return isoDate;
	}

	const date = value instanceof Date ? value : new Date(value);

	if (Number.isNaN(date.getTime())) {
		return '';
	}

	const month = String(date.getMonth() + 1).padStart(2, '0');
	const day = String(date.getDate()).padStart(2, '0');

	return `${date.getFullYear()}-${month}-${day}`;
}

function getFilterSummary(filter, value) {
	if (!hasSelectedValue(filter, value)) {
		return 'Any';
	}

	if (filter.type === 'text') {
		return String(value);
	}

	const selectedValues = (filter.multiple ? value : [value]).filter(
		selectedValue => selectedValue !== null && selectedValue !== undefined && selectedValue !== ''
	);
	const labels = selectedValues.map(
		selectedValue =>
			filter.options?.find(option => String(option.id) === String(selectedValue))?.name ?? String(selectedValue)
	);

	return labels.length === 1 ? labels[0] : `${labels.length} selected`;
}

function getSelectedFilterOptions(filter, value) {
	if (filter.type === 'text') {
		return [];
	}

	const selectedValues = filter.multiple ? (Array.isArray(value) ? value : []) : [value];

	return selectedValues
		.filter(selectedValue => selectedValue !== null && selectedValue !== undefined && selectedValue !== '')
		.map(
			selectedValue =>
				filter.options?.find(option => String(option.id) === String(selectedValue)) ?? {
					id: selectedValue,
					name: String(selectedValue),
				}
		);
}

export default IndexTableControls;