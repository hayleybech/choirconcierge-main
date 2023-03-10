import {useForm} from "@inertiajs/inertia-react";
import Dialog from "./Dialog";
import Form from "./Form";
import Label from "./inputs/Label";
import FileInput from "./inputs/FileInput";
import Help from "./inputs/Help";
import Error from "./inputs/Error";
import React from "react";
import useRoute from "../hooks/useRoute";

const ImportSingersDialog = ({ isOpen, setIsOpen }) => {
    const { route } = useRoute();
    const { data, setData, post, errors } = useForm({
        import_csv: null,
    });

    function submit(e) {
        e.preventDefault();
        post(route('singers.import'), {
            onSuccess: () => setIsOpen(false),
        });
    }

    return (
        <Dialog
            title="Import singers"
            okLabel="Import"
            onOk={submit}
            okVariant="primary"
            isOpen={isOpen}
            setIsOpen={setIsOpen}
        >
            <Form onSubmit={submit}>
                <div className="sm:col-span-6">
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
                    </Help>
                    {errors.import_csv && <Error>{errors.import_csv}</Error>}
                </div>
            </Form>
        </Dialog>
    );
};

export default ImportSingersDialog;