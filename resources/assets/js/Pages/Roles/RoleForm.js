import React from 'react';
import {useForm} from "@inertiajs/inertia-react";
import FormSection from "../../components/FormSection";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import Error from "../../components/inputs/Error";
import ButtonLink from "../../components/inputs/ButtonLink";
import Button from "../../components/inputs/Button";
import Form from "../../components/Form";
import FormFooter from "../../components/FormFooter";
import CheckboxWithLabel from "../../components/inputs/CheckboxWithLabel";
import {modelsAndAbilities} from "./modelsAndAbilities";
import FormWrapper from "../../components/FormWrapper";
import useRoute from "../../hooks/useRoute";

const RoleForm = ({ role }) => {
    const { route } = useRoute();

    const { data, setData, post, put, processing, errors } = useForm({
        name: role?.name ?? '',
        abilities: role?.abilities ?? [],
    });

    function submit(e) {
        e.preventDefault();
        role ? put(route('roles.update', {role})) : post(route('roles.store'));
    }

    function toggleAllAbilitiesForModel(modelKey, value) {
        return toggleArrayValues(
            modelsAndAbilities[modelKey].abilities.map((abilityKey) => `${modelKey}_${abilityKey}`),
            data.abilities,
            value
        );
    }

    function allAbilitiesForModelChecked(modelKey) {
        return modelsAndAbilities[modelKey].abilities
            .map((abilityKey) => `${modelKey}_${abilityKey}`)
            .every((modelAbilityKey) => data.abilities.includes(modelAbilityKey));
    }

    return (
        <FormWrapper>
            <Form onSubmit={submit}>

                <FormSection title="Role Details">
                    <div className="sm:col-span-6">
                        <Label label="Role Name" forInput="name" />
                        <TextInput name="name" value={data.name} updateFn={value => setData('name', value)} hasErrors={ !! errors['name'] } />
                        {errors.name && <Error>{errors.name}</Error>}
                    </div>

                    <table className="sm:col-span-6">
                        <thead>
                        <tr className="text-center md:text-left text-gray-900">
                            <th className="py-4 text-left">Model</th>
                            <th className="py-4">View</th>
                            <th className="py-4">Create</th>
                            <th className="py-4">Update</th>
                            <th className="py-4">Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        {objectMap(modelsAndAbilities, (modelKey, { label: modelName, abilities }) => (
                            <tr key={modelKey}>
                                <th className="py-4 px-2 text-left">
                                    <CheckboxWithLabel
                                        label={modelName}
                                        id={`${modelName}_all`}
                                        name={`${modelName}_all`}
                                        value="true"
                                        className="font-bold flex-col md:flex-row items-start"
                                        onChange={(e) => setData('abilities', toggleAllAbilitiesForModel(modelKey, e.target.checked))}
                                        checked={allAbilitiesForModelChecked(modelKey)}
                                    />
                                </th>
                                {(abilities.map((abilityKey) => (
                                <td className="py-4 px-2" key={`${modelKey}_${abilityKey}`}>
                                    <CheckboxWithLabel
                                        label={abilityKey[0].toUpperCase() + abilityKey.substring(1)}
                                        id={`${modelKey}_${abilityKey}`}
                                        name="abilities[]"
                                        value={`${modelKey}_${abilityKey}`}
                                        checked={data.abilities.includes(`${modelKey}_${abilityKey}`)}
                                        onChange={(e) => setData(
                                            'abilities',
                                            toggleArrayValue(`${modelKey}_${abilityKey}`, data.abilities, e.target.checked)
                                        )}
                                        className="flex-col md:flex-row items-center"
                                    />
                                </td>
                                )))}
                            </tr>
                        ))}
                        </tbody>
                    </table>

                </FormSection>

                <FormFooter>
                    <ButtonLink href={role ? route('roles.show', {role}) : route('roles.index')}>Cancel</ButtonLink>
                    <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                </FormFooter>
            </Form>
        </FormWrapper>
    );
}

export default RoleForm;

function toggleArrayValues(keys, array, on) {
    let newArray = array;
    keys.forEach((key) => newArray = toggleArrayValue(key, newArray, on));
    return newArray;
}

function toggleArrayValue(key, array, on) {
    if(on) {
        return addToArrayOnce(key, array);
    }

    return deleteFromArray(key, array);
}

function addToArrayOnce(item, array) {
    if(array.includes(item)) {
        return array;
    }

    return addToArray(item, array);
}

function addToArray(item, array) {
    return [...new Set(array).add(item)];
}

function deleteFromArray(item, array) {
    let set = new Set(array);
    set.delete(item);
    return [...set];
}

function objectMap(object, fn) {
    return Object.keys(object).map((key) => fn(key, object[key]));
}