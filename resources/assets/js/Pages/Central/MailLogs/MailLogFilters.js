import React from 'react';
import Label from '../../../components/inputs/Label';
import TextInput from '../../../components/inputs/TextInput';
import Filters from '../../../components/Filters';
import TenantSelect from '../../../components/inputs/TenantSelect';
import FilterActions from '../../../components/inputs/FilterActions';

const MailLogFilters = ({ form, tenants }) => (
	<Filters
		routeName="central.mail-logs.index"
		form={form}
		render={(data, setData) => (
			<>
				{[
					['subject', 'Subject'],
					['from', 'From'],
					['to', 'To'],
				].map(([name, label]) => (
					<div key={name}>
						<Label label={label} forInput={name} />
						<TextInput name={name} value={data[name]} updateFn={value => setData(name, value)} />
					</div>
				))}
				<div>
					<div className="flex items-center justify-between">
						<Label label="Tenant" forInput="tenant" />
						<FilterActions onClear={() => setData('tenant', '')} />
					</div>
					<TenantSelect
						options={tenants.map(({ id, name }) => ({ label: name, value: id }))}
						defaultValue={
							tenants
								.map(({ id, name }) => ({ label: name, value: id }))
								.find(option => option.value === data.tenant) ?? null
						}
						updateFn={value => setData('tenant', value)}
					/>
				</div>
			</>
		)}
	/>
);

export default MailLogFilters;
