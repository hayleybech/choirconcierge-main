import React, {useEffect} from 'react';
import {useForm} from "@inertiajs/react";
import Dialog from "../../components/Dialog";
import Form from "../../components/Form";
import Label from "../../components/inputs/Label";
import Error from "../../components/inputs/Error";
import TextInput from "../../components/inputs/TextInput";
import useRoute from "../../hooks/useRoute";

const RenameDocumentDialog = ({ isOpen, setIsOpen, folder, document }) => {
    const { route } = useRoute();
    const [, extension] = splitFilename(document?.title ?? '');
    const { data, setData, put, errors } = useForm({title: document?.title ?? ''});

    useEffect(() => { setData('title', document?.title ?? ''); }, [document]);

    function submit(e) {
        e.preventDefault();
        put(route('folders.documents.update', {folder, document}), {onSuccess: () => setIsOpen(false)});
    }

    return <Dialog title="Rename document" okLabel="Rename" onOk={submit} okVariant="primary" isOpen={isOpen} setIsOpen={setIsOpen}>
        <Form onSubmit={submit}>
            <Label label="New name" forInput="title" />
            <FilenameInput name="title" value={data.title.replace(extension, '')} extension={extension} updateFn={value => setData('title', value + extension)} hasErrors={ !! errors.title } />
            {errors.title && <Error>{errors.title}</Error>}
        </Form>
    </Dialog>;
};

const splitFilename = (filename) => [
    filename.substring(0, filename.lastIndexOf('.')) || filename,
    filename.substring(filename.lastIndexOf('.')) || '',
];

const FilenameInput = ({ name, value, extension, hasErrors, updateFn }) => <div className="mt-1 flex">
    <TextInput name={name} value={value} updateFn={updateFn} hasErrors={hasErrors} className="rounded-r-none" />
    <span className="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm mt-1 shadow-sm">{extension}</span>
</div>;

export default RenameDocumentDialog;