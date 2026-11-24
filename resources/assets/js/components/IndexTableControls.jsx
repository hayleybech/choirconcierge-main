import React, { useEffect, useRef, useState } from 'react';
import { Menu, Popover, Portal } from '@headlessui/react';
import Popper from 'popper.js';
import ActionMenuItem from './ActionMenu/ActionMenuItem';
import Button from './inputs/ButtonV2';
import DateRangeCalendarInput from './inputs/DateRangeCalendarInput';
import Icon from './Icon';
import { linkStyles } from './StyledLink';
import { useMediaQuery } from 'react-responsive';
import { DateTime } from 'luxon';
import FilterFieldRenderer, { renderFilterOption } from './FilterFieldRenderer';
import {
	getClearedFilterValue,
	getDefaultFilterValue,
	getFilterData,
	getFilterName,
	getFilterOptions,
	getFilterValue,
	getSelectedFilterOptions,
	hasSelectedFilterValue as hasSelectedValue,
	isMultipleFilter,
	isFilterValueNonDefault as isNonDefault,
} from './filterDescriptorUtils';

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
	showMobileDateRangeFilter = false,
}) => {
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });
	const previousData = useRef(form.data);
	const [addedFilters, setAddedFilters] = useState([]);
	const visibleFilterDescriptors = filters.filter(filter => filter.visible !== false);
	const autoSubmitFilters = visibleFilterDescriptors.filter(filter => ['date', 'text'].includes(filter.type));
	const autoSubmitFiltersRef = useRef(autoSubmitFilters);
	autoSubmitFiltersRef.current = autoSubmitFilters;
	const searchableFilters = visibleFilterDescriptors.filter(
		filter => ['date', 'text'].includes(filter.type) && filter.toolbarMode !== 'pill'
	);
	const filtersForPills = visibleFilterDescriptors
		.filter(filter => filter.toolbarMode === 'pill' || !['date', 'text'].includes(filter.type))
		.sort(
			(firstFilter, secondFilter) =>
				Number(secondFilter.type === 'daterange') - Number(firstFilter.type === 'daterange')
		);
	const mobileDateRangeFilter = showMobileDateRangeFilter
		? visibleFilterDescriptors.find(filter => filter.type === 'daterange')
		: null;

	useEffect(() => {
		if (previousData.current === form.data) {
			return undefined;
		}

		const hasChangedSearch = autoSubmitFiltersRef.current.some(
			filter => getFilterValue(filter, previousData.current) !== getFilterValue(filter, form.data)
		);
		previousData.current = form.data;

		if (!isDesktop && !hasChangedSearch) {
			return undefined;
		}

		const timeout = window.setTimeout(() => form.submit(undefined, { preserveState: true }), 300);

		return () => window.clearTimeout(timeout);
	}, [form.data, isDesktop]);

	const visibleFilters = filtersForPills.filter(filter => {
		const value = getFilterValue(filter, form.data);

		if (filter.toolbarOnlyWhenNonDefault) {
			return isNonDefault(filter, value);
		}

		return (
			filter.toolbarAlwaysVisible ||
			hasSelectedValue(filter, value) ||
			hasSelectedValue(filter, getDefaultFilterValue(filter)) ||
			addedFilters.includes(getFilterName(filter))
		);
	});
	const changedFiltersCount = visibleFilterDescriptors.filter(filter =>
		isNonDefault(filter, getFilterValue(filter, form.data))
	).length;
	const hasChangedFilters = hasChangedFiltersOverride ?? changedFiltersCount > 0;
	const availableFilters = filtersForPills.filter(filter => !visibleFilters.includes(filter));
	const changeFilter = (filter, value) => {
		if (filter.onChange) {
			filter.onChange(value, form);
			return;
		}

		Object.entries(getFilterData(filter, value)).forEach(([name, filterValue]) => form.setData(name, filterValue));
	};
	const addFilter = (filter, value) => {
		setAddedFilters(previousFilters =>
			previousFilters.includes(getFilterName(filter))
				? previousFilters
				: [...previousFilters, getFilterName(filter)]
		);
		changeFilter(filter, value);
	};
	const removeAddedFilter = filterName =>
		setAddedFilters(previousFilters => previousFilters.filter(addedFilter => addedFilter !== filterName));
	const clearFilter = filter => {
		changeFilter(filter, getClearedFilterValue(filter));

		if (!hasSelectedValue(filter, getDefaultFilterValue(filter))) {
			removeAddedFilter(getFilterName(filter));
		}
	};
	const resetFilter = filter => {
		const defaultValue = getDefaultFilterValue(filter);
		changeFilter(filter, defaultValue);
		removeAddedFilter(getFilterName(filter));
	};
	const resetAllFilters = () => visibleFilterDescriptors.forEach(resetFilter);

	return (
		<div className="bg-white px-4 py-2 sm:px-6 md:px-8 border-b border-gray-200 lg:border-b-0">
			<div
				aria-label="Filters"
				className="hidden items-center gap-3 lg:flex"
				data-testid="desktop-index-table-controls"
			>
				<div className="flex min-w-0 flex-1 flex-wrap items-center gap-2">
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
						{visibleFilters.map(filter => {
							const value = getFilterValue(filter, form.data);

							return (
								<FilterPill
									key={getFilterName(filter)}
									filter={filter}
									value={value}
									nonDefault={isNonDefault(filter, value)}
									onChange={value => changeFilter(filter, value)}
									onClear={() => clearFilter(filter)}
									onReset={() => resetFilter(filter)}
								/>
							);
						})}
					</div>
					{availableFilters.length > 0 && <AddFilterButton filters={availableFilters} onAdd={addFilter} />}
					<div className="flex shrink-0 items-center gap-1">
						{visibleFilters.length > 0 && (
							<Button
								type="button"
								size="xs"
								intent="secondary"
								weight="clear"
								className="h-6 text-xs"
								onClick={resetAllFilters}
							>
								<Icon icon="undo" type="regular" />
								Reset all filters
							</Button>
						)}
					</div>
				</div>
				<div className="sticky right-0 z-20 ml-auto flex shrink-0 items-center gap-2 bg-white">
					{sorts.length > 0 && <SortDropdown sorts={sorts} form={form} />}
					{tableActions.map(action => (
						<TableActionButton key={action.id ?? action.label} action={action} />
					))}
					{tableActionContent}
				</div>
			</div>

			<div
				aria-label={`${filterLabel || 'Table'} table controls`}
				className="flex items-center gap-2 lg:hidden"
				data-testid="mobile-index-table-controls"
			>
				{bulkEdit?.isActiveMobile && (
					<input
						ref={bulkEdit.refSelectAll}
						type="checkbox"
						checked={bulkEdit.totalItems > 0 && (bulkEdit.selectedIds ?? []).length === bulkEdit.totalItems}
						onChange={bulkEdit.toggleAll}
						aria-label={`Select all ${filterLabel || 'items'}`}
						className="shrink-0 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
					/>
				)}
				<div className="flex items-center gap-2 w-full justify-end">
					{mobileDateRangeFilter && (
						<div className="min-w-0 flex-1">
							<DateRangeCalendarInput
								ariaLabel={
									mobileDateRangeFilter.ariaLabel ?? `${mobileDateRangeFilter.label} date range`
								}
								startDate={form.data[mobileDateRangeFilter.start_name]}
								endDate={form.data[mobileDateRangeFilter.end_name]}
								startMonth={mobileDateRangeFilter.startMonth}
								endMonth={mobileDateRangeFilter.endMonth}
								mobile
								updateFn={value => changeFilter(mobileDateRangeFilter, value)}
							>
								{mobileDateRangeFilter.children}
							</DateRangeCalendarInput>
						</div>
					)}
					{searchableFilters.map(filter => (
						<NameSearchInput
							key={filter.name}
							filter={filter}
							filterLabel={filterLabel}
							value={form.data[filter.name]}
							onChange={value => changeFilter(filter, value)}
							className="w-32 shrink-0 grow md:grow-0"
						/>
					))}
					{filtersForPills.length > 0 && (
						<Button
							size="sm"
							weight="outline"
							intent={hasChangedFilters ? 'selected' : 'secondary'}
							className="h-6"
							onClick={onToggleFilters}
							aria-label={`Filter ${filterLabel || 'items'}`}
						>
							<Icon icon="filter" type="regular" />
							Filters{changedFiltersCount > 0 && ` (${changedFiltersCount})`}
						</Button>
					)}
					{sorts.length > 0 && <SortDropdown sorts={sorts} form={form} compact autoSubmit={!isDesktop} />}
					{(tableActions.length > 0 || tableActionContent || bulkEdit?.action) && (
						<MobileTableActions
							tableActions={tableActions}
							tableActionContent={tableActionContent}
							bulkEdit={bulkEdit}
							filterLabel={filterLabel}
						/>
					)}
				</div>
			</div>
		</div>
	);
};

const NameSearchInput = ({ filter, filterLabel, value, onChange, className = 'w-40 shrink-0 grow md:grow-0' }) => {
	return (
		<FilterFieldRenderer
			filter={filter}
			value={value}
			onChange={onChange}
			variant="toolbar"
			layout="search"
			filterLabel={filterLabel}
			className={className}
		/>
	);
};

const FilterPill = props => {
	if (props.filter.toolbarMode === 'select') {
		return (
			<div className="shrink-0">
				<FilterFieldRenderer
					filter={props.filter}
					value={props.value}
					onChange={props.onChange}
					variant="toolbar"
					layout="pill"
				/>
			</div>
		);
	}

	return (
		<Popover as="div" className="shrink-0">
			{({ open, close }) => <FilterPillContent {...props} open={open} close={close} />}
		</Popover>
	);
};

const FilterPillContent = ({ filter, value, nonDefault, onChange, onClear, onReset, open, close }) => {
	const buttonRef = useRef(null);
	const panelRef = useRef(null);
	const draftRangeRef = useRef(value);

	useEffect(() => {
		if (filter.type === 'daterange' && open) {
			draftRangeRef.current = value;
		}
	}, [filter.type, open, value?.from, value?.to]);

	return (
		<>
			<Popover.Button
				ref={buttonRef}
				as={Button}
				size="sm"
				intent={nonDefault ? 'selected' : 'secondary'}
				weight="outline"
				aria-label={`${filter.label}: ${getFilterSummary(filter, value)}; open filter values`}
				className="h-6 rounded"
			>
				{filter.type !== 'daterange' && <span>{filter.label}:</span>}
				<div className={`${filter.type === 'daterange' ? 'max-w-auto' : 'max-w-32'} truncate text-xs`}>
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
				className={`max-h-[calc(100vh-1rem)] ${
					filter.type === 'daterange'
						? 'w-80 max-w-[calc(100vw-1rem)] overflow-visible'
						: 'w-72 max-w-[calc(100vw-1rem)] overflow-y-auto'
				} rounded-md border border-gray-200 bg-white p-3 shadow-lg`}
			>
				<div className="mb-3 flex items-center justify-between gap-2">
					<div className="flex min-w-0 items-center gap-2">
						<h3 className="text-sm font-semibold text-gray-900">{filter.label}</h3>
						{isMultipleFilter(filter) && (
							<span className="shrink-0 text-xs text-gray-500">
								{getSelectedFilterOptions(filter, value).length} selected
							</span>
						)}
					</div>
					{!isMultipleFilter(filter) && (
						<div className="text-xs text-gray-500">
							<FilterValueSummary filter={filter} value={value} />
						</div>
					)}
					{isMultipleFilter(filter) && getFilterOptions(filter).length > 0 && (
						<button
							type="button"
							onClick={() => onChange(getFilterOptions(filter).map(option => option.id))}
							className={linkStyles('secondary', 'shrink-0 text-xs')}
						>
							Select All
						</button>
					)}
				</div>
				<FilterValueOptions
					filter={filter}
					value={value}
					onChange={onChange}
					inline={filter.type === 'daterange'}
					onClose={close}
					onDraftChange={range => {
						draftRangeRef.current = range;
					}}
					hideActions={filter.type === 'daterange'}
				/>
				<div className="mt-3 flex justify-between gap-2 border-t border-gray-200 pt-3">
					<div className="flex">
						{filter.type === 'daterange' && (
							<Button
								size="sm"
								intent="primary"
								onClick={() => {
									onChange(draftRangeRef.current);
									close();
								}}
							>
								<Icon icon="check" type="regular" /> Apply
							</Button>
						)}
					</div>
					<div className="flex items-center gap-2">
						<button
							type="button"
							className={linkStyles('secondary', 'inline-flex items-center gap-1 text-xs')}
							onClick={onClear}
						>
							<Icon icon="trash" type="regular" /> Clear
						</button>
						<button
							type="button"
							className={linkStyles('secondary', 'inline-flex items-center gap-1 text-xs')}
							onClick={onReset}
						>
							<Icon icon="undo" type="regular" />
							Default
						</button>
					</div>
				</div>
			</PositionedPanel>
		</>
	);
};

const FilterValueOptions = ({
	filter,
	value,
	onChange,
	inline = false,
	onClose,
	onDraftChange,
	hideActions = false,
}) => (
	<FilterFieldRenderer
		filter={filter}
		value={value}
		onChange={onChange}
		variant="toolbar"
		layout="options"
		inline={inline}
		onClose={onClose}
		onDraftChange={onDraftChange}
		hideActions={hideActions}
	/>
);

const AddFilterButton = ({ filters, onAdd }) => {
	const [selectedFilterName, setSelectedFilterName] = useState(null);
	const [draftValues, setDraftValues] = useState([]);
	const [textValue, setTextValue] = useState('');
	const selectedFilter = filters.find(filter => filter.name === selectedFilterName);

	const chooseFilter = filterName => {
		const nextFilter = filters.find(filter => filter.name === filterName);
		const nextFilterName = selectedFilterName === filterName ? null : filterName;

		setSelectedFilterName(nextFilterName);
		setDraftValues(nextFilterName && !nextFilter.multiple ? [''] : []);
		setTextValue('');
	};
	const selectAllDraftValues = filter => setDraftValues(getFilterOptions(filter).map(option => option.id));
	const chooseFilterValue = (filter, value) => {
		onAdd(filter, value);
		setSelectedFilterName(null);
		setDraftValues([]);
		setTextValue('');
	};
	const cancelFilterValue = () => {
		setSelectedFilterName(null);
		setDraftValues([]);
		setTextValue('');
	};
	const applyDraftValues = filter => {
		const value = isMultipleFilter(filter)
			? draftValues
			: draftValues.find(selectedValue => selectedValue !== '') ?? '';

		chooseFilterValue(filter, value);
	};
	const options = selectedFilter ? getFilterOptions(selectedFilter) : [];

	return (
		<Menu as="div" className="relative shrink-0">
			{({ open, close }) => (
				<AddFilterMenuContent
					filters={filters}
					open={open}
					closeMenu={close}
					selectedFilter={selectedFilter}
					selectedFilterName={selectedFilterName}
					draftValues={draftValues}
					textValue={textValue}
					options={options}
					chooseFilter={chooseFilter}
					chooseFilterValue={chooseFilterValue}
					setDraftValues={setDraftValues}
					selectAllDraftValues={selectAllDraftValues}
					applyDraftValues={applyDraftValues}
					cancelFilterValue={cancelFilterValue}
					setTextValue={setTextValue}
				/>
			)}
		</Menu>
	);
};

const AddFilterMenuContent = ({
	filters,
	open,
	closeMenu,
	selectedFilter,
	selectedFilterName,
	draftValues,
	textValue,
	options,
	chooseFilter,
	chooseFilterValue,
	selectAllDraftValues,
	applyDraftValues,
	cancelFilterValue,
	setTextValue,
	setDraftValues,
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
			<Menu.Button ref={buttonRef} as={Button} size="sm" weight="clear" className="h-6">
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
							className="flex w-full cursor-pointer items-center justify-between rounded px-3 py-2 text-left text-sm text-gray-700 hover:bg-purple-100 focus:bg-purple-100 focus:outline-none"
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
						className={`fixed z-50 flex max-h-[calc(100vh-1rem)] flex-col ${
							selectedFilter.type === 'daterange'
								? 'w-[22rem] max-w-[calc(100vw-1rem)] overflow-visible'
								: 'w-64 max-w-[calc(100vw-1rem)] overflow-visible'
						} rounded-md border border-gray-200 bg-white p-1 shadow-lg`}
					>
						<div className="flex shrink-0 items-center justify-between gap-2 px-3 py-2">
							<p className="text-sm font-semibold text-gray-900">{selectedFilter.label}</p>
							{isMultipleFilter(selectedFilter) && options.length > 0 && (
								<button
									type="button"
									aria-label="Select All"
									onClick={() => selectAllDraftValues(selectedFilter)}
									className={linkStyles('secondary', 'shrink-0 text-xs')}
								>
									Select All
								</button>
							)}
						</div>
						{selectedFilter.type === 'daterange' ? (
							<div className="shrink-0">
								<DateRangeCalendarInput
									ariaLabel={selectedFilter.ariaLabel ?? `${selectedFilter.label} date range`}
									startDate={getDefaultFilterValue(selectedFilter).from}
									endDate={getDefaultFilterValue(selectedFilter).to}
									startMonth={selectedFilter.startMonth}
									endMonth={selectedFilter.endMonth}
									updateFn={value => chooseFilterValue(selectedFilter, value)}
									inline
									onClose={closeMenu}
									renderActions={({ draftRange, updateDraftRange, selected, close }) => (
										<div className="mt-3 flex justify-between gap-2 border-t border-gray-200 pt-3">
											<div className="flex">
												<Button
													size="sm"
													intent="primary"
													role="menuitem"
													onClick={() => {
														chooseFilterValue(selectedFilter, draftRange);
														close();
													}}
												>
													<Icon icon="check" type="regular" /> Apply
												</Button>
											</div>
											<div className="flex gap-1">
												<Button
													size="sm"
													weight="clear"
													role="menuitem"
													onClick={() => updateDraftRange({})}
												>
													<Icon icon="trash" type="regular" /> Clear
												</Button>
												<Button
													size="sm"
													weight="clear"
													role="menuitem"
													onClick={() => {
														updateDraftRange(selected);
														cancelFilterValue();
														close();
														closeMenu();
													}}
												>
													<Icon icon="times" type="regular" /> Cancel
												</Button>
											</div>
										</div>
									)}
								>
									{selectedFilter.children}
								</DateRangeCalendarInput>
							</div>
						) : selectedFilter.type === 'text' ? (
							<div>
								<div className="px-3 pb-3">
									<input
										aria-label={`Value for ${selectedFilter.label}`}
										autoFocus
										value={textValue}
										onChange={event => setTextValue(event.target.value)}
										className="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500"
									/>
								</div>
								<div className="flex shrink-0 justify-between gap-1 border-t border-gray-200 px-2 pt-2 pb-1">
									<Menu.Item
										as={Button}
										type="button"
										disabled={!textValue.trim()}
										onClick={() => chooseFilterValue(selectedFilter, textValue)}
										intent="primary"
										size="sm"
										className="py-0.5 px-1"
									>
										<Icon icon="fa-check" />
										Apply
									</Menu.Item>
									<Menu.Item
										as={Button}
										type="button"
										role="menuitem"
										size="sm"
										weight="clear"
										className="py-0.5 px-1"
										onClick={() => {
											cancelFilterValue();
											closeMenu();
										}}
									>
										<Icon icon="times" type="regular" />
										Close
									</Menu.Item>
								</div>
							</div>
						) : (
							<div className="flex min-h-0 flex-1 flex-col">
								{selectedFilter.renderField ? (
									selectedFilter.renderField({
										value: isMultipleFilter(selectedFilter)
											? draftValues
											: draftValues.find(value => value !== '') ?? '',
										onChange: value => setDraftValues(Array.isArray(value) ? value : [value]),
										options,
										filter: selectedFilter,
										surface: 'add-filter',
									})
								) : (
									<FilterFieldRenderer
										filter={selectedFilter}
										value={
											isMultipleFilter(selectedFilter)
												? draftValues
												: draftValues.find(value => value !== '') ?? ''
										}
										onChange={value => setDraftValues(Array.isArray(value) ? value : [value])}
										options={options}
										variant="toolbar"
										layout="options"
										showEmptyOption={false}
										className="min-h-0 flex-1 px-2"
										optionClassName={`px-3 py-2 hover:bg-purple-100 ${selectedFilter.addFilterOptionClassName ??
											''}`}
										optionCompact={selectedFilter.addFilterOptionCompact ?? false}
									/>
								)}
								<div className="flex shrink-0 justify-between gap-1 border-t border-gray-200 px-2 pt-2 pb-1">
									<Button
										role="menuitem"
										type="button"
										intent="primary"
										size="sm"
										className="py-0.5 px-1"
										onClick={() => applyDraftValues(selectedFilter)}
									>
										<Icon icon="fa-check" />
										Apply
									</Button>
									<Button
										role="menuitem"
										type="button"
										size="sm"
										weight="clear"
										className="py-0.5 px-1"
										onClick={() => {
											cancelFilterValue();
											closeMenu();
										}}
									>
										<Icon icon="times" type="regular" />
										Close
									</Button>
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
	return renderFilterOption(filter, option, compact);
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
						className="h-6"
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
						<div
							role="group"
							aria-label="Sort direction"
							className="mt-3 flex border-t border-gray-200 pt-3"
						>
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
									className={`grow ${
										direction.value === 'asc' ? 'rounded-r-none' : '-ml-px rounded-l-none'
									}`}
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
		className="h-6"
		{...(action.download ? { external: true, download: action.download } : {})}
	>
		{action.icon && <Icon icon={action.icon} type="regular" />}
		{action.label}
	</Button>
);

const MobileTableActions = ({ tableActions, tableActionContent, bulkEdit, filterLabel }) => (
	<Menu as="div" className="relative">
		<Menu.Button
			as={Button}
			size="sm"
			weight="outline"
			className="h-6"
			aria-label={`${filterLabel || 'Table'} table actions`}
		>
			<Icon icon="cog" />
		</Menu.Button>
		<Menu.Items className="absolute right-0 z-50 mt-2 w-52 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none">
			{bulkEdit?.action && (
				<ActionMenuItem
					key="bulk-edit-action"
					onClick={bulkEdit.action.onClick}
					intent={bulkEdit.action.intent}
				>
					{bulkEdit.action.icon && <Icon icon={bulkEdit.action.icon} mr />}
					{bulkEdit.action.label}
				</ActionMenuItem>
			)}
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

const PositionedPanel = ({
	component: PanelComponent,
	open,
	referenceRef,
	panelRef,
	placement,
	className,
	children,
}) => {
	useViewportAwarePopper(referenceRef, panelRef, open, placement);

	if (!open) {
		return null;
	}

	return (
		<Portal>
			<PanelComponent ref={panelRef} className={`fixed z-[60] ${className}`}>
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

function getFilterSummary(filter, value) {
	if (!hasSelectedValue(filter, value)) {
		return 'Any';
	}

	if (filter.type === 'text') {
		return String(value);
	}
	if (filter.type === 'daterange') {
		return [value?.from, value?.to]
			.filter(Boolean)
			.map(formatDateRangeValue)
			.join(' – ');
	}

	const selectedValues = (isMultipleFilter(filter) ? value : [value]).filter(
		selectedValue => selectedValue !== null && selectedValue !== undefined && selectedValue !== ''
	);
	const options = getFilterOptions(filter);
	const labels = selectedValues.map(
		selectedValue =>
			options.find(option => String(option.id) === String(selectedValue))?.name ?? String(selectedValue)
	);

	return labels.length === 1 ? labels[0] : `${labels.length} selected`;
}

function formatDateRangeValue(value) {
	const date = value instanceof Date ? DateTime.fromJSDate(value) : DateTime.fromISO(String(value));

	return date.isValid ? date.toLocaleString(DateTime.DATE_MED) : String(value);
}

export default IndexTableControls;
