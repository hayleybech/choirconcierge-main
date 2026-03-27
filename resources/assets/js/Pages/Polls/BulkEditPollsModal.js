import React from 'react';
import Dialog from '../../components/Dialog';
import Label from '../../components/inputs/Label';
import { useForm } from '@inertiajs/react';
import useRoute from '../../hooks/useRoute';
import Error from '../../components/inputs/Error';
import CheckboxGroup from '../../components/inputs/CheckboxGroup';

import TimeInput from '../../components/inputs/Time';
import { DateTime } from 'luxon';
import DateInput from '../../components/inputs/Date';
import Help from '../../components/inputs/Help';
import RadioGroup from '../../components/inputs/RadioGroup';

const BulkEditPollsModal = ({ isOpen, setIsOpen, selectedPollIds, ensembles, onSuccess }) => {
	const { route } = useRoute();

	const { data, setData, post, processing, reset, transform, errors } = useForm({
		poll_ids: selectedPollIds,
		is_closed: null,
		close_at: null,
		ensemble_ids: [],
	});

	const rawDateFormat = 'yyyy-MM-dd HH:mm:ss';

	transform(data => ({
		...data,
		close_at: data.close_at ? data.close_at.toFormat(rawDateFormat) : null,
	}));

	function setCloseAtDate(value) {
		const date = DateTime.fromJSDate(value);
		const target = data.close_at ?? DateTime.now().set({ hour: 23, minute: 59, second: 59 });
		setData(
			'close_at',
			target.set({
				year: date.year,
				month: date.month,
				day: date.day,
			})
		);
	}

	function setCloseAtTime(value) {
		const time = DateTime.fromISO(value);
		const target = data.close_at ?? DateTime.now();
		setData(
			'close_at',
			target.set({
				hour: time.hour,
				minute: time.minute,
				second: 0,
			})
		);
	}

	const handleOk = e => {
		e.preventDefault();

		post(route('polls.bulk-update'), {
			preserveScroll: true,
			onSuccess: () => {
				setIsOpen(false);
				reset();
				if (onSuccess) onSuccess();
			},
		});
	};

	return (
		<Dialog
			title={`Edit ${selectedPollIds.length} Polls`}
			isOpen={isOpen}
			setIsOpen={setIsOpen}
			okLabel="Update Polls"
			onOk={handleOk}
			processing={processing}
			icon="pencil"
			okVariant="primary"
		>
			<div className="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
				{/* Status */}
				<div className="sm:col-span-6">
					<RadioGroup
						label={<Label label="Status" />}
						options={[
							{ id: '0', name: 'Open', icon: 'check', colour: 'text-emerald-700' },
							{ id: '1', name: 'Closed', icon: 'lock', colour: 'text-gray-700' },
						]}
						selected={data.is_closed === null ? null : data.is_closed ? '1' : '0'}
						setSelected={value => {
							setData('is_closed', value === '1');
						}}
						vertical
						size="sm"
					/>
					<button
						type="button"
						className="mt-1 text-sm font-medium text-purple-600 hover:text-purple-500"
						onClick={() => setData('is_closed', null)}
					>
						Clear
					</button>
					{errors.is_closed && <Error>{errors.is_closed}</Error>}
				</div>

				{/* Deadline */}
				<div className="sm:col-span-4 relative z-20">
					<Label forInput="close_at">Deadline (optional)</Label>
					<DateInput
						name="close_at"
						value={data.close_at}
						updateFn={setCloseAtDate}
						hasErrors={!!errors.close_at}
					/>
					{errors.close_at && <p className="text-sm text-red-600 mt-1">{errors.close_at}</p>}
					{data.close_at && <Help>Will close at {data.close_at.toLocaleString(DateTime.DATETIME_MED)}</Help>}
				</div>

				<div className="sm:col-span-2">
					<Label forInput="close_at_time">Time</Label>
					<TimeInput
						name="close_at_time"
						value={data.close_at?.toLocaleString(DateTime.TIME_24_SIMPLE) ?? null}
						updateFn={setCloseAtTime}
						disabled={!data.close_at}
					/>
				</div>

				{/* Ensembles */}
				<div className="sm:col-span-6">
					<Label label="Ensembles" forInput="ensemble_ids" />
					<CheckboxGroup
						name="ensemble_ids"
						options={ensembles.map(ensemble => ({ id: ensemble.id, name: ensemble.name }))}
						value={data.ensemble_ids}
						updateFn={value => setData('ensemble_ids', value)}
					/>
					{errors.ensemble_ids && <Error>{errors.ensemble_ids}</Error>}
				</div>
			</div>
		</Dialog>
	);
};

export default BulkEditPollsModal;
