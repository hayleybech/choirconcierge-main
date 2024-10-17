import React from 'react';
import Label from "../../../components/inputs/Label";
import TextInput from "../../../components/inputs/TextInput";
import Filters from "../../../components/Filters";

const TenantFilters = ({ form }) => (
    <Filters
        routeName="central.tenants.index"
        form={form}
        render={(data, setData) => (<>
            <div>
                <Label label="Tenant ID" forInput="id" />
                <TextInput name="id" value={data.id} updateFn={value => setData('id', value)} />
            </div>
        </>)}
    />
);

export default TenantFilters;