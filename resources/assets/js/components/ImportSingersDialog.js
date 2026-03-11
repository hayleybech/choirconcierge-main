import {useForm, usePage} from "@inertiajs/react";
import Dialog from "./Dialog";
import Form from "./Form";
import Label from "./inputs/Label";
import FileInput from "./inputs/FileInput";
import Help from "./inputs/Help";
import Error from "./inputs/Error";
import React, {useEffect, useState} from "react";
import useRoute from "../hooks/useRoute";

const ImportSingersDialog = ({ isOpen, setIsOpen }) => {
    const { route } = useRoute();
    const { props } = usePage();
    const { data, setData, post, errors, clearErrors, setError, processing } = useForm({
        import_csv: null,
    });
    const [previewData, setPreviewData] = useState(null);

    useEffect(() => {
        if (props.flash.preview) {
            setPreviewData(props.flash.preview);
        }
    }, [props.flash.preview]);

    function getPreview(e) {
        e.preventDefault();
        clearErrors();
        if (!data.import_csv) {
            setError('import_csv', 'Please select a file.');
            return;
        }

        post(route('singers.import.preview'), {
            preserveState: true,
            preserveScroll: true,
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
			okLabel={previewData ? 'Confirm Import' : 'Preview'}
			onOk={previewData ? submit : getPreview}
			okVariant="primary"
			isOpen={isOpen}
			setIsOpen={setIsOpen}
			processing={processing}
		>
			<Form onSubmit={previewData ? submit : getPreview}>
				<div className="sm:col-span-6 text-left">
					{!previewData && (
						<>
							<Label label="Import CSV File" forInput="import_csv" />
							<FileInput
								name="import_csv"
								value={data.import_csv}
								updateFn={value => setData('import_csv', value)}
								hasErrors={!!errors['import_csv']}
							/>
							<Help>
								<p className="mb-2">
									NOTE: This is a potentially destructive action and your file will not be validated.
								</p>

								<p className="mb-2">Supported file types:</p>
								<ul className="list-disc list-inside mb-2">
									<li>
										Create new import.{' '}
										<a
											className="text-purple-600 underline hover:no-underline"
											href={route('singers.import.template')}
										>
											Download CSV template
										</a>
									</li>
									<li>Export from Groupanizer / Choir Genius</li>
									<li>Export from HarmonySite</li>
								</ul>
								<p className="mb-2">
									Contact us for info on the specific requirements of these file types.
								</p>
								<p className="mb-2">
									To import a singer with multiple ensemble enrolements / voice parts, use this format
									in the Voice Part column: <br />
									<code>Ensemble 1 - Voice Part 1;Ensemble 2 - Voice Part 2</code>
								</p>
							</Help>
						</>
					)}

					{previewData && (
						<div className="mt-4">
							<p className="mb-2 font-semibold">
								Previewing {previewData.data.length} of {previewData.total} rows:
							</p>
							<div className="overflow-x-auto border rounded">
								<table className="min-w-full divide-y divide-gray-200">
									<thead className="bg-gray-50">
										<tr>
											{Object.keys(previewData.data[0] || {}).map(heading => (
												<th
													key={heading}
													className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
												>
													{heading}
												</th>
											))}
										</tr>
									</thead>
									<tbody className="bg-white divide-y divide-gray-200">
										{previewData.data.map((row, i) => (
											<tr key={i}>
												{Object.values(row).map((value, j) => (
													<td
														key={j}
														className="px-3 py-2 whitespace-nowrap text-xs text-gray-500"
													>
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
								onClick={() => {
									setPreviewData(null);
									setData('import_csv', null);
								}}
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
