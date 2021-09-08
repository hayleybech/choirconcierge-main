<?php

namespace App\Imports;

use App\Models\SingerCategory;
use App\Models\User;
use App\Models\VoicePart;
use DateTime;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class HarmonysiteSingersImport implements OnEachRow, WithHeadingRow
{
	public function onRow(Row $row): void
	{
		$rowArr = array_map(static function ($item) {
			if (is_string($item)) {
				return trim($item);
			}
			return $item;
		}, $row->toArray());

		$user = User::firstWhere('email', $rowArr['email_address']);
		if ($user) {
		    return;
		}

        $user = User::create([
            'email' => $rowArr['email_address'],
            'first_name' => $rowArr['first_name'],
            'last_name' => $rowArr['surname'],
            'password' => random_int(0, 100000),
            'dob' => DateTime::createFromFormat('d/m/Y', $rowArr['date_of_birth'] ?? null),
            'phone' => $rowArr['mobile_phone'],
            'address_street_1' => $rowArr['street_address'],
            'address_street_2' => $rowArr['street_address_line_2'],
            'address_suburb' => $rowArr['townsuburb'],
            'address_state' => $rowArr['state'],
            'address_postcode' => $rowArr['postcode'],
            'height' =>  $this->make_valid_height($rowArr['height']),
            'ice_name' => $rowArr['emergency_contact'],
            'profession' => $rowArr['occupation'],
        ]);

        $singer = $user->singers()->updateOrCreate(['user_id' => $user->id], [
            'onboarding_enabled' => false,
            'voice_part_id' => VoicePart::where('title', $this->convert_voice_part($rowArr['section']))->first()->id ?? null,
            'joined_at' => $this->make_valid_mysql_datetime($rowArr['registration_date']),
        ]);

		// Add SingerCategory
		if (explode(' ', $rowArr['status'])[0] === 'Active') {
			$category = SingerCategory::where('name', 'Members')->first();
		} else {
			$category = SingerCategory::where('name', 'Archived Members')->first();
		}
		$singer->category()->associate($category);

		$singer->save();
	}

	private function make_valid_mysql_datetime(?string $datetime_raw): string
    {
        $datetime_carbon = new Carbon(DateTime::createFromFormat('d/m/Y', $datetime_raw ?? null));

        $datetime_carbon = ($datetime_carbon->year >= 1970) ? $datetime_carbon : Carbon::now();

        $datetime_carbon->setTime(0, 0, 0);

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

    private function convert_voice_part(string $part_name): string
    {
        return explode(' ', $part_name)[0];
    }
}
