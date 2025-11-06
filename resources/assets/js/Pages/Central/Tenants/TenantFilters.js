import React from 'react';
import Label from "../../../components/inputs/Label";
import TextInput from "../../../components/inputs/TextInput";
import Filters from "../../../components/Filters";
import RadioGroup from "../../../components/inputs/RadioGroup";

const TenantFilters = ({ form }) => (
    <Filters
        routeName="central.tenants.index"
        form={form}
        render={(data, setData) => (<>
            <div>
                <Label label="Tenant ID" forInput="id" />
                <TextInput name="id" value={data.id} updateFn={value => setData('id', value)} />
            </div>

            <fieldset>
                <RadioGroup
                    name="billing_status"
                    label={<Label label="Billing Status" />}
                    options={[
                        { id: 'active', name: 'Active', icon: 'check-circle', colour: 'green-500', textColour: 'text-green-500' },
                        { id: 'gratis', name: 'Gratis', icon: 'heart-circle', colour: 'green-500', textColour: 'text-green-500' },
                        { id: 'trial', name: 'Trial', icon: 'dot-circle', colour: 'blue-500', textColour: 'text-blue-500' },
                        { id: 'inactive', name: 'Inactive', icon: 'times-circle', colour: 'red-500', textColour: 'text-red-500' },
                    ]}
                    selected={data.billing_status}
                    setSelected={value => setData('billing_status', value)}
                    vertical
                />
            </fieldset>
        </>)}
    />
);

export default TenantFilters;