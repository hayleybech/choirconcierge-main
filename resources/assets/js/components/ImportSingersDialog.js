import {useForm} from "@inertiajs/react";
import Dialog from "./Dialog";
import Form from "./Form";
import Label from "./inputs/Label";
import FileInput from "./inputs/FileInput";
import Help from "./inputs/Help";
import Error from "./inputs/Error";
import React, {useState} from "react";
import useRoute from "../hooks/useRoute";
import axios from "axios";

const ImportSingersDialog = ({ isOpen, setIsOpen }) => {
    const { route } = useRoute();
    const { data, setData, post, errors, clearErrors, setError } = useForm({
        import_csv: null,
    });
    const [previewData, setPreviewData] = useState(null);
    const [loadingPreview, setLoadingPreview] = useState(false);

    function getPreview(e) {
        e.preventDefault();
        clearErrors();
        if (!data.import_csv) {
            setError('import_csv', 'Please select a file.');
            return;
        }

        setLoadingPreview(true);
        const formData = new FormData();
        formData.append('import_csv[0]', data.import_csv[0]);

        axios.post(route('singers.import.preview'), formData)
            .then(response => {
                setPreviewData(response.data);
                setLoadingPreview(false);
            })
            .catch(error => {
                setLoadingPreview(false);
                if (error.response && error.response.data.errors) {
                    Object.keys(error.response.data.errors).forEach(key => {
                        setError(key, error.response.data.errors[key][0]);
                    });
                }
            });
    }

    function submit(e) {
        e.preventDefault();
        post(route('singers.import'), {
            onSuccess: () => {
                setIsOpen(false);
                setPreviewData(null);
            },
        });
    }

    return (
        <Dialog
            title="Import singers"
            okLabel={previewData ? "Confirm Import" : "Preview"}
            onOk={previewData ? submit : getPreview}
            okVariant="primary"
            isOpen={isOpen}
            setIsOpen={setIsOpen}
            processing={loadingPreview}
        >
            <Form onSubmit={previewData ? submit : getPreview}>
                <div className="sm:col-span-6">
                    {!previewData && (
                        <>
                            <Label label="Import CSV File" forInput="import_csv" />
                            <FileInput
                                name="import_csv"
                                value={data.import_csv}
                                updateFn={value => setData('import_csv', value)}
                                hasErrors={ !! errors['import_csv'] }
                            />
                            <Help>
                                <p className="mb-2">NOTE: This is a potentially destructive action and your file will not be validated.</p>

                                <p className="mb-2">Supported file types:</p>
                                <ul className="list-disc list-inside mb-2">
                                    <li>Groupanizer</li>
                                    <li>HarmonySite</li>
                                    <li>Choir Concierge</li>
                                </ul>
                                <p className="mb-2">Contact us for info on the specific requirements of these file types.</p>
                                <p className="mt-4">
                                    Need a template? <a className="text-purple-600 underline hover:no-underline" href={route('singers.import.template')}>Download a blank CSV template</a>
                                </p>
                            </Help>
                        </>
                    )}

                    {previewData && (
                        <div className="mt-4">
                            <p className="mb-2 font-semibold">Previewing {previewData.data.length} of {previewData.total} rows:</p>
                            <div className="overflow-x-auto border rounded">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            {Object.keys(previewData.data[0] || {}).map(heading => (
                                                <th key={heading} className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {heading}
                                                </th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {previewData.data.map((row, i) => (
                                            <tr key={i}>
                                                {Object.values(row).map((value, j) => (
                                                    <td key={j} className="px-3 py-2 whitespace-nowrap text-xs text-gray-500">
                                                        {value}
                                                    </td>
                                                ))}
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            <button
                                type="button"
                                className="mt-4 text-sm text-purple-600 underline"
                                onClick={() => setPreviewData(null)}
                            >
                                Upload a different file
                            </button>
                        </div>
                    )}
                    {errors.import_csv && <Error>{errors.import_csv}</Error>}
                </div>
            </Form>
        </Dialog>
    );
};

export default ImportSingersDialog;
