import React from 'react';
import CheckboxGroup from '../../components/inputs/CheckboxGroup';
import Filters from '../../components/Filters';
import Label from '../../components/inputs/Label';
import TextInput from '../../components/inputs/TextInput';
 
const MailingListFilters = ({ form }) => (
	<Filters
		routeName="groups.index"
		form={form}
		render={(data, setData) => (
			<>
				<div>
					<Label label="Search" forInput="search" />
					<TextInput
						name="search"
						value={data.search || ''}
						updateFn={value => setData('search', value)}
						placeholder="Search title or address..."
					/>
				</div>
				<fieldset>
					<legend className="text-sm font-medium text-gray-700">Type</legend>
					<CheckboxGroup
						name="list_type"
						options={[
							{ id: 'chat', name: 'Chat' },
							{ id: 'public', name: 'Public' },
							{ id: 'distribution', name: 'Mailout' },
						]}
						value={data.list_type}
						updateFn={value => setData('list_type', value)}
					/>
				</fieldset>
			</>
		)}
	/>
);

export default MailingListFilters;