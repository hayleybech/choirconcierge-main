<?php

namespace App\Imports;

use App\Models\Role;
use App\Models\Singer;
use App\Models\SingerCategory;
use App\Models\VoicePart;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class SingersImport implements OnEachRow, WithHeadingRow
{
	/*
	 * Groupanizer format import
	 */
	public function onRow(Row $row): void
	{
		$rowArr = array_map(static function ($item) {
			if (is_string($item)) {
				return trim($item);
			}
			return $item;
		}, $row->toArray());

		// Create Singer
		$new_singer = false;
		$singer = Singer::firstWhere('email', $rowArr['email']);
		if ($singer) {
			$singer->update([
				'first_name' => $rowArr['first_name'],
				'last_name' => $rowArr['last_name'],
				'onboarding_enabled' => false,
				'voice_part_id' => VoicePart::where('title', $rowArr['voice_part'])->first()->id ?? null,
				'joined_at' => date_create($rowArr['member_since'] ?? null),
			]);
		} else {
			$new_singer = true;
			$singer = Singer::create([
				'email' => $rowArr['email'],
				'first_name' => $rowArr['first_name'],
				'last_name' => $rowArr['last_name'],
				'onboarding_enabled' => false,
				'voice_part_id' => VoicePart::where('title', $rowArr['voice_part'])->first()->id ?? null,
				'joined_at' => date_create($rowArr['member_since'] ?? null),
				'password' => random_int(0, 100000),
			]);
		}

		// Add Profile
		$singer->profile()->updateOrCreate(
			['singer_id' => $singer->id],
			[
				'dob' => date_create($rowArr['birthday'] ?? null),
				'phone' => $rowArr['mobile_phone'],
				'address_street_1' => $rowArr['street'],
				'address_street_2' => $rowArr['additional'],
				'address_suburb' => $rowArr['city'],
				'address_state' => $rowArr['province'],
				'address_postcode' => $rowArr['postal_code'],
				'skills' => $rowArr['skills'],
				'height' => $rowArr['height'],
				'membership_details' => $rowArr['member_id'],
			],
		);

		// Add Roles
		$roles_list = explode(',', $rowArr['roles']);

		$roles_to_add = [];
		if (in_array('Music Team', $roles_list, true)) {
			$roles_to_add[] = Role::where('name', 'Music Team')->first()->id;
		}
		if (in_array('User Admin', $roles_list, true)) {
			$roles_to_add[] = Role::where('name', 'Membership Team')->first()->id;
		}
		if (in_array('Event Admin', $roles_list, true)) {
			$roles_to_add[] = Role::where('name', 'Events Team')->first()->id;
		}
		$singer->roles()->syncWithoutDetaching($roles_to_add);

		// Add SingerCategory
		if (in_array('Inactive Member', $roles_list, true)) {
			$category = SingerCategory::where('name', 'Archived Members')->first();
		} else {
			$category = SingerCategory::where('name', 'Members')->first();
		}
		$singer->category()->associate($category);

		$singer->save();
	}
}
