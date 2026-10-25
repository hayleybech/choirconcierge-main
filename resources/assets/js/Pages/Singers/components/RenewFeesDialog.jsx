import React, { useEffect } from 'react';
import { useForm, usePage } from '@inertiajs/react';
import { DateTime } from 'luxon';
import Dialog from '../../../components/Dialog';
import Form from '../../../components/Form';
import Label from '../../../components/inputs/Label';
import DateTextInput from '../../../components/inputs/DateTextInput';
import Error from '../../../components/inputs/Error';
import Help from '../../../components/inputs/Help';
import RadioGroup from '../../../components/inputs/RadioGroup';
import useRoute from '../../../hooks/useRoute';

const feeDiffs = {
	month: { months: 1 },
	quarter: { quarters: 1 },
	halfyear: { months: 6 },
	year: { years: 1 },
};

const RenewFeesDialog = ({ isOpen, setIsOpen, singer, singerIds, onSuccess }) => {
	const { route } = useRoute();
	const { tenant } = usePage().props;
	const isBulk = singerIds?.length > 0;

	const { data, setData, put, post, errors, transform } = useForm({
		freq: tenant.fee_freq ?? null,
		paid_until: singer?.paid_until ? DateTime.fromISO(singer.paid_until) : null,
	});

	useEffect(() => {
		if (isBulk) {
			return;
		}

		const prevDate = singer?.paid_until ? DateTime.fromISO(singer.paid_until) : null;
		if (tenant.fee_freq) {
			const diff = feeDiffs[tenant.fee_freq];
			setData('paid_until', prevDate?.plus(diff) ?? DateTime.now().plus(diff));
		} else {
			setData('paid_until', prevDate);
		}
	}, [singer, isBulk]);

	transform(data =>
		isBulk
			? { singer_ids: singerIds, freq: data.freq }
			: {
					...data,
					paid_until: data.paid_until?.toISODate() ?? null,
			  }
	);

	function submit(e) {
		e.preventDefault();
		const options = {
			onSuccess: () => {
				setIsOpen(false);
				onSuccess?.();
			},
		};

		if (isBulk) {
			post(route('singers.bulk-fees.update'), { preserveScroll: true, ...options });
			return;
		}

		put(route('singers.fees.update', { singer }), options);
	}

	function renewFor(period) {
		const diff = feeDiffs[period];
		setData(data => ({
			freq: period,
			paid_until: data.paid_until?.plus(diff) ?? DateTime.now().plus(diff),
		}));
	}

	return (
		<Dialog
			title={isBulk ? `Mark fees as paid for ${singerIds.length} singers` : 'Mark fees as paid'}
			okLabel={isBulk ? 'Renew Fees' : 'Save'}
			onOk={submit}
			okColour="primary"
			okMethod="delete"
			isOpen={isOpen}
			setIsOpen={setIsOpen}
		>
			<p className="mb-2">
				{isBulk
					? "Each singer's fees will be renewed from their current expiry date, or today if no expiry is set."
					: "To renew a singer's fees, update their expiry date below."}
			</p>

			<Form onSubmit={submit}>
				<div className="sm:col-span-6">
					<RadioGroup
						label="Renew for"
						selected={data.freq}
						setSelected={value => renewFor(value)}
						options={[
							{ id: 'month', name: 'Month' },
							{ id: 'quarter', name: 'Quarter' },
							{ id: 'halfyear', name: 'Half-year' },
							{ id: 'year', name: 'Year' },
						]}
					/>
					<Help>Your fee frequency will be saved for next time.</Help>
					{errors.freq && <Error>{errors.freq}</Error>}

					{!isBulk && (
						<>
							<hr className="mt-6 mb-4" />

							<Label label="Membership expires" forInput="paid_until" />
							<DateTextInput
								name="paid_until"
								hasErrors={!!errors.paid_until}
								value={data.paid_until?.toISODate() ?? ''}
								updateFn={value => setData('paid_until', DateTime.fromISO(value) ?? null)}
							/>
							<Help>Manually adjust the expiry date here.</Help>
							{errors.paid_until && <Error>{errors.paid_until}</Error>}
						</>
					)}
				</div>
			</Form>
		</Dialog>
	);
};

export default RenewFeesDialog;