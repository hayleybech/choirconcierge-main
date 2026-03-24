import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import AppHead from '../../components/AppHead';
import Label from '../../components/inputs/Label';
import TextInput from '../../components/inputs/TextInput';
import Button from '../../components/inputs/Button';
import Error from '../../components/inputs/Error';

const TwoFactorChallenge = () => {
	const [recovery, setRecovery] = useState(false);
	const { data, setData, post, processing, errors } = useForm({
		code: '',
		recovery_code: '',
		safe_device: false,
	});

	const submit = (e) => {
		e.preventDefault();
		post(route('auth.2fa.challenge'));
	};

	const toggleRecovery = (e) => {
		e.preventDefault();
		setRecovery(!recovery);
		setData(recovery ? { code: '', recovery_code: '' } : { code: '', recovery_code: '' });
	};

	return (
		<div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
			<AppHead title="Two-Factor Challenge" />

			<div className="sm:mx-auto sm:w-full sm:max-w-md">
				<img src="/img/vibrant/logo-dark.svg" alt="Choir Concierge" className="h-12 w-auto mx-auto" />

				<h2 className="mt-6 text-center text-3xl font-extrabold text-gray-900">Two-Factor Authentication</h2>
			</div>

			<div className="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">

				<p className="mb-4 text-sm text-gray-600">
					{recovery
						? 'Please confirm access to your account by entering one of your emergency recovery codes.'
						: 'Please confirm access to your account by entering the authentication code provided by your authenticator application.'}
				</p>

				<form onSubmit={submit}>
					{!recovery && (
						<div className="mb-4">
							<Label label="Code" forInput="code" />
							<TextInput
								name="code"
								value={data.code}
								updateFn={value => setData('code', value)}
								hasErrors={!!errors.code}
								autoFocus
								autoComplete="one-time-code"
							/>
							{errors.code && <Error>{errors.code}</Error>}
						</div>
					)}

					<div className="mb-4">
						<label className="flex items-center">
							<input
								type="checkbox"
								name="safe_device"
								checked={data.safe_device}
								onChange={e => setData('safe_device', e.target.checked)}
								className="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
							/>
							<span className="ml-2 text-sm text-gray-600">Remember this device</span>
						</label>
					</div>

					{recovery && (
						<div className="mb-4">
							<Label label="Recovery Code" forInput="recovery_code" />
							<TextInput
								name="recovery_code"
								value={data.recovery_code}
								updateFn={value => setData('recovery_code', value)}
								hasErrors={!!errors.recovery_code}
								autoFocus
							/>
							{errors.recovery_code && <Error>{errors.recovery_code}</Error>}
						</div>
					)}

					<div className="flex items-center justify-end mt-4">
						<button
							type="button"
							className="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer mr-4"
							onClick={toggleRecovery}
						>
							{recovery ? 'Use an authentication code' : 'Use a recovery code'}
						</button>

						<Button variant="primary" type="submit" size="sm" disabled={processing}>
							Log in
						</Button>
					</div>
				</form>
			</div>
		</div>
	);
};

export default TwoFactorChallenge;
