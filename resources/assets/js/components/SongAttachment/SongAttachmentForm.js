import React from 'react';
import { useForm } from '@inertiajs/react';
import Label from '../inputs/Label';
import FileInput from '../inputs/FileInput';
import Error from '../inputs/Error';
import RadioGroup from '../inputs/RadioGroup';
import AttachmentType from '../../AttachmentType';
import TextInput from '../inputs/TextInput';
import useRoute from '../../hooks/useRoute';
import Dialog from '../Dialog';
import Form from '../Form';

const SongAttachmentForm = ({ song, isOpen, setIsOpen }) => {
	const { route } = useRoute();

	const { data, setData, post, processing, errors, reset } = useForm({
		attachment_uploads: [],
		type: Object.keys(AttachmentType.types)[0],
		url: '',
		title: '',
	});

	function submit(e) {
		e.preventDefault();
		post(route('songs.attachments.store', { song: song.id }), {
			onSuccess: () => {
				reset();
				setIsOpen(false);
			},
		});
	}

	return (
		<Dialog
			title="Add Attachment"
			okLabel="Save"
			onOk={submit}
			okVariant="primary"
			isOpen={isOpen}
			setIsOpen={setIsOpen}
			processing={processing}
			icon={null}
		>
			<Form onSubmit={submit}>
				<div className="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
					<div className="sm:col-span-6">
						<RadioGroup
							label={<Label label="Attachment Type" />}
							options={Object.keys(AttachmentType.types).map(slug => ({
								id: slug,
								name: AttachmentType.get(slug).title,
								textColour: AttachmentType.get(slug).textColour,
								colour: AttachmentType.get(slug).textColour,
								icon: AttachmentType.get(slug).icon,
							}))}
							vertical
							selected={data.type}
							setSelected={value => setData('type', value)}
						/>
						{errors.type && <Error>{errors.type}</Error>}
					</div>
					{data.type === 'youtube' ? (
						<>
							<div className="sm:col-span-6">
								<Label label="YouTube URL" forInput="url" />
								<TextInput
									name="url"
									value={data.url}
									updateFn={value => setData('url', value)}
									hasErrors={!!errors['url']}
									placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ"
								/>
								{errors.url && <Error>{errors.url}</Error>}
							</div>
							<div className="sm:col-span-6">
								<Label label="Video description" forInput="title" />
								<TextInput
									name="title"
									value={data.title}
									updateFn={value => setData('title', value)}
									hasErrors={!!errors['title']}
									placeholder="Never Gonna Give You Up"
								/>
								{errors.title && <Error>{errors.title}</Error>}
							</div>
						</>
					) : (
						<div className="sm:col-span-6">
							<Label label="File Upload" forInput="attachment_uploads" />
							<FileInput
								name="attachment_uploads"
								value={data.attachment_uploads}
								updateFn={value => setData('attachment_uploads', value)}
								hasErrors={!!errors['attachment_uploads']}
								multiple
								vertical
							/>
							{errors.attachment_uploads && <Error>{errors.attachment_uploads}</Error>}
						</div>
					)}
				</div>
			</Form>
		</Dialog>
	);
};

export default SongAttachmentForm;
