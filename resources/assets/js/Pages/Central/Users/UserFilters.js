import React from 'react';
import Label from '../../../components/inputs/Label';
import TextInput from '../../../components/inputs/TextInput';
import Filters from '../../../components/Filters';

const UserFilters = ({ form }) => (
	<Filters
		routeName="central.users.index"
		form={form}
		render={(data, setData) => (
			<>
				<div>
					<Label label="Search" forInput="search" />
					<TextInput name="search" value={data.search} updateFn={value => setData('search', value)} />
				</div>
			</>
		)}
	/>
);

export default UserFilters;
