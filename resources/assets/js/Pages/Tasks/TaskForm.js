import React from 'react';
import {useForm} from "@inertiajs/react";
import Form from "../../components/Form";
import FormSection from "../../components/FormSection";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import Error from "../../components/inputs/Error";
import FormFooter from "../../components/FormFooter";
import ButtonLink from "../../components/inputs/ButtonLink";
import Button from "../../components/inputs/Button";
import Select from "../../components/inputs/Select";
import FormWrapper from "../../components/FormWrapper";
import useRoute from "../../hooks/useRoute";

const TaskForm = ({ roles }) => {
    const { route } = useRoute();
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        role_id: '',
    });

    function submit(e) {
        e.preventDefault();
        post(route('tasks.store'));
    }

    return (
        <FormWrapper>
            <Form onSubmit={submit}>

                <FormSection title="Task Details">
                    <div className="sm:col-span-6">
                        <Label label="Task Name" forInput="name" />
                        <TextInput name="name" value={data.name} updateFn={value => setData('name', value)} hasErrors={ !! errors['name'] } />
                        {errors.name && <Error>{errors.name}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Role" forInput="role" />
                        <Select
                            name="role" value={data.role_id}
                            options={roles.map(role => ({ key: role.id, label: role.name }))}
                            updateFn={value => setData('role_id', value)}
                            hasErrors={ !! errors['role_id'] }
                        />
                        {errors.role_id && <Error>{errors.role_id}</Error>}
                    </div>

                </FormSection>

                <FormFooter>
                    <ButtonLink href={route('tasks.index')}>Cancel</ButtonLink>
                    <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                </FormFooter>
            </Form>
        </FormWrapper>
    );
}

export default TaskForm;