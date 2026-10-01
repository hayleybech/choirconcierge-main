import React from 'react';
import Label from '../../../components/inputs/Label';
import TextInput from '../../../components/inputs/TextInput';
import Filters from '../../../components/Filters';
import MailingListSelect from '../../../components/inputs/MailingListSelect';
import FilterActions from '../../../components/inputs/FilterActions';

const MailLogFilters = ({ form, mailingLists }) => (
	<Filters
		routeName="communications.index"
		form={form}
		render={(data, setData) => (
			<>
				{[
					['subject', 'Subject'],
					['from', 'From'],
				].map(([name, label]) => (
					<div key={name}>
						<Label label={label} forInput={name} />
						<TextInput name={name} value={data[name]} updateFn={value => setData(name, value)} />
					</div>
				))}
				<div>
					<div className="flex items-center justify-between">
						<Label label="To" forInput="to" />
						<FilterActions onClear={() => setData('to', [])} />
					</div>
					<MailingListSelect
						options={mailingLists.map(({ title, email, type_icon }) => ({ label: title, value: email, email, type_icon }))}
						defaultValue={mailingLists
							.map(({ title, email, type_icon }) => ({ label: title, value: email, email, type_icon }))
							.filter(option => (Array.isArray(data.to) ? data.to : [data.to]).includes(option.value))}
						multiple
						isClearable={false}
						updateFn={value => setData('to', value)}
					/>
				</div>
			</>
		)}
	/>
);

export default MailLogFilters;