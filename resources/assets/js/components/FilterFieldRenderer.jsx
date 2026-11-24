import React from 'react';
import Badge from './Badge';
import DateRangeCalendarInput from './inputs/DateRangeCalendarInput';
import FilterActions from './inputs/FilterActions';
import Icon from './Icon';
import Label from './inputs/Label';
import TextInput from './inputs/TextInput';
import {
	formatDateInputValue,
	getClearedFilterValue,
	getDefaultFilterValue,
	getFilterOptions,
	isMultipleFilter,
	isFilterValueNonDefault,
} from './filterDescriptorUtils';
import CheckboxGroup from './inputs/CheckboxGroup';

const FilterFieldRenderer = ({
	filter,
	value,
	onChange,
	variant = 'drawer',
	layout = 'field',
	filterLabel,
	className = '',
	optionClassName = '',
	options: optionsOverride,
	optionsContext,
	inline = false,
	optionCompact = false,
	onClose,
	onDraftChange,
	hideActions = false,
	showEmptyOption = true,
	renderActions,
	onClear,
	onReset,
}) => {
	const options = optionsOverride ?? getFilterOptions(filter, optionsContext);
	const multiple = isMultipleFilter(filter);
	if (variant === 'toolbar' && filter.renderToolbarField) {
		return filter.renderToolbarField({ value, onChange, options, filter });
	}

	if (variant === 'drawer' && filter.renderField) {
		const defaultValue = getDefaultFilterValue(filter);
		const nonDefault = isFilterValueNonDefault(filter, value);

		return (
			<fieldset className="min-w-0 space-y-2">
				<div className="flex items-center justify-between gap-2">
					<div className="flex min-w-0 items-center gap-2">
						<legend
							className={`text-sm font-medium ${
								nonDefault && filter.modifiedLabelClassName
									? filter.modifiedLabelClassName
									: 'text-gray-700'
							}`}
						>
							{filter.label}
						</legend>
						{multiple && (
							<span className="shrink-0 text-xs text-gray-500">{getSelectedCount(value)} selected</span>
						)}
						{filter.showDefaultMarker && nonDefault && (
							<span
								title="Filter differs from default"
								className="h-1.5 w-1.5 shrink-0 rounded-full bg-purple-700"
							/>
						)}
					</div>
					{filter.showActions && (
						<FilterActions
							onSelectAll={
								multiple && options.length > 0 ? () => onChange(options.map(option => option.id)) : null
							}
							onClear={() => (onClear ? onClear() : onChange(getClearedFilterValue(filter)))}
							onReset={() => (onReset ? onReset() : onChange(defaultValue))}
							selectAllLabel={filter.selectAllLabel ?? 'Select All'}
							clearLabel={filter.clearLabel}
							resetDisabled={!nonDefault}
							resetAriaLabel={filter.resetAriaLabel ?? `Reset ${filter.label} filter`}
						/>
					)}
				</div>
				{filter.renderField({ value, onChange, options, filter, surface: 'drawer' })}
			</fieldset>
		);
	}

	if (layout === 'search') {
		const isDateInput = filter.type === 'date';
		const inputLabel = isDateInput
			? `${filter.label}${filterLabel ? ` for ${filterLabel}` : ''}`
			: `Search ${filterLabel ? `${filterLabel} by ${filter.label.toLowerCase()}` : filter.label}`;
		const clearLabel = isDateInput ? `Clear ${inputLabel}` : `Clear ${filterLabel || filter.label} search`;

		return (
			<label className={`relative block ${className || 'w-40 shrink-0 grow md:grow-0'}`}>
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
					placeholder={isDateInput ? '' : 'Search'}
					value={isDateInput ? formatDateInputValue(value) : value ?? ''}
					onChange={event => onChange(event.target.value)}
					className="block h-6 w-full rounded-full border-gray-300 py-0 pl-8 pr-8 text-xs shadow-none focus:border-purple-500 focus:ring-purple-500"
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
	}

	if (filter.type === 'daterange') {
		return (
			<fieldset className={filter.fieldClassName ?? 'space-y-2'}>
				{variant === 'drawer' && <legend className="text-sm font-medium text-gray-700">{filter.label}</legend>}
				<DateRangeCalendarInput
					ariaLabel={filter.ariaLabel ?? `${filter.label} date range`}
					startDate={value?.from}
					endDate={value?.to}
					startMonth={filter.startMonth}
					endMonth={filter.endMonth}
					updateFn={onChange}
					inline={inline}
					onClose={onClose}
					onDraftChange={onDraftChange}
					hideActions={hideActions}
					renderActions={renderActions}
				>
					{filter.children}
				</DateRangeCalendarInput>
			</fieldset>
		);
	}

	if (filter.type === 'text' || filter.type === 'date') {
		const defaultValue = getDefaultFilterValue(filter);
		const nonDefault = isFilterValueNonDefault(filter, value);
		const showDrawerActions = variant === 'drawer' && (filter.showActions || filter.showDefaultMarker);
		const isToolbarPopoverField = variant === 'toolbar' && layout === 'options';
		const input = (
			<div className={variant === 'drawer' || isToolbarPopoverField ? 'space-y-1' : 'space-y-1 px-3 pb-2'}>
				{!showDrawerActions && !isToolbarPopoverField && <Label label={filter.label} forInput={filter.name} />}
				<TextInput
					name={filter.name}
					type={filter.inputType ?? (filter.type === 'date' ? 'date' : 'text')}
					value={filter.type === 'date' ? formatDateInputValue(value) : value ?? ''}
					updateFn={onChange}
					size={variant === 'drawer' ? 'md' : 'sm'}
					placeholder={filter.placeholder}
					aria-label={filter.label}
					className={nonDefault ? 'border-purple-300 bg-purple-50' : ''}
				/>
			</div>
		);

		if (!showDrawerActions) {
			return input;
		}

		return (
			<fieldset className="space-y-2">
				<div className="flex items-center justify-between gap-2">
					<div className="flex min-w-0 items-center gap-2">
						<Label
							label={filter.label}
							forInput={filter.name}
							className={`text-sm font-medium ${
								nonDefault && filter.modifiedLabelClassName
									? filter.modifiedLabelClassName
									: 'text-gray-700'
							}`}
						/>
						{filter.showDefaultMarker && nonDefault && (
							<span
								title="Filter differs from default"
								className="h-1.5 w-1.5 shrink-0 rounded-full bg-purple-700"
							/>
						)}
					</div>
					{filter.showActions && (
						<FilterActions
							onClear={() => (onClear ? onClear() : onChange(getClearedFilterValue(filter)))}
							onReset={() => (onReset ? onReset() : onChange(defaultValue))}
							clearLabel={filter.clearLabel}
							resetDisabled={!nonDefault}
							resetAriaLabel={filter.resetAriaLabel ?? `Reset ${filter.label} filter`}
						/>
					)}
				</div>
				{input}
			</fieldset>
		);
	}

	if (variant === 'drawer') {
		const defaultValue = getDefaultFilterValue(filter);
		const nonDefault = isFilterValueNonDefault(filter, value);
		const clearValue = () => (onClear ? onClear() : onChange(getClearedFilterValue(filter)));
		const resetValue = () => (onReset ? onReset() : onChange(defaultValue));

		return (
			<fieldset className="space-y-2">
				<div className="flex items-center justify-between gap-2">
					<div className="flex min-w-0 items-center gap-2">
						<legend
							className={`text-sm font-medium ${
								nonDefault && filter.modifiedLabelClassName
									? filter.modifiedLabelClassName
									: 'text-gray-700'
							}`}
						>
							{filter.label}
						</legend>
						{multiple && (
							<span className="shrink-0 text-xs text-gray-500">{getSelectedCount(value)} selected</span>
						)}
						{filter.showDefaultMarker && nonDefault && (
							<span
								title="Filter differs from default"
								className="h-1.5 w-1.5 shrink-0 rounded-full bg-purple-700"
							/>
						)}
					</div>
					{filter.showActions && (
						<FilterActions
							onSelectAll={
								multiple && options.length > 0 ? () => onChange(options.map(option => option.id)) : null
							}
							onClear={clearValue}
							onReset={resetValue}
							selectAllLabel={filter.selectAllLabel ?? 'Select All'}
							clearLabel={filter.clearLabel}
							resetDisabled={!nonDefault}
							resetAriaLabel={filter.resetAriaLabel ?? `Reset ${filter.label} filter`}
						/>
					)}
				</div>
				{multiple ? (
					<CheckboxGroup
						name={filter.name}
						options={options}
						value={Array.isArray(value) ? value : []}
						defaultValue={Array.isArray(defaultValue) ? defaultValue : []}
						updateFn={onChange}
						renderOption={option => renderDrawerFilterOption(filter, option)}
					/>
				) : (
					<div className="space-y-2">
						{getChoiceOptions(filter, options, showEmptyOption).map(option => (
							<label
								key={String(option.id)}
								className="flex cursor-pointer items-center gap-3 text-sm text-gray-700"
							>
								<input
									type="radio"
									name={`filter-${filter.name}`}
									checked={String(value ?? '') === String(option.id)}
									onChange={() => onChange(option.id)}
									className="border-gray-300 text-purple-600 focus:ring-purple-500"
								/>
								{renderFilterOption(filter, option)}
							</label>
						))}
					</div>
				)}
			</fieldset>
		);
	}

	const selectedValues = multiple ? (Array.isArray(value) ? value : []) : [value ?? ''];

	return (
		<div data-filter-field-options className={`${className || 'max-h-60'} space-y-1 overflow-y-auto`}>
			{getChoiceOptions(filter, options, showEmptyOption).map(option => {
				const checked = selectedValues.some(selectedValue => String(selectedValue) === String(option.id));

				return (
					<label
						key={String(option.id)}
						className={`flex cursor-pointer items-center gap-2 rounded ${
							optionCompact ? 'text-xs' : 'text-sm'
						} text-gray-700 ${optionClassName || 'px-2 py-1.5 hover:bg-gray-50'}`}
					>
						<input
							type={multiple ? 'checkbox' : 'radio'}
							name={`filter-${filter.name}`}
							checked={checked}
							onChange={() => onChange(getNextChoiceValue(multiple, selectedValues, option, checked))}
							onClick={event => {
								if (!multiple && checked) {
									event.preventDefault();
									onChange(getNextChoiceValue(multiple, selectedValues, option, checked));
								}
							}}
							className="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
						/>
						{renderFilterOption(filter, option, optionCompact)}
					</label>
				);
			})}
		</div>
	);
};

export function renderFilterOption(filter, option, compact = false, isChanged = false) {
	if (option.id === '') {
		return getOptionLabel(option);
	}

	if (filter.renderOption) {
		return filter.renderOption(option, compact, isChanged);
	}

	return (
		<Badge colour="bg-gray-100 text-gray-800" compact={compact}>
			{getOptionLabel(option)}
		</Badge>
	);
}

export default FilterFieldRenderer;

function getChoiceOptions(filter, options, showEmptyOption) {
	return !showEmptyOption || isMultipleFilter(filter) || filter.allowEmpty === false
		? options
		: [{ id: '', name: filter.emptyOptionLabel ?? 'Any' }, ...options];
}

function getNextChoiceValue(multiple, selectedValues, option, checked) {
	if (!multiple) {
		return checked ? '' : option.id;
	}

	const nonEmptyValues = selectedValues.filter(selectedValue => selectedValue !== '');

	return checked
		? nonEmptyValues.filter(selectedValue => String(selectedValue) !== String(option.id))
		: [...nonEmptyValues, option.id];
}

function renderDrawerFilterOption(filter, option) {
	return renderFilterOption(filter, option, filter.drawerOptionCompact ?? false);
}

function getOptionLabel(option) {
	return option.name ?? option.label ?? String(option.id);
}

function getSelectedCount(value) {
	return Array.isArray(value)
		? value.filter(selectedValue => selectedValue !== null && selectedValue !== undefined && selectedValue !== '')
				.length
		: 0;
}
