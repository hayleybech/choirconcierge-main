import React from 'react';
import Filters from '../../components/Filters';
import Label from '../../components/inputs/Label';
import TextInput from '../../components/inputs/TextInput';

const DocumentFilters = ({ form }) => (
	<Filters
		routeName="folders.index"
		form={form}
		render={(data, setData) => (
			<>
				<div>
					<Label label="Title" forInput="title" />
					<TextInput 
                        name="title"
                        value={data.title || ''}
                        updateFn={value => setData('title', value)}
                        placeholder="Search folders or documents..."
                    />
				</div>
			</>
		)}
	/>
);

export default DocumentFilters;
