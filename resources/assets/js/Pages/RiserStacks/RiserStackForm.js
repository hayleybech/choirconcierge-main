import React, {useState} from 'react';
import { useForm, usePage } from "@inertiajs/react";
import FormSection from "../../components/FormSection";
import Label from "../../components/inputs/Label";
import TextInput from "../../components/inputs/TextInput";
import Error from "../../components/inputs/Error";
import ButtonLink from "../../components/inputs/ButtonLink";
import Button from "../../components/inputs/Button";
import RiserStackEditor from "../../components/RiserStack/RiserStackEditor";
import DetailToggle from "../../components/inputs/DetailToggle";
import RiserStackHoldingArea from "../../components/RiserStack/RiserStackHoldingArea";
import FormFooter from "../../components/FormFooter";
import Form from "../../components/Form";
import useRoute from "../../hooks/useRoute";
import useMetricImperialPreference from "../../hooks/useMetricImperialPreference";
import CheckboxGroup from "../../components/inputs/CheckboxGroup";

const RiserStackForm = ({ stack, voiceParts, singers, ensembles }) => {
    const { route } = useRoute();

    const { user } = usePage().props;

    const [showHeights, setShowHeights] = useState(false);
    const [showImperial, setShowImperial] = useMetricImperialPreference();

    const { data, setData, post, put, processing, errors } = useForm({
        title: stack?.title ?? '',
        rows: stack?.rows ?? 4,
        columns: stack?.columns ?? 4,
        front_row_length: stack?.front_row_length ?? 1,
        front_row_on_floor: stack?.front_row_on_floor ?? false,
        singer_positions: stack?.members ??  [],
        ensembles: stack?.ensembles?.map(e => e.id) ?? [],
    });

    function submit(e) {
        e.preventDefault();
        stack ? put(route('stacks.update', {stack})) : post(route('stacks.store'));
    }

    const [selectedSinger, setSelectedSinger] = useState(null);
    const [holdingAreaSingers, setHoldingAreaSingers] = useState(singers);

    function removeSingerFromHoldingArea(singerRemoving){
        setHoldingAreaSingers(
          holdingAreaSingers.filter(singer => singer.id !== singerRemoving.id)
        );
    }

    function moveSingerToHoldingArea(singer){
        removeSingerFromStack(singer);

        setHoldingAreaSingers(holdingAreaSingers => {
            holdingAreaSingers.push(singer)
            return holdingAreaSingers;
        });
    }

    function removeSingerFromStack(singer){
        setData('singer_positions', data.singer_positions.filter(stackSinger => stackSinger.id !== singer.id));
    }

    return (
        <Form onSubmit={submit}>

            <div className="px-8">
                <FormSection>
                    <div className="sm:col-span-3">
                        <Label label="Riser Stack Title" forInput="title" />
                        <TextInput name="title" value={data.title} updateFn={value => setData('title', value)} hasErrors={ !! errors['title'] } />
                        {errors.title && <Error>{errors.title}</Error>}
                    </div>

                    <div className="sm:col-span-1">
                        <Label label="Rows" forInput="rows" />
                        <TextInput
                            name="rows"
                            type="number"
                            min={1}
                            value={data.rows}
                            updateFn={value => setData('rows', value)}
                            hasErrors={ !! errors['rows'] }
                        />
                        {errors.rows && <Error>{errors.rows}</Error>}
                    </div>

                    <div className="sm:col-span-1">
                        <Label label="Columns" forInput="columns" />
                        <TextInput
                            name="columns"
                            type="number"
                            min={1}
                            value={data.columns}
                            updateFn={value => setData('columns', value)}
                            hasErrors={ !! errors['columns'] }
                        />
                        {errors.columns && <Error>{errors.columns}</Error>}
                    </div>

                    <div className="sm:col-span-1">
                        <Label label="Front Row Singers" forInput="front_row_length" />
                        <TextInput
                            name="front_row_length"
                            type="number"
                            min={1}
                            value={data.front_row_length}
                            updateFn={value => setData('front_row_length', value)}
                            hasErrors={ !! errors['front_row_length'] }
                        />
                        {errors.front_row_length && <Error>{errors.front_row_length}</Error>}
                    </div>

                    <div className="sm:col-span-1">
                        <DetailToggle
                            label="Front row on floor?"
                            description="Show a 'floor' row in front of the riser stack."
                            value={data.front_row_on_floor}
                            updateFn={value => setData('front_row_on_floor',  value)}
                        />
                    </div>

                    <div className="sm:col-span-1">
                        <DetailToggle
                          label="Show heights"
                          description="Show singers' heights instead of initials"
                          value={showHeights}
                          updateFn={value => setShowHeights(value)}
                        />
                    </div>
                    <div className="sm:col-span-1">
                        <DetailToggle
                            label="Metric/Imperial"
                            description={`Showing ${showImperial ? 'imperial' : 'metric'}`}
                            value={showImperial}
                            updateFn={setShowImperial}
                            disabled={!showHeights}
                        />
                    </div>

                    {ensembles.length > 1 && (
                        <div className="sm:col-span-6">
                            <fieldset>
                                <legend className="text-sm font-medium text-gray-700">Ensembles</legend>
                                <CheckboxGroup
                                    name="ensembles"
                                    options={ensembles.map((ensemble) => ({ id: ensemble.id, name: ensemble.name }))}
                                    value={data.ensembles}
                                    updateFn={value => setData('ensembles', value)}
                                />
                                {errors.ensembles && <Error>{errors.ensembles}</Error>}
                            </fieldset>
                        </div>
                    )}

                </FormSection>
            </div>

            <div className="flex flex-wrap">
                <div className="w-full md:w-1/3 border-r border-gray-200">
                    <RiserStackHoldingArea
                        voiceParts={voiceParts}
                        singers={holdingAreaSingers}
                        setSelectedSinger={setSelectedSinger}
                        selectedSinger={selectedSinger}
                        moveSelectedSingerToHoldingArea={() => { moveSingerToHoldingArea(selectedSinger); setSelectedSinger(null); }}
                        showImperial={showImperial}
                    />
                </div>

                <div className="w-full md:w-2/3 overflow-scroll">
                    <RiserStackEditor
                        editing
                        rows={parseInt(data.rows)}
                        columns={parseInt(data.columns)}
                        width={1000}
                        height={500}
                        frontRowOnFloor={data.front_row_on_floor}
                        spotsOnFrontRow={parseInt(data.front_row_length)}
                        singerPositions={data.singer_positions}
                        setPositions={value => setData('singer_positions', value)}
                        setSelectedSinger={setSelectedSinger}
                        selectedSinger={selectedSinger}
                        removeSingerFromHoldingArea={removeSingerFromHoldingArea}
                        showHeights={showHeights}
                        showImperial={showImperial}
                        currentUserId={user.id}
                    />
                </div>
            </div>

            <FormFooter>
                <ButtonLink href={stack ? route('stacks.show', {stack}) : route('stacks.index')}>Cancel</ButtonLink>
                <Button variant="primary" type="submit" className="ml-3" disabled={processing}>Save</Button>
            </FormFooter>
        </Form>
    );
}

export default RiserStackForm;