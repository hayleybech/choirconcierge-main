<?php

namespace App\Imports;

use App\Models\Role;
use App\Models\SingerCategory;
use App\Models\User;
use App\Models\VoicePart;
use DateTime;
use Illuminate\Support\Carbon;
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

		$user = User::firstWhere('email', $rowArr['email']);
		if ($user) {
		    return;
		}

        $user = User::create([
            'email' => $rowArr['email'],
            'first_name' => $rowArr['first_name'],
            'last_name' => $rowArr['last_name'],
            'password' => random_int(0, 100000),
            'dob' => date_create($rowArr['birthday'] ?? null),
            'phone' => $rowArr['mobile_phone'],
            'address_street_1' => $rowArr['street'],
            'address_street_2' => $rowArr['additional'],
            'address_suburb' => $rowArr['city'],
            'address_state' => $rowArr['province'],
            'address_postcode' => $rowArr['postal_code'],
            'skills' => $rowArr['skills'],
            'height' =>  $this->make_valid_height($rowArr['height']),
        ]);

        $singer = $user->singers()->updateOrCreate(['user_id' => $user->id], [
            'onboarding_enabled' => false,
            'voice_part_id' => VoicePart::where('title', $rowArr['voice_part'])->first()->id ?? null,
            'joined_at' => $this->make_valid_mysql_datetime($rowArr['member_since']),
            'membership_details' => $rowArr['member_id'],
        ]);

		// Add Roles
		$roles_list = explode(',', $rowArr['roles']);

		$roles_to_add = [];
        if (in_array('Site Admin', $roles_list, true)) {
            $roles_to_add[] = Role::where('name', 'Admin')->first()->id;
        }
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

	private function make_valid_mysql_datetime(?string $datetime_raw): string
    {
        $datetime_carbon = new Carbon(new DateTime($datetime_raw ?? null));

        $datetime_carbon = ($datetime_carbon->year >= 1970) ? $datetime_carbon : Carbon::now();

        return $datetime_carbon->toDateTimeString();
    }

    /**
     * Assumes heights greater than 1000 are in mm
     */
    private function make_valid_height(?string $height_raw): float
    {
        $height_float = (float) preg_replace("/[^0-9.]/", "", $height_raw);

        if($height_float > 10_000) {
            return 0;
        }

        return ($height_float < 1000) ? $height_float : $height_float  / 10;
    }
}
