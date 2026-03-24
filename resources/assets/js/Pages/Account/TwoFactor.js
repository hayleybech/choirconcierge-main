import React from 'react';
import PageHeader from '../../components/PageHeader/PageHeader';
import AppHead from '../../components/AppHead';
import { useForm, router } from '@inertiajs/react';
import useRoute from '../../hooks/useRoute';
import FormWrapper from '../../components/FormWrapper';
import FormSection from '../../components/FormSection';
import Label from '../../components/inputs/Label';
import TextInput from '../../components/inputs/TextInput';
import Button from '../../components/inputs/Button';
import Error from '../../components/inputs/Error';
import Help from '../../components/inputs/Help';
import CentralLayout from '../../Layouts/CentralLayout';
import classNames from '../../classNames';
import DateTag from '../../components/DateTag';

const TwoFactor = ({ enabled, qr_code, recovery_codes }) => {
	const { route } = useRoute();

	const { data, setData, post, processing, errors, reset } = useForm({
		code: '',
	});

	const enableTwoFactor = (e) => {
		e.preventDefault();
		post(route('central.account.two-factor.store'), {
			preserveScroll: true,
			onSuccess: () => reset('code'),
		});
	};

	const disableTwoFactor = () => {
		if (confirm('Are you sure you want to disable two-factor authentication?')) {
			router.delete(route('central.account.two-factor.destroy'), {
				preserveScroll: true,
			});
		}
	};

	const regenerateRecoveryCodes = () => {
		if (confirm('Are you sure you want to regenerate recovery codes? Your old codes will no longer work.')) {
			router.post(route('central.account.two-factor.regenerate'), {}, {
				preserveScroll: true,
			});
		}
	};

	return (
		<>
			<AppHead title="Two-Factor Authentication" />
			<PageHeader
				title="Two-Factor Authentication"
				icon="shield-alt"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('central.dash') },
					{ name: 'Edit Profile', url: route('central.account.edit') },
					{ name: 'Two-Factor Authentication', url: route('central.account.two-factor.show') },
				]}
			/>

			<FormWrapper>
				{!enabled && (
					<form onSubmit={enableTwoFactor}>
						<FormSection
							title="Enable Two-Factor Authentication"
							description="Two-factor authentication adds an extra layer of security to your account by requiring more than just a password to log in."
						>
							<div className="sm:col-span-6">
								<p className="text-sm text-gray-600 mb-4">
									To enable two-factor authentication, scan the following QR code using your phone's authenticator application (e.g. Google Authenticator, Authy).
								</p>
								
								<div className="mb-4 p-4 bg-white inline-block rounded-lg shadow-sm border border-gray-200" dangerouslySetInnerHTML={{ __html: qr_code }} />
								
								<div className="mb-4">
									<Label label="Authentication Code" forInput="code" />
									<TextInput
										name="code"
										value={data.code}
										updateFn={value => setData('code', value)}
										hasErrors={!!errors.code}
										placeholder="123456"
										className="max-w-xs"
									/>
									{errors.code && <Error>{errors.code}</Error>}
									<Help>Enter the 6-digit code from your authenticator app to confirm.</Help>
								</div>

								<Button variant="primary" type="submit" disabled={processing}>
									Confirm and Enable
								</Button>
							</div>
						</FormSection>
					</form>
				)}

				{enabled && (
					<div className="space-y-6">
						<FormSection
							title="Two-Factor Authentication Enabled"
							description="You have enabled two-factor authentication. Your account is more secure."
						>
							<div className="sm:col-span-6">
								<p className="text-sm text-gray-600 mb-4">
									Two-factor authentication is currently active. If you lose your phone, you can use one of the recovery codes below to log in.
								</p>
								
								<div className="grid grid-cols-2 gap-2 mb-4 p-4 bg-gray-50 rounded border border-gray-200 font-mono text-sm">
									{recovery_codes.map(({code, used_at}, index) => (
										<div key={index} className="flex gap-1">
											<pre className={classNames(!!used_at && 'line-through')}>{code}</pre>
											{used_at && <DateTag date={used_at} format="DATETIME_SHORT" className="text-gray-400 text-xs" label="Used at" />}
										</div>
									))}
								</div>

								<div className="flex space-x-3">
									<Button variant="danger-solid" onClick={regenerateRecoveryCodes}>
										Regenerate Recovery Codes
									</Button>
									<Button variant="danger-outline" onClick={disableTwoFactor}>
										Disable Two-Factor Authentication
									</Button>
								</div>
							</div>
						</FormSection>
					</div>
				)}
			</FormWrapper>
		</>
	);
};

TwoFactor.layout = page => <CentralLayout children={page} />;

export default TwoFactor;
