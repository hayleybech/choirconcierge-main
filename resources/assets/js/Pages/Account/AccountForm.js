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

const AccountForm = ({ }) => {
    const { user } = usePage().props;
    const { data, setData, put, processing, errors } = useForm({
        first_name: user.first_name,
        last_name: user.last_name,
        avatar: user.avatar_url,
        email: user.email,
        password: '',
        password_confirmation: '',

        dob: user.dob,
        height: user.height,
        profession: user.profession,
        skills: user.skills,
        bha_id: user.bha_id,
    });

    function submit(e) {
        e.preventDefault();
        put(route('accounts.update'));
    }

    return (
        <div className="bg-gray-50">

            <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <form className="space-y-8 divide-y divide-gray-200" onSubmit={submit}>

                    <div className="space-y-8 divide-y divide-gray-200">

                        <FormSection title="User Details">
                            
                            <div className="sm:col-span-3">
                                <Label label="First Name" forInput="first_name" />
                                <TextInput name="first_name" value={data.first_name} updateFn={value => setData('first_name', value)} hasErrors={ !! errors['first_name'] } />
                                {errors.first_name && <Error>{errors.first_name}</Error>}
                            </div>
                            <div className="sm:col-span-3">
                                <Label label="Last Name" forInput="last_name" />
                                <TextInput name="last_name" value={data.last_name} updateFn={value => setData('last_name', value)} hasErrors={ !! errors['last_name'] } />
                                {errors.last_name && <Error>{errors.last_name}</Error>}
                            </div>

                            <div className="sm:col-span-6">
                                <Label label="Profile Picture" forInput="avatar" />
                                <AvatarUpload currentImage={user.avatar_url} />
                            </div>

                            <div className="sm:col-span-6">
                                <Label label="Email Address" forInput="email" />
                                <TextInput type="email" name="email" value={data.email} updateFn={value => setData('email', value)} hasErrors={ !! errors['email'] } />
                                {errors.email && <Error>{errors.email}</Error>}
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

                    </div>

                    <div className="pt-5">
                        <div className="flex justify-end">
                            <ButtonLink href={route('singers.show', user.singer)}>Cancel</ButtonLink>
                            <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default AccountForm;