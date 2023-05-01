import React from 'react';
import {useForm} from "@inertiajs/inertia-react";
import Form from "../../components/Form";
import Label from "../../components/inputs/Label";
import Error from "../../components/inputs/Error";
import Button from "../../components/inputs/Button";
import FileInput from "../../components/inputs/FileInput";
import Icon from "../../components/Icon";
import useRoute from "../../hooks/useRoute";

const DocumentForm = ({ folder }) => {
    const { route } = useRoute();

    const { data, setData, post, processing, errors } = useForm({
        document_uploads: [],
    });

    function submit(e) {
        e.preventDefault();
        post(route('folders.documents.store', {folder}));
    }

    return (
        <Form onSubmit={submit}>
            <div className="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 items-end">
                <div className="sm:col-span-5">
                    <Label label="File Upload" forInput="document_uploads" />
                    <FileInput
                        name="document_uploads"
                        value={data.document_uploads}
                        updateFn={value => setData('document_uploads', value)}
                        hasErrors={ !! errors['document_uploads'] }
                        multiple
                    />
                    {errors.document_uploads && <Error>{errors.document_uploads}</Error>}
                </div>

                <div className="sm:col-span-1">
                    <Button variant="primary" type="submit" className="block w-full" disabled={processing}>
                        <Icon icon="file-plus" />
                        Upload
                    </Button>
                </div>

            </div>
        </Form>
    );
};

export default DocumentForm;