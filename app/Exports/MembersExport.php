<?php

namespace App\Exports;

use App\Models\Enrolment;
use App\Models\Membership;
use App\Models\Role;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MembersExport implements FromQuery, WithMapping, WithHeadings
{
	use Exportable;

	public function query()
	{
		return Membership::query()
			->with([
				'user',
				'enrolments' => ['ensemble', 'voice_part'],
				'roles',
				'category',
			]);
	}

	public function map($membership): array
	{
		return [
			$membership->user_id,
			$membership->id,

			// User
			$membership->user->first_name,
			$membership->user->last_name,
			$membership->user->pronouns,
			$membership->user->email,
			$membership->user->dob,
			$membership->user->phone,
			$membership->user->ice_name,
			$membership->user->ice_phone,
			$membership->user->address_street_1,
			$membership->user->address_street_2,
			$membership->user->address_suburb,
			$membership->user->address_state,
			$membership->user->address_postcode,
			$membership->user->profession,
			$membership->user->skills,
			$membership->user->height,
			$membership->user->bha_id,
			$membership->user->created_at,
			$membership->user->updated_at,
			$membership->user->last_login,

			// Enrolments
			$membership
				->enrolments
				->map(fn(Enrolment $enrolment) => $enrolment->ensemble->name . ' - ' . $enrolment->voice_part->title)
				->join(', '),

			// Roles
			$membership
				->roles
				->map(fn(Role $role) => $role->name)
				->join(', '),

			// Status
			$membership->category->name,

			// Membership
			$membership->onboarding_enabled ? 'Yes' : 'No',
			$membership->reason_for_joining,
			$membership->referrer,
			$membership->membership_details,
			$membership->created_at,
			$membership->updated_at,
			$membership->joined_at,
			$membership->paid_until,
			$membership->fee_status,
		];
	}

	public function headings(): array
	{
		return [
			'User ID',
			'Member ID',

			// User
			'First Name',
			'Last Name',
			'Pronouns',
			'Email',
			'Date of Birth',
			'Phone',
			'Emergency Name',
			'Emergency Phone',
			'Address Street 1',
			'Address Street 2',
			'Address Suburb',
			'Address State',
			'Address Postcode',
			'Profession',
			'Skills',
			'Height',
			'Society ID',
			'User Created At',
			'User Updated At',
			'Last Login',

			// Enrolments
			'Ensemble - Voice Part',

			// Roles
			'Roles',

			// Status
			'Membership Status',

			// Membership
			'Onboarding Enabled',
			'Reason for Joining',
			'Referrer',
			'Notes',
			'Membership Created At',
			'Membership Updated At',
			'Joined At',
			'Paid Until',
			'Fee Status',
		];
	}
}
