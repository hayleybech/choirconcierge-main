import React, {useState} from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import VoicePartForm from "./VoicePartForm";
import useRoute from "../../hooks/useRoute";
import DeleteDialog from "../../components/DeleteDialog";

const Edit = ({ voice_part: voicePart }) => {
	const { route } = useRoute();

	const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);

	return (
		<>
			<AppHead title={`Edit - ${voicePart.title}`} />
			<PageHeader
				title="Edit Voice Part"
				icon="fa-users-class"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash')},
					{ name: 'Singers', url: route('singers.index')},
					{ name: 'Voice Parts', url: route('voice-parts.index')},
					{ name: voicePart.title, url: '#'},
					{ name: 'Edit', url: route('voice-parts.edit', {voice_part: voicePart})},
				]}
				actions={[
					{ label: 'Delete', icon: 'trash', onClick: () => setDeleteDialogIsOpen(true), variant: 'danger-outline', can: 'delete_voice_part' },
				].filter(action => action.can ? voicePart.can[action.can] : true)}
			/>

			<VoicePartForm voicePart={voicePart} />

			<DeleteDialog
				title="Delete Voice Part"
				url={route('voice-parts.destroy', {voice_part: voicePart})}
				isOpen={deleteDialogIsOpen}
				setIsOpen={setDeleteDialogIsOpen}
			>
				Are you sure you want to delete this voice part?
				All of its singers will have no voice part.
				This action cannot be undone.
			</DeleteDialog>
		</>
	);
}

Edit.layout = page => <TenantLayout children={page} />

export default Edit;