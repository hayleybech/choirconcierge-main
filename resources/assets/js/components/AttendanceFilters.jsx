import React from 'react';
import Filters from './Filters';
import Label from './inputs/Label';
import TextInput from './inputs/TextInput';
import CheckboxGroup from './inputs/CheckboxGroup';
import FilterActions from './inputs/FilterActions';
import Badge from './Badge';
import Icon from './Icon';
import SingerStatus from '../SingerStatus';
import SingerStatusTag from './SingerStatusTag';
import VoicePartTag from './VoicePartTag';
 
const AttendanceFilters = ({ event, voiceParts, form, ensembles, singerStatuses }) => (
	<Filters
		routeName="events.attendances.index"
		routeParams={{ event: event.id }}
		form={form}
		render={(data, setData) => (
			<>
				<div>
					<Label label="Name" forInput="user.name" />
					<TextInput
						name="user.name"
						value={data['user.name']}
						updateFn={value => setData('user.name', value)}
					/>
				</div>

				<fieldset>
					<div className="flex items-center justify-between">
						<legend className="text-sm font-medium text-gray-700">Singer Status</legend>
						<FilterActions
							onSelectAll={() =>
								setData(
									'status.id',
									singerStatuses.map(status => status.id)
								)
							}
							onClear={() => setData('status.id', [])}
						/>
					</div>
					<CheckboxGroup
						name="status.id"
						options={singerStatuses.map(status => ({ id: status.id, name: status.name }))}
						value={data['status.id']}
						updateFn={value => setData('status.id', value)}
						renderOption={option => <SingerStatusTag status={new SingerStatus(option.id)} withLabel />}
					/>
				</fieldset>

				<fieldset>
					<div className="flex items-center justify-between">
						<legend className="text-sm font-medium text-gray-700">Voice Part</legend>
						<FilterActions
							onSelectAll={() =>
								setData(
									'enrolments.voice_part_id',
									voiceParts.map(part => part.id)
								)
							}
							onClear={() => setData('enrolments.voice_part_id', [])}
						/>
					</div>
					<CheckboxGroup
						name="enrolments.voice_part_id"
						options={voiceParts.map(part => ({ id: part.id, name: part.title, colour: part.colour }))}
						value={data['enrolments.voice_part_id']}
						updateFn={value => setData('enrolments.voice_part_id', value)}
						renderOption={option => <VoicePartTag title={option.name} colour={option.colour} />}
					/>
				</fieldset>

				<fieldset>
					<div className="flex items-center justify-between">
						<legend className="text-sm font-medium text-gray-700">Attendance Response</legend>
						<FilterActions
							onSelectAll={() =>
								setData('attendance.response', [
									'present',
									'late',
									'unknown',
									'late_deemed_absent',
									'absent',
								])
							}
							onClear={() => setData('attendance.response', [])}
						/>
					</div>
					<CheckboxGroup
						name="attendance.response"
						options={[
							{
								id: 'present',
								name: 'On Time',
								icon: 'check',
								colour: 'text-emerald-500',
							},
							{ id: 'late', name: 'Late', icon: 'alarm-snooze', colour: 'text-yellow-500' },
							{ id: 'unknown', name: 'Not recorded', icon: 'circle', colour: 'text-gray-500' },
							{
								id: 'late_deemed_absent',
								name: 'Late (Deemed Absent)',
								icon: 'alarm-exclamation',
								colour: 'text-orange-500',
							},
							{ id: 'absent', name: 'Absent', icon: 'times', colour: 'text-red-500' },
						]}
						value={data['attendance.response']}
						updateFn={value => setData('attendance.response', value)}
						renderOption={option => (
							<span className="inline-flex items-center gap-1.5">
								<Icon icon={option.icon} className={option.colour} />
								{option.name}
							</span>
						)}
					/>
				</fieldset>

				{ensembles.length > 1 && (
					<fieldset>
						<div className="flex items-center justify-between">
							<legend className="text-sm font-medium text-gray-700">Ensemble</legend>
							<FilterActions
								onSelectAll={() =>
									setData(
										'enrolments.ensemble_id',
										ensembles.map(ensemble => ensemble.id)
									)
								}
								onClear={() => setData('enrolments.ensemble_id', [])}
							/>
						</div>
						<CheckboxGroup
							name="enrolments.ensemble_id"
							options={ensembles.map(ensemble => ({ id: ensemble.id, name: ensemble.name }))}
							value={data['enrolments.ensemble_id']}
							updateFn={value => setData('enrolments.ensemble_id', value)}
							renderOption={option => <Badge colour="bg-purple-100 text-purple-800">{option.name}</Badge>}
						/>
					</fieldset>
				)}
			</>
		)}
	/>
);

export default AttendanceFilters;
