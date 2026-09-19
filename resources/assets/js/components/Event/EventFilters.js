import React from 'react';
import Label from "../inputs/Label";
import TextInput from "../inputs/TextInput";
import CheckboxGroup from "../inputs/CheckboxGroup";
import RadioGroup from "../inputs/RadioGroup";
import Filters from "../Filters";
import FilterActions from "../inputs/FilterActions";

const EventFilters = ({ eventTypes, ensembles, userEnsemblesCount, form }) => (
	<Filters
		routeName="events.index"
		form={form}
		render={(data, setData) => (
			<>
				<div>
					<Label label="Title" forInput="title" />
					<TextInput name="title" value={data.title} updateFn={value => setData('title', value)} />
				</div>

				{userEnsemblesCount > 1 && (
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
							options={ensembles.map((ensemble) => ({ id: ensemble.id, name: ensemble.name }))}
							value={data['ensembles.id']}
							updateFn={value => setData('ensembles.id', value)}
							size="xs"
						/>
					</fieldset>
				)}

				<fieldset className="">
					<div className="flex items-center justify-between">
						<legend className="text-sm font-medium text-gray-700">Type</legend>
						<FilterActions
							onSelectAll={() => setData('type.id', eventTypes.map(type => type.id))}
							onClear={() => setData('type.id', [])}
						/>
					</div>
					<CheckboxGroup
						name="type.id"
						options={eventTypes.map(type => ({ id: type.id, name: type.title }))}
						value={data['type.id']}
						updateFn={value => setData('type.id', value)}
					/>
				</fieldset>

				<div>
					<RadioGroup
						label={
							<div className="flex items-center justify-between mb-2">
								<Label label="Date" />
								<FilterActions
									onClear={() => setData('date', 'all')}
								/>
							</div>
						}
						options={[
							{ id: 'all', name: 'All', icon: 'exchange' },
							{ id: 'upcoming', name: 'Upcoming', icon: 'arrow-from-left' },
							{ id: 'past', name: 'Past', icon: 'arrow-from-right' },
						]}
						selected={data.date}
						setSelected={value => {
							setData('date', value);

							if (value === 'upcoming') {
								setData('sort', 'start_date');
								setData('sortDir', 'asc')
							}
							if (value === 'past') {
								setData('sort', 'start_date');
								setData('sortDir', 'desc');
							}
						}}
						vertical
						size="xs"
					/>
				</div>
			</>
		)}
	/>
);

export default EventFilters;