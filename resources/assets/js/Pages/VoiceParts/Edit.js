import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import VoicePartForm from "./VoicePartForm";
import useRoute from "../../hooks/useRoute";

const Edit = ({ voice_part: voicePart }) => {
	const { route } = useRoute();

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
			/>

			<VoicePartForm voicePart={voicePart} />
		</>
	);
}

Edit.layout = page => <TenantLayout children={page} />

export default Edit;