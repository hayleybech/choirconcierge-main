import { CollapsePanelWithoutPadding } from '../../../components/CollapseGroup';
import Icon from '../../../components/Icon';
import React, { useState } from 'react';
import Button from '../../../components/inputs/Button';
import TextInput from '../../../components/inputs/TextInput';
import useRoute from '../../../hooks/useRoute';
import { useForm } from '@inertiajs/react';
import Dialog from '../../../components/Dialog';
import Form from '../../../components/Form';
import Label from '../../../components/inputs/Label';
import Error from '../../../components/inputs/Error';
import DateTag from "../../../components/DateTag";

const examples = [
	{ label: 'Favourite Colour', value: 'Purple', updated_at: '2025-11-25' },
	{ label: 'Favourite Artist', value: 'John Farnham', updated_at: '2025-11-25' },
	{ label: 'Favourite TV-Show', value: 'Stargate SG-1', updated_at: '2025-11-25' },
	{ label: 'Blank Example', value: '', updated_at: '2025-11-25' },
	{
		label: 'Long Example',
		value:
			'"Lorem ipsum" is a placeholder text used in graphic design, publishing, and web development to show the visual form of a document or typeface without relying on meaningful content. The text is derived from a scrambled Latin-language text by Cicero, but it is not intended to be readable and has been used as a dummy text for centuries to demonstrate layouts and fonts.',
		updated_at: '2025-11-25',
	},
];
const CustomFieldsSection = () => {
	const [showCreateDialog, setShowCreateDialog] = useState(false);

	return (
		<CollapsePanelWithoutPadding>
			<table className="w-full">
				<tbody className="divide-y divide-gray-200 border-b border-gray-200">
					{examples.map(({ label, value, updated_at }) => (
						<CustomFieldItem key={label} label={label} value={value} updated_at={updated_at} />
					))}
				</tbody>
				<tfoot>
					<tr>
						<td colSpan={3}>
							<div className="flex justify-center items-center gap-2 py-3 px-4 sm:px-6 lg:px-8">
								<div className="text-gray-700 text-sm">Add a new custom field</div>
								<Button variant="primary" size="xs" onClick={() => setShowCreateDialog(true)}>
									<Icon icon="plus" />
									Create
								</Button>
							</div>
						</td>
					</tr>
				</tfoot>
			</table>

			<CreateCustomFieldDialog isOpen={showCreateDialog} setIsOpen={setShowCreateDialog} />
		</CollapsePanelWithoutPadding>
	);
};

export default CustomFieldsSection;

const CustomFieldItem = ({ label, value, updated_at }) => {
	const [isEditing, setIsEditing] = useState(false);

	return (
		<tr
			key={label}
			className="[&>td:first-child]:pl-4 [&>td:first-child]:sm:pl-6 [&>td:first-child]:lg:pl-8 [&>td:last-child]:pr-4 [&>td:last-child]:sm:pr-6 [&>td:last-child]:lg:pr-8"
		>
			<td className="py-3 leading-tight lg:whitespace-nowrap text-sm lg:text-base">
				<strong>{label}</strong>
			</td>

			<td className="py-3 text-sm text-gray-700 px-3 leading-snug w-full">
				{isEditing ? (
					<div className="flex gap-1 items-center">
						<div className="-mt-1 grow">
							<TextInput value={value} className="p-1 text-sm leading-none" wrapperClasses="mt-0" />
						</div>
						<Button variant="primary" size="xs" onClick={() => setIsEditing(prev => !prev)}>
							<Icon icon="save" />
							<div className="hidden md:inline">Save</div>
						</Button>
					</div>
				) : (
					<div className="flex gap-2 justify-between">
						{value}
						{!!value && (
							<span className="text-sm text-gray-500 italic hidden md:inline shrink-0">
								<DateTag icon="pencil" date={updated_at} label="Updated" />
							</span>
						)}
					</div>
				)}
			</td>

			<td className="py-3">
				<div className="flex items-center justify-end gap-2">
					{isEditing ? (
						<Button variant="secondary" size="xs" onClick={() => setIsEditing(false)}>
							<Icon icon="times" />
							<div className="hidden md:inline">Cancel</div>
						</Button>
					) : (
						<Button variant="primary" size="xs" onClick={() => setIsEditing(true)}>
							<Icon icon="edit" />
							<div className="hidden md:inline">Edit</div>
						</Button>
					)}

					<Button variant="danger-outline" size="xs">
						<Icon icon="trash" />
						<div className="hidden md:inline">Delete</div>
					</Button>
				</div>
			</td>
		</tr>
	);
};

const CreateCustomFieldDialog = ({ isOpen, setIsOpen }) => {
	const { route } = useRoute();

	const { data, setData, post, errors } = useForm({
		name: '',
	});

	function submit(e) {
		e.preventDefault();
		post(route('custom-fields.store'), {
			onSuccess: () => setIsOpen(false),
		});
	}

	return (
		<Dialog
			title="Create Custom Field"
			okLabel="Create"
			onOk={submit}
			okVariant="primary"
			isOpen={isOpen}
			setIsOpen={setIsOpen}
		>
			<p className="mb-2">New custom fields appear on all profiles for your organisation. </p>
			<Form onSubmit={submit}>
				<div className="sm:col-span-6">
					<Label label="Name" forInput="name" />
					<TextInput
						name="name"
						placeholder="e.g. Favourite Colour"
						value={data.name}
						updateFn={value => setData('name', value)}
						hasErrors={!!errors['name']}
					/>
					{errors.name && <Error>{errors.name}</Error>}
				</div>
			</Form>
		</Dialog>
	);
};
