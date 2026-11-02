import React, { useEffect, useRef, useState } from 'react';
import { Menu, Popover } from '@headlessui/react';
import Badge from '../../components/Badge';
import FeeStatus from '../../components/FeeStatus';
import Button from '../../components/inputs/ButtonV2';
import Icon from '../../components/Icon';
import SingerStatus from '../../SingerStatus';
import SingerStatusTag from '../../components/SingerStatusTag';
import VoicePartTag from '../../components/VoicePartTag';
import { PositionedPanel, useViewportAwarePopper } from './SingerPositionedPanel';
import SingerSortDropdown from './SingerSortDropdown';

const SingersFilterControlsPrototype = ({ filters, sorts, form, tableActions }) => {
	const firstUpdate = useRef(true);
	const [addedFilters, setAddedFilters] = useState([]);
	const nameFilter = filters.find(filter => filter.name === 'user.name');
	const filtersForPills = filters.filter(filter => filter !== nameFilter);

	useEffect(() => {
		if (firstUpdate.current) {
			firstUpdate.current = false;
			return;
		}

		const timeout = window.setTimeout(() => form.submit(undefined, { preserveState: true }), 300);

		return () => window.clearTimeout(timeout);
	}, [form.data]);

	const visibleFilters = filtersForPills.filter(
		filter =>
			hasSelectedValue(filter, form.data[filter.name]) ||
			hasSelectedValue(filter, filter.defaultValue) ||
			addedFilters.includes(filter.name)
	);
	const availableFilters = filtersForPills.filter(filter => !visibleFilters.includes(filter));
	const addFilter = (filter, value) => {
		setAddedFilters(previousFilters => [...new Set([...previousFilters, filter.name])]);
		changeFilter(filter, value);
	};
	const removeAddedFilter = filterName =>
		setAddedFilters(previousFilters => previousFilters.filter(addedFilter => addedFilter !== filterName));
	const changeFilter = (filter, value) => form.setData(filter.name, value);
	const clearFilter = filter => {
		const emptyValue = filter.multiple ? (hasSelectedValue(filter, filter.defaultValue) ? [''] : []) : '';
		changeFilter(filter, emptyValue);
		if (!hasSelectedValue(filter, filter.defaultValue)) {
			removeAddedFilter(filter.name);
		}
	};
	const resetFilter = filter => {
		changeFilter(filter, filter.defaultValue ?? (filter.multiple ? [] : ''));
		removeAddedFilter(filter.name);
	};
	const clearAllFilters = () => filters.forEach(clearFilter);
	const resetAllFilters = () => filters.forEach(resetFilter);
	const nameSearchInput = nameFilter ? (
		<NameSearchInput value={form.data[nameFilter.name]} onChange={value => changeFilter(nameFilter, value)} />
	) : null;

	const pills = visibleFilters.map(filter => (
		<FilterPill
			key={filter.name}
			filter={filter}
			value={form.data[filter.name]}
			nonDefault={isNonDefault(filter, form.data[filter.name])}
			onChange={value => changeFilter(filter, value)}
			onClear={() => clearFilter(filter)}
			onReset={() => resetFilter(filter)}
		/>
	));
	const addFilterButton = <AddFilterButton filters={availableFilters} onAdd={addFilter} />;
	const filterRowActions = (
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
	);
	const actions = <TableActionControls sorts={sorts} form={form} tableActions={tableActions} />;

	return (
		<div className="bg-white px-4 py-2 sm:px-6 md:px-8">
			<div className="grid grid-cols-1 gap-3 rounded-lg lg:grid-cols-[minmax(0,1fr)_auto]">
				<div className="min-w-0 lg:flex lg:flex-wrap lg:items-center lg:gap-2">
					<div className="flex items-center gap-2 lg:contents">
						<span className="hidden text-xs font-semibold uppercase tracking-wide text-gray-500 sm:inline">
							Filters
						</span>
						{nameSearchInput}
						<div className="lg:order-3">{addFilterButton}</div>
					</div>
					<div className="mt-2 flex min-w-0 items-center gap-2 overflow-x-auto pb-1 lg:contents">{pills}</div>
					<div className="mt-2 lg:order-4 lg:mt-0">{filterRowActions}</div>
				</div>
				<div className="flex items-center justify-end border-t border-gray-200 pt-2 lg:border-l lg:border-t-0 lg:pl-3 lg:pt-0">
					{actions}
				</div>
			</div>
		</div>
	);
};

const NameSearchInput = ({ value, onChange }) => (
	<label className="relative block w-40 shrink-0 grow md:grow-0">
		<span className="sr-only">Search singers by name</span>
		<input
			type="text"
			role="searchbox"
			aria-label="Search singers by name"
			placeholder="Search singers"
			value={value ?? ''}
			onChange={event => onChange(event.target.value)}
			className="block h-[26px] w-full rounded-full border-gray-300 py-0 pl-8 pr-8 text-xs shadow-none focus:border-purple-500 focus:ring-purple-500"
		/>
		<span
			aria-hidden="true"
			className="pointer-events-none absolute inset-y-0 left-2.5 flex items-center text-gray-400"
		>
			<Icon icon="search" size="text-xs" type="regular" />
		</span>
		{value && (
			<button
				type="button"
				aria-label="Clear singer name search"
				onClick={() => onChange('')}
				className="absolute inset-y-0 right-1 flex w-6 items-center justify-center rounded-full text-gray-400 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500"
			>
				<Icon icon="times" size="text-xs" type="regular" />
			</button>
		)}
	</label>
);

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
				className="w-72 max-w-[calc(100vw-1rem)] max-h-[calc(100vh-1rem)] overflow-y-auto rounded-md border border-gray-200 bg-white p-3 shadow-lg"
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
					{filter.name === 'fee_status' ? (
						<button
							type="button"
							onClick={() => onChange(filter.options.map(option => option.id))}
							className="shrink-0 text-xs font-medium text-purple-600 underline hover:text-purple-800 hover:no-underline"
						>
							All
						</button>
					) : !filter.multiple ? (
						<div className="text-xs text-gray-500">
							<FilterValueSummary filter={filter} value={value} />
						</div>
					) : null}
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
			<label className="block text-sm font-medium text-gray-700" htmlFor={`filter-${filter.name}`}>
				Search name
				<input
					id={`filter-${filter.name}`}
					type="search"
					value={value ?? ''}
					onChange={event => onChange(event.target.value)}
					className="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500"
				/>
			</label>
		);
	}

	const values = filter.multiple ? (Array.isArray(value) ? value : []) : [value ?? ''];
	const options = filter.multiple ? filter.options : [{ id: '', name: 'Any' }, ...filter.options];

	return (
		<div className="max-h-60 space-y-1 overflow-y-auto">
			{options.map(option => {
				const checked = values.some(selectedValue => String(selectedValue) === String(option.id));

				return (
					<label
						key={option.id}
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
										? selectedValues.filter(
											selectedValue => String(selectedValue) !== String(option.id)
										)
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
	const selectAllDraftValues = filter => setDraftValues(filter.options.map(option => option.id));

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
		? selectedFilter.type === 'text'
			? []
			: selectedFilter.multiple
			? selectedFilter.options
			: [{ id: '', name: 'Any' }, ...selectedFilter.options]
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
					applyDraftValues={applyDraftValues}
					selectAllDraftValues={selectAllDraftValues}
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
	applyDraftValues,
	selectAllDraftValues,
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
						<p className="shrink-0 px-3 py-2 text-sm font-semibold text-gray-900">{selectedFilter.label}</p>
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
								{selectedFilter.name === 'fee_status' && (
									<div className="flex shrink-0 justify-end px-3 pb-1">
										<button
											type="button"
											onClick={() => selectAllDraftValues(selectedFilter)}
											className="text-xs font-medium text-purple-600 underline hover:text-purple-800 hover:no-underline"
										>
											All
										</button>
									</div>
								)}
								<div className="min-h-0 flex-1 space-y-1 overflow-y-auto">
									{options.map(option => (
										<label
											key={String(option.id)}
											className="flex cursor-pointer items-center gap-2 rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
										>
											<input
												type="checkbox"
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

	if (filter.name === 'status' && SingerStatus.statuses[option.id]) {
		return <SingerStatusTag status={new SingerStatus(option.id)} withLabel compact={compact} />;
	}

	if (filter.name === 'enrolments.voice_part_id') {
		return <VoicePartTag title={option.name} colour={option.colour} compact={compact} />;
	}

	if (filter.name === 'enrolments.ensemble_id') {
		return (
			<Badge colour="bg-purple-100 text-purple-800" compact={compact}>
				{option.name}
			</Badge>
		);
	}

	if (filter.name === 'fee_status') {
		return <FeeStatus status={option.id} compact={compact} />;
	}

	return (
		<Badge colour="bg-gray-100 text-gray-800" compact={compact}>
			{option.name}
		</Badge>
	);
};

const TableActionControls = ({ sorts, form, tableActions }) => {
	return (
		<div className="flex shrink-0 items-center gap-2">
			<SingerSortDropdown sorts={sorts} form={form} />
			{tableActions.map(action => (
				<Button
					key={action.label}
					href={action.url}
					onClick={action.onClick}
						size="sm"
						weight="outline"
						className="h-8"
						external={action.download}
					download={action.download}
				>
					<Icon icon={action.icon} type="regular" />
					{action.label}
				</Button>
			))}
		</div>
	);
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
			filter.options.find(option => String(option.id) === String(selectedValue))?.name ?? String(selectedValue)
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
				filter.options.find(option => String(option.id) === String(selectedValue)) ?? {
					id: selectedValue,
					name: String(selectedValue),
				}
		);
}

export default SingersFilterControlsPrototype;
