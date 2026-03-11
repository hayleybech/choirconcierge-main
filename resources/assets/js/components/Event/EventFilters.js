import React from 'react';
import Label from "../inputs/Label";
import TextInput from "../inputs/TextInput";
import CheckboxGroup from "../inputs/CheckboxGroup";
import RadioGroup from "../inputs/RadioGroup";
import Filters from "../Filters";

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
						<legend className="text-sm font-medium text-gray-700">Ensemble</legend>
						<CheckboxGroup
							name="ensembles.id"
							options={ensembles.map((ensemble) => ({ id: ensemble.id, name: ensemble.name }))}
							value={data['ensembles.id']}
							updateFn={value => setData('ensembles.id', value)}
						/>
					</fieldset>
				)}

				<fieldset className="">
					<legend className="text-sm font-medium text-gray-700">Type</legend>
					<CheckboxGroup
						name="type.id"
						options={eventTypes.map(type => ({ id: type.id, name: type.title }))}
						value={data['type.id']}
						updateFn={value => setData('type.id', value)}
					/>
				</fieldset>

				<div>
					<RadioGroup
						label={<Label label="Date" />}
						options={[
							{ id: 'all', name: 'All' },
							{ id: 'upcoming', name: 'Upcoming' },
							{ id: 'past', name: 'Past' },
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
					/>
				</div>
			</>
		)}
	/>
);

export default EventFilters;