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
import DateTag from '../../../components/DateTag';
import { useMediaQuery } from 'react-responsive';
import DeleteDialog from '../../../components/DeleteDialog';

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
const CustomFieldsSection = ({ singer, customFields }) => {
	const [showCreateDialog, setShowCreateDialog] = useState(false);

	return (
		<CollapsePanelWithoutPadding>
			<table className="w-full">
				<tbody className="divide-y divide-gray-200 border-b border-gray-200">
					{customFields.map(({ id, name, entries }) => (
						<CustomFieldItem key={id} id={id} label={name} entry={entries[0]} singer={singer} />
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

const CustomFieldItem = ({ id, label, entry, singer }) => {
	const [isEditing, setIsEditing] = useState(false);
	const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);

	const isXl = useMediaQuery({ query: '(min-width: 1280px)' });

	const { route } = useRoute();

	const { data, setData, post, put, errors } = useForm({
		value: entry?.value ?? '',
		customFieldId: id,
	});

	function submit(e) {
		e.preventDefault();

		entry
			? put(route('singers.custom-fields.update', { singer, entry }), {
					onSuccess: () => setIsEditing(false),
			  })
			: post(route('singers.custom-fields.store', { singer }), {
					onSuccess: () => setIsEditing(false),
			  });
	}

	return (
		<tr
			key={label}
			className="[&>td:first-child]:pl-4 [&>td:first-child]:sm:pl-6 [&>td:first-child]:lg:pl-8 [&>td:last-child]:pr-4 [&>td:last-child]:sm:pr-6 [&>td:last-child]:lg:pr-8"
		>
			<td className="py-3 leading-tight xl:whitespace-nowrap text-sm xl:text-base">
				<strong>{label}</strong>
			</td>

			<td className="py-3 text-sm text-gray-700 px-3 leading-snug w-full">
				{isEditing ? (
					<form className="flex gap-1 items-center" onSubmit={e => submit(e)}>
						<div className="-mt-1 grow">
							<TextInput
								name="value"
								value={data.value}
								updateFn={value => setData('value', value)}
								hasErrors={!!errors['value']}
								className="p-1 text-sm leading-none"
								wrapperClasses="mt-0"
							/>
						</div>
						<Button variant="primary" size="xs" onClick={e => submit(e)}>
							<Icon icon="save" />
							<div className="hidden md:inline">Save</div>
						</Button>
					</form>
				) : (
					<div className="flex gap-2 justify-between">
						{entry?.value}
						{!!entry && (
							<span className="text-sm text-gray-500 italic hidden md:inline shrink-0">
								<DateTag
									icon="pencil"
									date={entry?.updated_at}
									label={isXl ? 'Updated' : ''}
									format={isXl ? 'DATE_MED' : 'DATE_SHORT'}
									mr={false}
									className="flex items-center gap-x-1 xl:gap-x-1.5"
								/>
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

					<Button variant="danger-outline" size="xs" onClick={() => setDeleteDialogIsOpen(true)}>
						<Icon icon="trash" />
						<div className="hidden md:inline">Delete</div>
					</Button>

					<DeleteDialog
						title="Delete Custom Field"
						url={route('custom-fields.destroy', { custom_field: id })}
						isOpen={deleteDialogIsOpen}
						setIsOpen={setDeleteDialogIsOpen}
					>
						Are you sure you want to delete this custom field?
						<br />
						This will be deleted for <strong>all users.</strong>
						<br />
						This action cannot be undone.
						<br />
					</DeleteDialog>
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
