import React from 'react';
import {useForm} from "@inertiajs/inertia-react";
import FormSection from "../FormSection";
import Label from "../inputs/Label";
import FileInput from "../inputs/FileInput";
import Error from "../inputs/Error";
import Select from "../inputs/Select";
import Button from "../inputs/Button";
import Icon from "../Icon";

const SongAttachmentForm = ({ categories, song }) => {
    const { data, setData, post, processing, errors } = useForm({
        attachment_uploads: [],
        category: null,
    });

    function submit(e) {
        e.preventDefault();
        post(route('songs.attachments.store', song.id));
    }

    return (
        <div className="md:max-w-5xl md:mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <FormSection title="Add Attachment">
                <div className="sm:col-span-3">
                    <Label label="File Upload" forInput="attachment_uploads" />
                    <FileInput
                        name="attachment_uploads"
                        value={data.attachment_uploads}
                        updateFn={value => setData('attachment_uploads', value)}
                        hasErrors={ !! errors['attachment_uploads'] }
                        multiple
                    />
                    {errors.attachment_uploads && <Error>{errors.attachment_uploads}</Error>}
                </div>
                <div className="sm:col-span-3">
                    <Label label="Category" forInput="category" />
                    <Select
                        name="category"
                        options={categories.map(category => ({ key: category.id, label: category.title }) )}
                        value={data.category}
                        updateFn={value => setData('category', value)} hasErrors={ !! errors['category'] }
                    />
                    {errors.category && <Error>{errors.category}</Error>}
                </div>
                <div className="">
                    <Button onClick={submit} variant="primary" size="sm" disabled={processing}><Icon icon="check" mr />Save</Button>
                </div>
            </FormSection>
        </div>
    );
}

export default SongAttachmentForm;