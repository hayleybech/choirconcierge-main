import React from 'react';
import {useForm} from "@inertiajs/inertia-react";
import FormSection from "../FormSection";
import Label from "../inputs/Label";
import FileInput from "../inputs/FileInput";
import Error from "../inputs/Error";
import Button from "../inputs/Button";
import Icon from "../Icon";
import RadioGroup from "../inputs/RadioGroup";
import AttachmentType from "../../AttachmentType";

const SongAttachmentForm = ({ song }) => {
    const { data, setData, post, processing, errors } = useForm({
        attachment_uploads: [],
        type: Object.keys(AttachmentType.types)[0],
    });

    function submit(e) {
        e.preventDefault();
        post(route('songs.attachments.store', song.id));
    }

    return (
        <div className="md:max-w-5xl md:mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <FormSection title="Add Attachment">
                <div className="sm:col-span-6">
                    <RadioGroup
                        label={<Label label="Attachment Type" />}
                        options={Object.keys(AttachmentType.types).map(slug => ({
                            id: slug,
                            name: AttachmentType.get(slug).title,
                            textColour: AttachmentType.get(slug).textColour,
                            colour: AttachmentType.get(slug).textColour,
                            icon: AttachmentType.get(slug).icon,
                        }))}
                        vertical
                        selected={data.type}
                        setSelected={value => setData('type', value)}
                    />
                    {errors.type && <Error>{errors.type}</Error>}
                </div>
                <div className="sm:col-span-6">
                    <Label label="File Upload" forInput="attachment_uploads" />
                    <FileInput
                        name="attachment_uploads"
                        value={data.attachment_uploads}
                        updateFn={value => setData('attachment_uploads', value)}
                        hasErrors={ !! errors['attachment_uploads'] }
                        multiple
                        vertical
                    />
                    {errors.attachment_uploads && <Error>{errors.attachment_uploads}</Error>}
                </div>
                <div className="">
                    <Button onClick={submit} variant="primary" size="sm" disabled={processing}><Icon icon="check" />Save</Button>
                </div>
            </FormSection>
        </div>
    );
}

export default SongAttachmentForm;