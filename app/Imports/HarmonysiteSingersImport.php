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

class HarmonysiteSingersImport implements OnEachRow, WithHeadingRow
{
    private SingerCategory $activeCategory;

    private SingerCategory $archivedCategory;

    private Role $userRole;

    public function __construct()
    {
        $this->activeCategory = SingerCategory::firstWhere('name', 'Members');
        $this->archivedCategory = SingerCategory::firstWhere('name', 'Archived Members');

        $this->userRole = Role::firstWhere('name', 'User');
    }

    public function onRow(Row $row): void
    {
        $rowArr = array_map(static function ($item) {
            if (is_string($item)) {
                return trim($item);
            }

            return $item;
        }, $row->toArray());

        $user = User::firstOrCreate(
            ['email' => $rowArr['email_address']],
            [
                'first_name' => $rowArr['first_name'],
                'last_name' => $rowArr['surname'],
                'password' => random_int(0, 100000),
                'dob' => isset($rowArr['date_of_birth']) ? DateTime::createFromFormat('d/m/Y', $rowArr['date_of_birth']) : null,
                'phone' => $rowArr['mobile_phone'] ?? '',
                'address_street_1' => $rowArr['street_address'] ?? '',
                'address_street_2' => $rowArr['street_address_line_2'] ?? '',
                'address_suburb' => $rowArr['townsuburb'] ?? '',
                'address_state' => $rowArr['state'] ?? '',
                'address_postcode' => $rowArr['postcode'] ?? '',
                'height' =>  isset($rowArr['height']) ? $this->make_valid_height($rowArr['height'] ?? null) : null,
                'ice_name' => $rowArr['emergency_contact'] ?? '',
                'profession' => $rowArr['occupation'] ?? '',
            ]
        );

        if(! $user) {
            return;
        }

        $member = $user->memberships()->updateOrCreate(['user_id' => $user->id], [
            'onboarding_enabled' => false,
            'joined_at' => $this->make_valid_mysql_datetime($rowArr['registration_date'] ?? null),
        ]);

        // Add an enrolment to the first ensemble
        // @todo add support for specifying which ensemble
        $ensemble = tenant()->ensembles?->first();
        if($ensemble) {
            $member->enrolments()->updateOrCreate([
                'membership_id' => $member->id,
                'ensemble_id'   => $ensemble->id,
                'voice_part_id' => isset($rowArr['voice_part']) ? VoicePart::firstWhere('title', $rowArr['voice_part'])->id : null,
            ]);
        }

        // Add SingerCategory
        if (explode(' ', $rowArr['status'])[0] === 'Active') {
            $category = $this->activeCategory;
        } else {
            $category = $this->archivedCategory;
        }
        $member->category()->associate($category);

        // Add User Role
        $member->roles()->attach($this->userRole);

        $member->save();
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
        $height_float = (float) preg_replace('/[^0-9.]/', '', $height_raw);

        if ($height_float > 10_000) {
            return 0;
        }

        return ($height_float < 1000) ? $height_float : $height_float / 10;
    }

    private function convert_voice_part(string $part_name): string
    {
        return explode(' ', $part_name)[0];
    }
}
