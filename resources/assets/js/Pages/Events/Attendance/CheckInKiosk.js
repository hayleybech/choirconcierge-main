import React from 'react';
import AppHead from '../../../components/AppHead';
import useRoute from '../../../hooks/useRoute';
import SingerSelect from '../../../components/inputs/SingerSelect';
import Label from '../../../components/inputs/Label';
import { useForm, usePage } from '@inertiajs/react';
import Form from '../../../components/Form';
import Button from '../../../components/inputs/Button';
import ToastFlash from '../../../components/ToastFlash';
import Icon from '../../../components/Icon';
import Error from '../../../components/inputs/Error';
import { DateTime } from 'luxon';

const CheckInKiosk = ({ event, individualCheckInUrl }) => {
	const { route } = useRoute();
	const { tenant, flash } = usePage().props;

	const { data, setData, post, processing, errors } = useForm({
		user: '',
	});

	function submit(e) {
		e.preventDefault();
		post(route('events.kiosk-check-ins.store', { event }));
	}

	return (
		<>
			<AppHead title={`Check-In Page - ${event.title}`} />
			<ToastFlash errors={errors} flash={flash} />

			<div className="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-100">
				<div className="sm:mx-auto sm:w-full sm:max-w-md">
					{tenant ? (
						<img src={tenant.logo_url} alt={tenant.name} className="h-16 w-auto mx-auto" />
					) : (
						<img src="/img/vibrant/logo-dark.svg" alt="Choir Concierge" className="h-12 w-auto mx-auto" />
					)}

					<h2 className="mt-8 text-center text-4xl font-extrabold text-gray-900">Event Check-In</h2>

					<p className="text-center mt-6">Checking in for:</p>

					<h3 className="mt-2 text-center text-2xl font-extrabold text-gray-900">{event.title}</h3>

					<div className="mt-2 font-bold flex items-center justify-center text-gray-600">
						<Icon icon="calendar-day" type="regular" mr />
						{DateTime.fromISO(event.start_date).hasSame(DateTime.fromISO(event.end_date), 'day') ? (
							<span className="whitespace-nowrap">
								{DateTime.fromISO(event.start_date).toLocaleString(DateTime.DATE_MED)}
							</span>
						) : (
							<div>
								<span className="whitespace-nowrap">
									{DateTime.fromISO(event.start_date).toLocaleString(DateTime.DATE_MED)}
								</span>
								{' - '}
								<span className="whitespace-nowrap">
									{DateTime.fromISO(event.end_date).toLocaleString(DateTime.DATE_MED)}
								</span>
							</div>
						)}
					</div>
				</div>

				<div className="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
					<div className="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
						<Form onSubmit={submit}>
							<div>
								<Label label="Singer" />
								<SingerSelect updateFn={value => setData('user', value)} hasErrors={!!errors['user']} />
								{errors.user && <Error>{errors.user}</Error>}
							</div>

							<Button
								variant="primary"
								type="submit"
								size="sm"
								className="space-x-2 w-full"
								disabled={processing}
							>
								<Icon icon="check" />
								I'm Here
							</Button>
						</Form>

						{individualCheckInUrl}
					</div>
				</div>

				<img src="/img/vibrant/logo-dark.svg" alt="Choir Concierge" className="h-8 w-auto mx-auto mt-12" />
			</div>
		</>
	);
};

export default CheckInKiosk;
