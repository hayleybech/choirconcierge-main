import React from 'react';
import { useForm } from '@inertiajs/react';
import Form from './Form';
import Label from './inputs/Label';
import SongSelect from './inputs/SongSelect';
import TextInput from './inputs/TextInput';
import Error from './inputs/Error';
import useRoute from '../hooks/useRoute';
import Dialog from './Dialog';

const ScheduleItemForm = ({ event, isOpen, setIsOpen }) => {
	const { route } = useRoute();

	const { data, setData, post, processing, errors, reset } = useForm({
		song_id: null,
		description: '',
		duration: 0,
	});

	function submit(e) {
		e.preventDefault();
		post(route('events.activities.store', { event }), {
			onSuccess: () => {
				reset();
				setIsOpen(false);
			},
		});
	}

	return (
		<Dialog
			title="Add Activity"
			okLabel="Save"
			onOk={submit}
			okVariant="primary"
			isOpen={isOpen}
			setIsOpen={setIsOpen}
			processing={processing}
			icon={null}
		>
			<Form onSubmit={submit}>
				<div className="flex flex-col items-stretch gap-6 text-left">
					<div className="">
						<Label label="Song (Optional)" />
						<SongSelect updateFn={value => setData('song_id', value)} />
					</div>
					<div className="">
						<Label label="Description (Optional)" forInput="description" />
						<TextInput
							name="description"
							value={data.description}
							updateFn={value => setData('description', value)}
							hasErrors={!!errors['description']}
						/>
						{errors.description && <Error>{errors.description}</Error>}
					</div>
					<div className="">
						<Label label="Duration (Optional)" forInput="duration" />
						<TextInput
							name="duration"
							value={data.duration}
							updateFn={value => setData('duration', value)}
							hasErrors={!!errors['duration']}
							type="number"
							min={0}
						/>
						{errors.duration && <Error>{errors.duration}</Error>}
					</div>
				</div>
			</Form>
		</Dialog>
	);
};

export default ScheduleItemForm;
