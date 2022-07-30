import React from 'react';
import {useForm} from "@inertiajs/inertia-react";
import FormSection from "../../../components/FormSection";
import Label from "../../../components/inputs/Label";
import TextInput from "../../../components/inputs/TextInput";
import RangeInput from "../../../components/inputs/RangeInput";
import Button from "../../../components/inputs/Button";
import Error from "../../../components/inputs/Error";
import Select from "../../../components/inputs/Select";
import Icon from "../../../components/Icon";
import FormFooter from "../../../components/FormFooter";
import Form from "../../../components/Form";

const PlacementForm = ({ singer, placement, voice_parts }) => {
    const { data, setData, post, put, processing, errors } = useForm({
        experience: placement?.experience ?? '',
        instruments: placement?.instruments ?? '',
        skill_pitch: placement?.skill_pitch ?? 3,
        skill_harmony: placement?.skill_harmony ?? 3,
        skill_performance: placement?.skill_performance ?? 3,
        skill_sightreading: placement?.skill_sightreading ?? 3,
        voice_tone: placement?.voice_tone ?? 3,
        voice_part_id: singer.voice_part_id ?? 0,
    });

    function submit(e) {
        e.preventDefault();
        placement ? put(route('singers.placements.update', [singer, placement])) : post(route('singers.placements.store', [singer]));
    }

    return (
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
            <Form onSubmit={submit}>

                <FormSection title="Placement Details">
                    <div className="sm:col-span-6">
                        <Label label="Experience" forInput="experience" />
                        <TextInput
                            name="experience"
                            autoComplete="given-name"
                            value={data.experience}
                            updateFn={value => setData('experience', value)}
                            hasErrors={ !! errors['experience'] }
                        />
                        {errors.experience && <Error>{errors.experience}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Instruments" forInput="instruments" />
                        <TextInput
                            name="instruments"
                            autoComplete="given-name"
                            value={data.instruments}
                            updateFn={value => setData('instruments', value)}
                            hasErrors={ !! errors['instruments'] }
                        />
                        {errors.instruments && <Error>{errors.instruments}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Pitch Skill" forInput="skill_pitch" />
                        <div className="mt-1 flex space-x-4 items-center text-sm text-gray-500">
                            <div>1</div>
                            <RangeInput min={1} max={5} step={1} updateFn={value => setData('skill_pitch', value[0])} values={[data.skill_pitch]} />
                            <div>5</div>
                        </div>
                        {errors.skill_pitch && <Error>{errors.skill_pitch}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Harmony Skill" forInput="skill_harmony" />
                        <div className="mt-1 flex space-x-4 items-center text-sm text-gray-500">
                            <div>1</div>
                            <RangeInput min={1} max={5} step={1} updateFn={value => setData('skill_harmony', value[0])} values={[data.skill_harmony]} />
                            <div>5</div>
                        </div>
                        {errors.skill_harmony && <Error>{errors.skill_harmony}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Performance Skill" forInput="skill_performance" />
                        <div className="mt-1 flex space-x-4 items-center text-sm text-gray-500">
                            <div>1</div>
                            <RangeInput min={1} max={5} step={1} updateFn={value => setData('skill_performance', value[0])} values={[data.skill_performance]} />
                            <div>5</div>
                        </div>
                        {errors.skill_performance && <Error>{errors.skill_performance}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Sight Reading Skill" forInput="skill_sightreading" />
                        <div className="mt-1 flex space-x-4 items-center text-sm text-gray-500">
                            <div>1</div>
                            <RangeInput min={1} max={5} step={1} updateFn={value => setData('skill_sightreading', value[0])} values={[data.skill_sightreading]} />
                            <div>5</div>
                        </div>
                        {errors.skill_sightreading && <Error>{errors.skill_sightreading}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Voice Tone" forInput="voice_tone" />
                        <div className="mt-1 flex space-x-4 items-center text-sm text-gray-500">
                            <div className="text-center">
                                <Icon icon="flute" className="text-gray-600 fa-lg" />
                                <p className="text-xs">Fluty</p>
                            </div>
                            <RangeInput min={1} max={3} step={1} updateFn={value => setData('voice_tone', value[0])} values={[data.voice_tone]} />
                            <div className="text-center">
                                <Icon icon="trumpet" className="text-gray-600 fa-lg" />
                                <p className="text-xs">Brassy</p>
                            </div>
                        </div>
                        {errors.voice_tone && <Error>{errors.voice_tone}</Error>}
                    </div>

                    <div className="sm:col-span-6">
                        <Label label="Voice part" forInput="voice_part_id" />
                        <Select name="voice_part_id" options={voice_parts.map(part => ({ key: part.id, label: part.title}))} value={data.voice_part_id} updateFn={value => setData('voice_part_id', value)} />
                        {errors.voice_part_id && <Error>{errors.voice_part_id}</Error>}
                    </div>

                </FormSection>

                <FormFooter>
                    <Button href={route('singers.show', [singer])}>Cancel</Button>
                    <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
                </FormFooter>
            </Form>
        </div>
    );
}

export default PlacementForm;