import React from 'react';
import Filters from './Filters';
import Label from './inputs/Label';
import TextInput from './inputs/TextInput';
import CheckboxGroup from './inputs/CheckboxGroup';
import RadioGroup from './inputs/RadioGroup';
import FilterActions from './inputs/FilterActions';

const PollFilters = ({ form, ensembles }) => (
	<Filters
		routeName="polls.index"
		form={form}
		render={(data, setData) => (
			<>
				<div>
					<Label label="Title" forInput="title" />
					<TextInput
						name="title"
						value={data['title']}
						updateFn={value => setData('title', value)}
					/>
				</div>

				<fieldset>
					<RadioGroup
						name="status"
						label={
							<div className="flex items-center justify-between mb-2">
								<Label label="Status" />
								<FilterActions
									onClear={() => setData('status', '')}
								/>
							</div>
						}
						options={[
							{ id: '', name: 'All', icon: 'poll' },
							{ id: 'open', name: 'Open', icon: 'check', colour: 'text-emerald-500' },
							{ id: 'closed', name: 'Closed', icon: 'lock', colour: 'text-gray-500' },
						]}
						selected={data.status}
						setSelected={value => setData('status', value)}
						vertical
						size="xs"
					/>
				</fieldset>

				{ensembles.length > 1 && (
					<fieldset>
						<div className="flex items-center justify-between">
							<legend className="text-sm font-medium text-gray-700">Ensemble</legend>
							<FilterActions
								onSelectAll={() => setData('ensembles.id', ensembles.map(ensemble => ensemble.id))}
								onClear={() => setData('ensembles.id', [])}
							/>
						</div>
						<CheckboxGroup
							name="ensembles.id"
							options={ensembles.map(ensemble => ({ id: ensemble.id, name: ensemble.name }))}
							value={data['ensembles.id']}
							updateFn={value => setData('ensembles.id', value)}
						/>
					</fieldset>
				)}
			</>
		)}
	/>
);

export default PollFilters;
