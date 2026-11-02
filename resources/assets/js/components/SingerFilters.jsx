import React from 'react';
import Filters from "./Filters";
import Label from "./inputs/Label";
import TextInput from "./inputs/TextInput";
import CheckboxGroup from "./inputs/CheckboxGroup";
import FilterActions from "./inputs/FilterActions";
import { areFilterValuesEqual } from '../lib/filterValues';
import SingerStatus from '../SingerStatus';
import Badge from './Badge';
import FeeStatus from './FeeStatus';
import SingerStatusTag from './SingerStatusTag';
import VoicePartTag from './VoicePartTag';

const feeStatusOptions = [
	{ id: 'paid', name: 'Paid' },
	{ id: 'expires-soon', name: 'Expires Soon' },
	{ id: 'expired', name: 'Expired' },
	{ id: 'unknown', name: 'Unknown' },
];
 
const SingerFilters = ({ statuses, voiceParts, roles, form, ensembles, defaultStatus }) => {
	const defaultValues = {
		'user.name': '',
		status: [defaultStatus],
		'enrolments.voice_part_id': [],
		'roles.id': [],
		fee_status: [],
		'enrolments.ensemble_id': [],
	};

	const isDefault = (data, name) => areFilterValuesEqual(data[name], defaultValues[name]);
	const fieldLabelClass = (data, name) =>
		`text-sm font-medium ${isDefault(data, name) ? 'text-gray-700' : 'text-purple-700'}`;
	const filterName = (data, name, label, showSelectedCount = false) => (
		<>
			<span>{label}</span>
			{showSelectedCount && (
				<span className="ml-1 text-xs font-normal text-gray-500">{getSelectedFilterCount(data[name])} selected</span>
			)}
			{!isDefault(data, name) && (
				<span aria-hidden="true" title="Filter differs from default">
					*
				</span>
			)}
		</>
	);
	const filterActions = (data, setData, name, label, onClear, onSelectAll) => (
		<FilterActions
			onSelectAll={onSelectAll}
			onClear={onClear}
			onReset={() => setData(name, defaultValues[name])}
			clearLabel="None"
			resetDisabled={isDefault(data, name)}
			resetAriaLabel={`Reset ${label} filter`}
		/>
	);

	return (
		<Filters
			routeName="singers.index"
			form={form}
			autoSubmit={false}
			showClearAll={false}
			render={(data, setData) => (
				<>
					<div>
						<div className="flex items-center justify-between">
							<Label
								label={filterName(data, 'user.name', 'Name')}
								forInput="user.name"
								className={fieldLabelClass(data, 'user.name')}
							/>
							{filterActions(data, setData, 'user.name', 'Name', () => setData('user.name', ''))}
						</div>
						<TextInput
							name="user.name"
							value={data['user.name']}
							updateFn={value => setData('user.name', value)}
							className={isDefault(data, 'user.name') ? '' : 'border-purple-300 bg-purple-50'}
						/>
					</div>

					<fieldset>
						<div className="flex items-center justify-between">
							<legend className={fieldLabelClass(data, 'status')}>
								{filterName(data, 'status', 'Status', true)}
							</legend>
							{filterActions(
								data,
								setData,
								'status',
								'Status',
								() => setData('status', ['']),
								() => setData('status', statuses.map(status => status.id))
							)}
						</div>
						<CheckboxGroup
							name="status"
							options={statuses.map(status => ({ id: status.id, name: status.name }))}
							value={data.status}
							defaultValue={defaultValues.status}
							renderOption={(option, isChanged) => renderSingerFilterOption('status', option, isChanged)}
							updateFn={value => setData('status', value)}
						/>
					</fieldset>

					<fieldset>
						<div className="flex items-center justify-between">
							<legend className={fieldLabelClass(data, 'enrolments.voice_part_id')}>
								{filterName(data, 'enrolments.voice_part_id', 'Voice Part', true)}
							</legend>
							{filterActions(
								data,
								setData,
								'enrolments.voice_part_id',
								'Voice Part',
								() => setData('enrolments.voice_part_id', []),
								() => setData('enrolments.voice_part_id', voiceParts.map(part => part.id))
							)}
						</div>
						<CheckboxGroup
							name="enrolments.voice_part_id"
							options={voiceParts.map(part => ({ id: part.id, name: part.title, colour: part.colour }))}
							value={data['enrolments.voice_part_id']}
							defaultValue={defaultValues['enrolments.voice_part_id']}
							renderOption={(option, isChanged) =>
								renderSingerFilterOption('enrolments.voice_part_id', option, isChanged)
							}
							updateFn={value => setData('enrolments.voice_part_id', value)}
						/>
					</fieldset>

					{ensembles.length > 1 && (
						<fieldset>
							<div className="flex items-center justify-between">
								<legend className={fieldLabelClass(data, 'enrolments.ensemble_id')}>
									{filterName(data, 'enrolments.ensemble_id', 'Ensemble', true)}
								</legend>
								{filterActions(
									data,
									setData,
									'enrolments.ensemble_id',
									'Ensemble',
									() => setData('enrolments.ensemble_id', []),
									() => setData('enrolments.ensemble_id', ensembles.map(ensemble => ensemble.id))
								)}
							</div>
							<CheckboxGroup
								name="enrolments.ensemble_id"
								options={ensembles.map(ensemble => ({ id: ensemble.id, name: ensemble.name }))}
								value={data['enrolments.ensemble_id']}
								defaultValue={defaultValues['enrolments.ensemble_id']}
								renderOption={(option, isChanged) =>
									renderSingerFilterOption('enrolments.ensemble_id', option, isChanged)
								}
								updateFn={value => setData('enrolments.ensemble_id', value)}
							/>
						</fieldset>
					)}

					<fieldset>
						<div className="flex items-center justify-between">
							<legend className={fieldLabelClass(data, 'roles.id')}>
								{filterName(data, 'roles.id', 'Role', true)}
							</legend>
							{filterActions(
								data,
								setData,
								'roles.id',
								'Role',
								() => setData('roles.id', []),
								() => setData('roles.id', roles.map(role => role.id))
							)}
						</div>
						<CheckboxGroup
							name="roles.id"
							options={roles.map(role => ({ id: role.id, name: role.name }))}
							value={data['roles.id']}
							defaultValue={defaultValues['roles.id']}
							renderOption={(option, isChanged) => renderSingerFilterOption('roles.id', option, isChanged)}
							updateFn={value => setData('roles.id', value)}
						/>
					</fieldset>

					<fieldset>
						<div className="flex items-center justify-between">
							<legend className={fieldLabelClass(data, 'fee_status')}>
								{filterName(data, 'fee_status', 'Fee Status', true)}
							</legend>
							{filterActions(
								data,
								setData,
								'fee_status',
								'Fee Status',
								() => setData('fee_status', []),
								() => setData('fee_status', feeStatusOptions.map(option => option.id))
							)}
						</div>
						<CheckboxGroup
							name="fee_status"
							options={feeStatusOptions}
							value={data.fee_status}
							defaultValue={defaultValues.fee_status}
							renderOption={(option, isChanged) => renderSingerFilterOption('fee_status', option, isChanged)}
							updateFn={value => setData('fee_status', value)}
						/>
					</fieldset>
				</>
			)}
		/>
	);
};

function getSelectedFilterCount(value) {
	return (Array.isArray(value) ? value : [value]).filter(
		selectedValue => selectedValue !== null && selectedValue !== undefined && String(selectedValue).trim() !== ''
	).length;
}

const renderSingerFilterOption = (filterName, option, isChanged) => {
	let value = <Badge colour="bg-gray-100 text-gray-800">{option.name}</Badge>;

	if (filterName === 'status' && SingerStatus.statuses[option.id]) {
		value = <SingerStatusTag status={new SingerStatus(option.id)} withLabel compact />;
	} else if (filterName === 'enrolments.voice_part_id') {
		value = <VoicePartTag title={option.name} colour={option.colour} />;
	} else if (filterName === 'enrolments.ensemble_id') {
		value = <Badge colour="bg-purple-100 text-purple-800">{option.name}</Badge>;
	} else if (filterName === 'fee_status') {
		value = <FeeStatus status={option.id} />;
	}

	return (
		<span className="inline-flex items-center gap-1.5">
			{value}
			{isChanged && (
				<span aria-hidden="true" title="Filter value differs from default" className="text-gray-500">
					*
				</span>
			)}
		</span>
	);
};

export default SingerFilters;