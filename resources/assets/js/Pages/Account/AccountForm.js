import React from 'react';
import {useForm, usePage} from "@inertiajs/inertia-react";
import FormSection from "../../components/FormSection";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import Error from "../../components/inputs/Error";
import ButtonLink from "../../components/inputs/ButtonLink";
import Button from "../../components/inputs/Button";
import AvatarUpload from "../../components/AvatarUpload";
import DateInput from "../../components/inputs/Date";
import Help from "../../components/inputs/Help";
import Select from "../../components/inputs/Select";
import Form from "../../components/Form";
import FormFooter from "../../components/FormFooter";

const AccountForm = ({ }) => {
    const { user } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        first_name: user.first_name,
        last_name: user.last_name,
        avatar: null,
        email: user.email,
        phone: user.phone ?? '',
        password: '',

        password_confirmation: '',
        dob: user.dob,
        height: user.height ?? '',
        profession: user.profession ?? '',
        skills: user.skills ?? '',
	    
        bha_id: user.bha_id ?? '',
	    ice_name: user.ice_name ?? '',
	    ice_phone: user.ice_phone ?? '',
	    
	    address_street_1: user.address_street_1 ?? '',
	    address_street_2: user.address_street_2 ?? '',
	    address_suburb: user.address_suburb ?? '',
	    address_state: user.address_state ?? '',
	    address_postcode: user.address_postcode ?? '',
    });

    function submit(e) {
        e.preventDefault();
        post(route('accounts.update'));
    }

    return (
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <Form onSubmit={submit}>

                <FormSection title="User Details">

                    <div className="sm:col-span-2">
                        <Label label="Profile Picture" forInput="avatar" />
                        <AvatarUpload currentImage={data.avatar ? URL.createObjectURL(data.avatar) : user.avatar_url} updateFn={value => setData('avatar', value)} />
                    </div>
                    <div className="sm:col-span-2">
                        <Label label="First Name" forInput="first_name" />
                        <TextInput name="first_name" value={data.first_name} updateFn={value => setData('first_name', value)} hasErrors={ !! errors['first_name'] } />
                        {errors.first_name && <Error>{errors.first_name}</Error>}
                    </div>
                    <div className="sm:col-span-2">
                        <Label label="Last Name" forInput="last_name" />
                        <TextInput name="last_name" value={data.last_name} updateFn={value => setData('last_name', value)} hasErrors={ !! errors['last_name'] } />
                        {errors.last_name && <Error>{errors.last_name}</Error>}
                    </div>

                    <div className="sm:col-span-3">
                        <Label label="Email Address" forInput="email" />
                        <TextInput type="email" name="email" value={data.email} updateFn={value => setData('email', value)} hasErrors={ !! errors['email'] } />
                        {errors.email && <Error>{errors.email}</Error>}
                    </div>
                    <div className="sm:col-span-3">
                        <Label label="Phone" forInput="phone" />
                        <TextInput type="tel" name="phone" value={data.phone} updateFn={value => setData('phone', value)} hasErrors={ !! errors['phone'] } />
                        {errors.phone && <Error>{errors.phone}</Error>}
                    </div>

                    <div className="sm:col-span-3">
                        <Label label="Change Password" forInput="password" />
                        <TextInput type="password" name="password" value={data.password} updateFn={value => setData('password', value)} hasErrors={ !! errors['password'] } />
                        {errors.password && <Error>{errors.password}</Error>}
                    </div>
                    <div className="sm:col-span-3">
                        <Label label="Confirm New Password" forInput="password_confirmation" />
                        <TextInput type="password" name="password_confirmation" value={data.password_confirmation} updateFn={value => setData('password_confirmation', value)} hasErrors={ !! errors['password_confirmation'] } />
                        {errors.password_confirmation && <Error>{errors.password_confirmation}</Error>}
                    </div>

                </FormSection>

                <FormSection title="Profile Details">

                    <div className="sm:col-span-2">
                        <Label label="Date of Birth" forInput="dob" />
                        <DateInput name="dob" hasErrors={ !! errors.dob } value={data.dob} updateFn={value => setData('dob', value)} />
                        {errors.dob && <Error>{errors.dob}</Error>}
                    </div>

                    <div className="sm:col-span-2">
                        <Label label="Height (cm)" forInput="height" />
                        <TextInput type="number" name="height" value={data.height} updateFn={value => setData('height', value)} hasErrors={ !! errors['height'] } />
                        <Help>Knowing the singer's height is useful for riser stacks.</Help>
                        {errors.height && <Error>{errors.height}</Error>}
                    </div>
                    <div className="sm:col-span-2">
                        <Label label="BHA Member ID (e.g. 1234)" forInput="bha_id" />
                        <TextInput name="bha_id" value={data.bha_id} updateFn={value => setData('bha_id', value)} hasErrors={ !! errors['bha_id'] } />
                        {errors.bha_id && <Error>{errors.bha_id}</Error>}
                    </div>

                    <div className="sm:col-span-3">
                        <Label label="What is your profession?" forInput="profession" />
                        <TextInput name="profession" value={data.profession} updateFn={value => setData('profession', value)} hasErrors={ !! errors['profession'] } />
                        {errors.profession && <Error>{errors.profession}</Error>}
                    </div>
                    <div className="sm:col-span-3">
                        <Label label="What non-musical skills do you have?" forInput="skills" />
                        <TextInput name="skills" value={data.skills} updateFn={value => setData('skills', value)} hasErrors={ !! errors['skills'] } />
                        {errors.skills && <Error>{errors.skills}</Error>}
                    </div>
                </FormSection>

                <FormSection title="Emergency Contact">

                    <div className="sm:col-span-3">
                        <Label label="Emergency Contact Name" forInput="ice_name" />
                        <TextInput name="ice_name" value={data.ice_name} updateFn={value => setData('ice_name', value)} hasErrors={ !! errors['ice_name'] } />
                        {errors.ice_name && <Error>{errors.ice_name}</Error>}
                    </div>
                    <div className="sm:col-span-3">
                        <Label label="Emergency Contact Phone" forInput="ice_phone" />
                        <TextInput type="tel" name="ice_phone" value={data.ice_phone} updateFn={value => setData('ice_phone', value)} hasErrors={ !! errors['ice_phone'] } />
                        {errors.ice_phone && <Error>{errors.ice_phone}</Error>}
                    </div>

                </FormSection>

                <FormSection title="Address">

                    <div className="sm:col-span-6">
                        <Label label="Street Address 1" forInput="address_street_1" />
                        <TextInput name="address_street_1" value={data.address_street_1} updateFn={value => setData('address_street_1', value)} hasErrors={ !! errors['address_street_1'] } />
                        {errors.address_street_1 && <Error>{errors.address_street_1}</Error>}
                    </div>
                    <div className="sm:col-span-6">
                        <Label label="Street Address 2" forInput="address_street_2" />
                        <TextInput type="tel" name="address_street_2" value={data.address_street_2} updateFn={value => setData('address_street_2', value)} hasErrors={ !! errors['address_street_2'] } />
                        {errors.address_street_2 && <Error>{errors.address_street_2}</Error>}
                    </div>

                    <div className="sm:col-span-3">
                        <Label label="Suburb" forInput="address_suburb" />
                        <TextInput name="address_suburb" value={data.address_suburb} updateFn={value => setData('address_suburb', value)} hasErrors={ !! errors['address_suburb'] } />
                        {errors.address_suburb && <Error>{errors.address_suburb}</Error>}
                    </div>
                    <div className="sm:col-span-1">
                        <Label label="State" forInput="address_state" />
                        <Select
                            name="address_state"
                            value={data.address_state}
                            updateFn={value => setData('address_state', value)}
                            hasErrors={ !! errors['address_state'] }
                            options={[
                                { key: 'ACT', label: 'ACT' },
                                { key: 'NSW', label: 'NSW' },
                                { key: 'NT',  label: 'NT'  },
                                { key: 'QLD', label: 'QLD' },
                                { key: 'SA',  label: 'SA'  },
                                { key: 'TAS', label: 'TAS' },
                                { key: 'VIC', label: 'VIC' },
                                { key: 'WA',  label: 'WA'  },
                            ]}
                        />
                        {errors.address_state && <Error>{errors.address_state}</Error>}
                    </div>
                    <div className="sm:col-span-2">
                        <Label label="Postcode" forInput="address_postcode" />
                        <TextInput name="address_postcode" value={data.address_postcode} updateFn={value => setData('address_postcode', value)} hasErrors={ !! errors['address_postcode'] } />
                        {errors.address_postcode && <Error>{errors.address_postcode}</Error>}
                    </div>

                </FormSection>

                <FormFooter>
                    <ButtonLink href={route('singers.show', user.singer)}>Cancel</ButtonLink>
                    <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                </FormFooter>

            </Form>
        </div>
    );
}

export default AccountForm;