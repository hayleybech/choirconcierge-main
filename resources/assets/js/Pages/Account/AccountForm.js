import React from 'react';
import { useForm, usePage } from '@inertiajs/react';
import FormSection from '../../components/FormSection';
import Label from '../../components/inputs/Label';
import TextInput from '../../components/inputs/TextInput';
import Error from '../../components/inputs/Error';
import ButtonLink from '../../components/inputs/ButtonLink';
import Button from '../../components/inputs/Button';
import AvatarUpload from '../../components/AvatarUpload';
import Help from '../../components/inputs/Help';
import Select from '../../components/inputs/Select';
import Form from '../../components/Form';
import FormFooter from '../../components/FormFooter';
import DayInput from '../../components/inputs/Day';
import { DateTime } from 'luxon';
import FormWrapper from '../../components/FormWrapper';
import useRoute from '../../hooks/useRoute';
import MetricImperialInput from '../../components/inputs/MetricImperialInput';
import CountrySelect from "../../components/inputs/CountrySelect";
import StateSelect from "../../components/inputs/StateSelect";

const AccountForm = ({}) => {
	const { route } = useRoute();
	const { user } = usePage().props;

	const { data, setData, post, processing, errors } = useForm({
		first_name: user.first_name,
		last_name: user.last_name,
		avatar: null,
		email: user.email,
		phone: user.phone ?? '',
		password: '',
		pronouns: user.pronouns ?? '',

		password_confirmation: '',
		dob: user.dob ? DateTime.fromJSDate(new Date(user.dob)).toISODate() : '',
		height: user.height ?? '',
		profession: user.profession ?? '',
		skills: user.skills ?? '',
		dietary_requirements: user.dietary_requirements ?? '',
		medical_conditions: user.medical_conditions ?? '',

		bha_id: user.bha_id ?? '',
		ice_name: user.ice_name ?? '',
		ice_phone: user.ice_phone ?? '',

		address_street_1: user.address_street_1 ?? '',
		address_street_2: user.address_street_2 ?? '',
		address_suburb: user.address_suburb ?? '',
		address_state: user.address_state ?? '',
		address_country: user.address_country ?? '',
		address_postcode: user.address_postcode ?? '',
	});

	function submit(e) {
		e.preventDefault();
		post(route('accounts.update'));
	}

	return (
		<FormWrapper>
			<Form onSubmit={submit}>
				<FormSection title="User Details">
					<div className="sm:col-span-6">
						<Label label="Profile Picture" forInput="avatar" />
						<AvatarUpload
							name="avatar"
							currentImage={data.avatar ? URL.createObjectURL(data.avatar) : user.avatar_url}
							updateFn={value => setData('avatar', value)}
						/>
					</div>

					<div className="sm:col-span-2">
						<Label label="First Name" forInput="first_name" />
						<TextInput
							name="first_name"
							value={data.first_name}
							updateFn={value => setData('first_name', value)}
							hasErrors={!!errors['first_name']}
							autoComplete="given-name"
						/>
						{errors.first_name && <Error>{errors.first_name}</Error>}
					</div>
					<div className="sm:col-span-2">
						<Label label="Last Name" forInput="last_name" />
						<TextInput
							name="last_name"
							value={data.last_name}
							updateFn={value => setData('last_name', value)}
							hasErrors={!!errors['last_name']}
							autoComplete="family-name"
						/>
						{errors.last_name && <Error>{errors.last_name}</Error>}
					</div>
					<div className="sm:col-span-2">
						<Label label="Pronouns" forInput="pronouns" />
						<TextInput
							name="pronouns"
							value={data.pronouns}
							placeholder="she/they/he"
							updateFn={value => setData('pronouns', value)}
							hasErrors={!!errors['pronouns']}
						/>
						{errors.pronouns && <Error>{errors.pronouns}</Error>}
					</div>

					<div className="sm:col-span-3">
						<Label label="Email Address" forInput="email" />
						<TextInput
							type="email"
							name="email"
							value={data.email}
							updateFn={value => setData('email', value)}
							hasErrors={!!errors['email']}
							autoComplete="email"
						/>
						{errors.email && <Error>{errors.email}</Error>}
					</div>
					<div className="sm:col-span-3">
						<Label label="Phone" forInput="phone" />
						<TextInput
							type="tel"
							name="phone"
							value={data.phone}
							updateFn={value => setData('phone', value)}
							hasErrors={!!errors['phone']}
							autoComplete="tel"
						/>
						{errors.phone && <Error>{errors.phone}</Error>}
					</div>

					<div className="sm:col-span-3">
						<Label label="Change Password" forInput="password" />
						<TextInput
							type="password"
							name="password"
							value={data.password}
							updateFn={value => setData('password', value)}
							hasErrors={!!errors['password']}
							autoComplete="new-password"
						/>
						{errors.password && <Error>{errors.password}</Error>}
					</div>
					<div className="sm:col-span-3">
						<Label label="Confirm New Password" forInput="password_confirmation" />
						<TextInput
							type="password"
							name="password_confirmation"
							value={data.password_confirmation}
							updateFn={value => setData('password_confirmation', value)}
							hasErrors={!!errors['password_confirmation']}
							autoComplete="new-password"
						/>
						{errors.password_confirmation && <Error>{errors.password_confirmation}</Error>}
					</div>
				</FormSection>

				<FormSection title="Profile Details">
					<div className="sm:col-span-3 lg:col-span-2">
						<Label label="Date of Birth" forInput="dob" />
						<DayInput
							name="dob"
							hasErrors={!!errors.dob}
							value={data.dob}
							updateFn={value => setData('dob', value)}
							max={DateTime.now().toISODate()}
							autoComplete="bday"
						/>
						{errors.dob && <Error>{errors.dob}</Error>}
					</div>

					<div className="sm:col-span-3 lg:col-span-2">
						<Label label="Height" forInput="height" />
						<MetricImperialInput
							name="height"
							value={data.height}
							updateFn={value => setData('height', value)}
							hasErrors={!!errors['height']}
						/>
						<Help>Knowing the singer's height is useful for riser stacks.</Help>
						{errors.height && <Error>{errors.height}</Error>}
					</div>
					<div className="sm:col-span-3 lg:col-span-2">
						<Label label="BHA Member ID (e.g. 1234)" forInput="bha_id" />
						<TextInput
							name="bha_id"
							value={data.bha_id}
							updateFn={value => setData('bha_id', value)}
							hasErrors={!!errors['bha_id']}
						/>
						{errors.bha_id && <Error>{errors.bha_id}</Error>}
					</div>

					<div className="sm:col-span-3">
						<Label label="What is your profession?" forInput="profession" />
						<TextInput
							name="profession"
							value={data.profession}
							updateFn={value => setData('profession', value)}
							hasErrors={!!errors['profession']}
						/>
						{errors.profession && <Error>{errors.profession}</Error>}
					</div>
					<div className="sm:col-span-3">
						<Label label="What non-musical skills do you have?" forInput="skills" />
						<TextInput
							name="skills"
							value={data.skills}
							updateFn={value => setData('skills', value)}
							hasErrors={!!errors['skills']}
						/>
						{errors.skills && <Error>{errors.skills}</Error>}
					</div>

					<div className="sm:col-span-3">
						<Label label="Dietary Requirements" forInput="dietary_requirements" />
						<TextInput
							name="dietary_requirements"
							value={data.dietary_requirements}
							updateFn={value => setData('dietary_requirements', value)}
							hasErrors={!!errors['dietary_requirements']}
						/>
						{errors.dietary_requirements && <Error>{errors.dietary_requirements}</Error>}
					</div>

					<div className="sm:col-span-3">
						<Label label="Medical Conditions" forInput="medical_conditions" />
						<TextInput
							name="medical_conditions"
							value={data.medical_conditions}
							updateFn={value => setData('medical_conditions', value)}
							hasErrors={!!errors['medical_conditions']}
						/>
						{errors.medical_conditions && <Error>{errors.medical_conditions}</Error>}
					</div>
				</FormSection>

				<FormSection title="Emergency Contact">
					<div className="sm:col-span-3">
						<Label label="Emergency Contact Name" forInput="ice_name" />
						<TextInput
							name="ice_name"
							value={data.ice_name}
							updateFn={value => setData('ice_name', value)}
							hasErrors={!!errors['ice_name']}
						/>
						{errors.ice_name && <Error>{errors.ice_name}</Error>}
					</div>
					<div className="sm:col-span-3">
						<Label label="Emergency Contact Phone" forInput="ice_phone" />
						<TextInput
							type="tel"
							name="ice_phone"
							value={data.ice_phone}
							updateFn={value => setData('ice_phone', value)}
							hasErrors={!!errors['ice_phone']}
						/>
						{errors.ice_phone && <Error>{errors.ice_phone}</Error>}
					</div>
				</FormSection>

				<FormSection title="Address">
					<div className="sm:col-span-6">
						<Label label="Street Address 1" forInput="address_street_1" />
						<TextInput
							name="address_street_1"
							value={data.address_street_1}
							updateFn={value => setData('address_street_1', value)}
							hasErrors={!!errors['address_street_1']}
							autoComplete="address-line1"
						/>
						{errors.address_street_1 && <Error>{errors.address_street_1}</Error>}
					</div>
					<div className="sm:col-span-6">
						<Label label="Street Address 2" forInput="address_street_2" />
						<TextInput
							type="tel"
							name="address_street_2"
							value={data.address_street_2}
							updateFn={value => setData('address_street_2', value)}
							hasErrors={!!errors['address_street_2']}
							autoComplete="address-line2"
						/>
						{errors.address_street_2 && <Error>{errors.address_street_2}</Error>}
					</div>

					<div className="sm:col-span-2">
						<Label label="Suburb / City" forInput="address_suburb" />
						<TextInput
							name="address_suburb"
							value={data.address_suburb}
							updateFn={value => setData('address_suburb', value)}
							hasErrors={!!errors['address_suburb']}
							autoComplete="address-level2"
						/>
						{errors.address_suburb && <Error>{errors.address_suburb}</Error>}
					</div>
					<div className="sm:col-span-2">
						<Label label="Country" forInput="address_country" />
						<div className="mt-1">
							<CountrySelect
								name="address_country"
								defaultValue={data.address_country}
								updateFn={value => setData({ ...data, address_country: value, address_state: '' })}
								hasErrors={!!errors['address_country']}
								autoComplete="country"
							/>
						</div>
						{errors.address_country && <Error>{errors.address_country}</Error>}
					</div>
					<div className="sm:col-span-1">
						<Label label="State / Region" forInput="address_state" />
						{['AU', 'CA', 'US'].includes(data.address_country) ? (
							<div className="mt-1">
								<StateSelect
									country={data.address_country}
									name="address_state"
									defaultValue={data.address_state}
									updateFn={value => setData('address_state', value)}
									hasErrors={!!errors['address_state']}
									autoComplete="address-level1"
								/>
							</div>
						) : (
							<TextInput
								name="address_state"
								value={data.address_state}
								updateFn={value => setData('address_state', value)}
								hasErrors={!!errors['address_state']}
								autoComplete="address-level1"
								disabled={!data.address_country}
							/>
						)}
						{errors.address_state && <Error>{errors.address_state}</Error>}
					</div>
					<div className="sm:col-span-1">
						<Label label="Postal Code / Zip Code" forInput="address_postcode" />
						<TextInput
							name="address_postcode"
							value={data.address_postcode}
							updateFn={value => setData('address_postcode', value)}
							hasErrors={!!errors['address_postcode']}
							autoComplete="postal-code"
						/>
						{errors.address_postcode && <Error>{errors.address_postcode}</Error>}
					</div>
				</FormSection>

				<FormFooter>
					<ButtonLink href={route('singers.show', { singer: user.membership })}>Cancel</ButtonLink>
					<Button variant="primary" type="submit" className="ml-3" disabled={processing}>
						Save
					</Button>
				</FormFooter>
			</Form>
		</FormWrapper>
	);
};

export default AccountForm;
