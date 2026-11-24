import React from 'react';
import { components } from 'react-select';
import Select from 'react-select';
import Icon from '../Icon';
import reactSelectStyles from './reactSelectStyles';

const mailingListSelectStyles = {
	...reactSelectStyles,
	menu: base => ({
		...base,
		minWidth: '250px',
	}),
};

const MailingListSelect = ({
	options,
	defaultValue,
	value,
	updateFn,
	multiple = false,
	isClearable = true,
	prefixLabel,
	ariaLabel,
	toolbarPill = false,
	constrainWidth = false,
	truncateSelectedLabels = false,
	className = 'mt-1',
}) => (
	<div className={className}>
		<Select
			cacheOptions
			options={options}
			components={{
				SingleValue,
				MultiValueLabel,
				Option,
				...(toolbarPill
					? {
							ClearIndicator: ToolbarClearIndicator,
							DropdownIndicator: ToolbarDropdownIndicator,
							MenuList: ToolbarMenuList,
							ValueContainer: ToolbarValueContainer,
					  }
					: prefixLabel
					? { ValueContainer: PrefixedValueContainer }
					: {}),
			}}
			isMulti={multiple}
			isClearable={isClearable}
			isSearchable
			constrainWidth={constrainWidth}
			truncateSelectedLabels={truncateSelectedLabels}
			menuPosition={toolbarPill ? 'fixed' : 'absolute'}
			menuPortalTarget={toolbarPill && typeof document !== 'undefined' ? document.body : undefined}
			classNamePrefix="mailing-list-select"
			className={constrainWidth ? 'w-full min-w-0 max-w-full' : undefined}
			aria-label={ariaLabel}
			prefixLabel={prefixLabel}
			value={value ?? defaultValue ?? null}
			onChange={option =>
				multiple ? updateFn(option?.map(item => item.value) ?? []) : updateFn(option?.value ?? '')
			}
			styles={
				toolbarPill
					? toolbarPillStyles
					: constrainWidth
					? constrainedMailingListSelectStyles
					: mailingListSelectStyles
			}
		/>
	</div>
);

export default MailingListSelect;

const PrefixedValueContainer = ({ children, selectProps, ...props }) => (
	<components.ValueContainer {...props} selectProps={selectProps}>
		<span className="mr-1 shrink-0 text-xs text-gray-600">{selectProps.prefixLabel}</span>
		{children}
	</components.ValueContainer>
);

const ToolbarValueContainer = ({ children, selectProps, ...props }) => {
	const selectedOptions = Array.isArray(selectProps.value) ? selectProps.value : [];
	const colorClass = selectedOptions.length > 0 ? 'text-purple-700' : 'text-gray-600';
	const iconColorClass = selectedOptions.length > 0 ? 'text-purple-600' : 'text-gray-500';
	const searchInput = React.Children.toArray(children).filter(child => !child.props?.data);

	return (
		<components.ValueContainer {...props} selectProps={selectProps}>
			<span className={`mr-1 shrink-0 text-xs ${colorClass}`}>{selectProps.prefixLabel}</span>
			{selectedOptions.length === 1 && (
				<span
					className={`flex min-w-0 items-center gap-0.5 truncate text-xs ${colorClass}`}
					title={selectedOptions[0].label}
				>
					<Icon icon={selectedOptions[0].type_icon} size="text-xs" className={iconColorClass} />
					<span className="max-w-[20ch] truncate">{selectedOptions[0].label}</span>
				</span>
			)}
			{selectedOptions.length > 1 && (
				<span className={`mr-1 shrink-0 text-xs ${colorClass}`}>{selectedOptions.length} selected</span>
			)}
			{searchInput}
		</components.ValueContainer>
	);
};

const ToolbarMenuList = ({ children, selectProps, ...props }) => {
	const selectedOptions = Array.isArray(selectProps.value) ? selectProps.value : [];
	const selectedValues = new Set(selectedOptions.map(option => String(option.value)));
	const unselectedChildren = React.Children.toArray(children).filter(
		child => !selectedValues.has(String(child.props?.data?.value))
	);

	return (
		<components.MenuList {...props} selectProps={selectProps}>
			{selectedOptions.length > 0 && (
				<div role="group" aria-label="Selected mailing lists" className="mb-1 border-b border-gray-200 pb-1">
					{selectedOptions.map(option => (
						<div
							key={option.value}
							className="flex items-center justify-between gap-2 rounded bg-purple-50 px-3 py-2 text-xs text-gray-700"
						>
							<span className="flex min-w-0 items-center truncate">
								<Icon icon={option.type_icon} size="text-xs" className="text-purple-600" mr />
								<span className="truncate">{option.label}</span>
							</span>
							<button
								type="button"
								aria-label={`Remove ${option.label}`}
								className="flex h-6 w-6 shrink-0 items-center justify-center rounded text-gray-500 hover:bg-purple-100 hover:text-gray-700"
								onMouseDown={event => event.preventDefault()}
								onClick={() =>
									selectProps.onChange(
										selectedOptions.filter(selected => selected.value !== option.value)
									)
								}
							>
								<Icon icon="times" type="regular" size="text-xs" className="text-purple-600" />
							</button>
						</div>
					))}
				</div>
			)}
			{unselectedChildren}
		</components.MenuList>
	);
};

const ToolbarClearIndicator = props => {
	const hasSelection = Array.isArray(props.selectProps.value) && props.selectProps.value.length > 0;

	return (
		<components.ClearIndicator {...props}>
			<Icon
				icon="times"
				type="regular"
				size="text-xs"
				className={hasSelection ? 'text-purple-600' : 'text-gray-500'}
			/>
		</components.ClearIndicator>
	);
};

const ToolbarDropdownIndicator = props => {
	const hasSelection = Array.isArray(props.selectProps.value) && props.selectProps.value.length > 0;

	return (
		<components.DropdownIndicator {...props}>
			<Icon
				icon="chevron-down"
				type="regular"
				size="text-xs"
				className={hasSelection ? 'text-purple-600' : 'text-gray-500'}
			/>
		</components.DropdownIndicator>
	);
};

const SingleValue = ({ data: { label, type_icon }, children, ...props }) => (
	<components.SingleValue {...props}>
		<span className="text-gray-700 text-sm">
			<Icon icon={type_icon} mr />
			{label}
		</span>
	</components.SingleValue>
);

const MultiValueLabel = ({ data: { label, type_icon }, selectProps, children, ...props }) => (
	<components.MultiValueLabel {...props}>
		{selectProps.truncateSelectedLabels ? (
			<span className="flex w-full min-w-0 items-center text-sm text-gray-700">
				<Icon icon={type_icon} mr className="shrink-0" />
				<span className="min-w-0 max-w-full flex-1 truncate" title={label}>
					{label}
				</span>
			</span>
		) : (
			<span className="text-gray-700 text-sm">
				<Icon icon={type_icon} mr />
				{label}
			</span>
		)}
	</components.MultiValueLabel>
);

const Option = ({ data: { label, email, type_icon }, children, isSelected, ...props }) => (
	<components.Option {...props} isSelected={isSelected}>
		<div className={isSelected ? 'text-white text-sm' : 'text-gray-700 text-xs'}>
			<Icon icon={type_icon} size={isSelected ? undefined : 'text-xs'} mr />
			{label}
		</div>
		<div className={`mt-1 ${isSelected ? 'text-purple-100' : 'text-gray-500'} text-xs`}>{email}</div>
	</components.Option>
);

const toolbarPillStyles = {
	...mailingListSelectStyles,
	control: (base, state) => ({
		...base,
		'minHeight': '26px',
		'height': '26px',
		'backgroundColor': state.hasValue ? '#faf5ff' : '#ffffff',
		'borderColor': state.isFocused ? '#a855f7' : state.hasValue ? '#e9d5ff' : '#e5e7eb',
		'borderRadius': '0.25rem',
		'boxShadow': 'none',
		'&:hover': {
			borderColor: state.isFocused ? '#a855f7' : state.hasValue ? '#e9d5ff' : '#e5e7eb',
		},
	}),
	valueContainer: base => ({
		...base,
		minWidth: 0,
		flexWrap: 'nowrap',
		padding: '0 6px',
	}),
	input: base => ({
		...base,
		margin: 0,
		padding: 0,
		fontSize: '0.75rem',
		lineHeight: '1rem',
	}),
	clearIndicator: base => ({
		...base,
		padding: '0 4px',
	}),
	dropdownIndicator: base => ({
		...base,
		padding: '0 4px',
	}),
	indicatorsContainer: base => ({
		...base,
		height: '24px',
	}),
	indicatorSeparator: base => ({
		...base,
		display: 'none',
	}),
	menuPortal: base => ({
		...base,
		zIndex: 50,
	}),
};

const constrainedMailingListSelectStyles = {
	...mailingListSelectStyles,
	container: base => ({
		...base,
		width: '100%',
		minWidth: 0,
		maxWidth: '100%',
	}),
	control: base => ({
		...base,
		width: '100%',
		minWidth: 0,
		maxWidth: '100%',
		boxSizing: 'border-box',
	}),
	valueContainer: base => ({
		...base,
		flex: '1 1 0%',
		flexWrap: 'wrap',
		minWidth: 0,
		maxWidth: '100%',
		boxSizing: 'border-box',
	}),
	multiValue: base => ({
		...base,
		flex: '0 1 auto',
		minWidth: 0,
		maxWidth: 'calc(100% - 4px)',
	}),
	multiValueLabel: base => ({
		...base,
		flex: '1 1 auto',
		minWidth: 0,
		maxWidth: '100%',
		overflow: 'hidden',
	}),
};
