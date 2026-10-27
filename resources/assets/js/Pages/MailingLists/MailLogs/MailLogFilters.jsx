import React from 'react';
import Label from '../../../components/inputs/Label';
import TextInput from '../../../components/inputs/TextInput';
import Filters from '../../../components/Filters';
import MailingListSelect from '../../../components/inputs/MailingListSelect';
import FilterActions from '../../../components/inputs/FilterActions';
import CheckboxGroup from '../../../components/inputs/CheckboxGroup';
import RadioGroup from '../../../components/inputs/RadioGroup';
 
const MailLogFilters = ({ form, mailingLists, mailTypes, inboxEnabled }) => (
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
				<fieldset>
					<div className="flex items-center justify-between">
						<legend className="text-sm font-medium text-gray-700">Mail type</legend>
						<FilterActions
							onSelectAll={() => setData('type', mailTypes.map(type => type.id))}
							onClear={() => setData('type', [])}
						/>
					</div>
					<CheckboxGroup
						name="type"
						options={mailTypes}
						value={data.type}
						updateFn={value => setData('type', value)}
					/>
				</fieldset>
				{inboxEnabled && (
					<fieldset>
						<RadioGroup
							label={
								<div className="flex items-center justify-between mb-2">
									<Label label="Read status" />
									<FilterActions onClear={() => setData('read', '')} />
								</div>
							}
							options={[
								{ id: '', name: 'All' },
								{ id: 'read', name: 'Read', icon: 'envelope-open-text' },
								{ id: 'unread', name: 'Unread', icon: 'envelope' },
							]}
							selected={data.read}
							setSelected={value => setData('read', value)}
							vertical
							size="xs"
						/>
					</fieldset>
				)}
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
