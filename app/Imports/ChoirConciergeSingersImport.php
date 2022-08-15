<?php

namespace App\Imports;

use App\Models\Role;
use App\Models\SingerCategory;
use App\Models\User;
use App\Models\VoicePart;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class ChoirConciergeSingersImport implements OnEachRow, WithHeadingRow
{
    private SingerCategory $activeCategory;

    private SingerCategory $archivedCategory;

    /** @var Collection<Role> */
    private Collection $roles;

    public function __construct()
    {
        $this->activeCategory = SingerCategory::firstWhere('name', 'Members');
        $this->archivedCategory = SingerCategory::firstWhere('name', 'Archived Members');

        $this->roles = Role::all();
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
            ['email' => $rowArr['email']],
            [
                'first_name' => $rowArr['first_name'],
                'last_name' => $rowArr['last_name'],
                'pronouns' => $rowArr['pronouns'] ?? '',
                'password' => $rowArr['password'] ?? random_int(0, 100000),
                'dob' => isset($rowArr['dob']) && trim($rowArr['dob']) ? date_create($rowArr['dob']) : null,
                'phone' => $rowArr['phone'] ?? '',
                'ice_name' => $rowArr['ice_name'] ?? '',
                'ice_phone' => $rowArr['ice_phone'] ?? '',
                'address_street_1' => $rowArr['address_street_1'] ?? '',
                'address_street_2' => $rowArr['address_street_2'] ?? '',
                'address_suburb' => $rowArr['address_suburb'] ?? '',
                'address_state' => $rowArr['address_state'] ?? '',
                'address_postcode' => $rowArr['address_postcode'] ?? '',
                'profession' => $rowArr['profession'] ?? '',
                'skills' => $rowArr['skills'] ?? '',
                'height' =>  isset($rowArr['height']) ? $this->make_valid_height($rowArr['height'] ?? '') : '',
                'bha_id' => $rowArr['bha_id'] ?? '',
            ]
        );

        $singer = $user->singers()->updateOrCreate(['id' => $user->id], [
            'onboarding_enabled' => false,
            'reason_for_joining' => $rowArr['reason_for_joining'] ?? '',
            'referrer' => $rowArr['referrer'] ?? '',
            'membership_details' => $rowArr['membership_details'] ?? '',
            'voice_part_id' => isset($rowArr['voice_part']) ? VoicePart::firstWhere('title', $rowArr['voice_part'])->id : null,
            'joined_at' => $this->make_valid_mysql_datetime($rowArr['joined_at'] ?? null),
            'paid_until' => $this->make_valid_mysql_datetime($rowArr['paid_until'] ?? null),
        ]);

        // Add Roles
        $roles_list = isset($rowArr['roles']) ? explode(',', $rowArr['roles']) : [];

        $roles_to_add = [$this->roles->firstWhere('name', 'User')->id];
        if (in_array('Admin', $roles_list, true)) {
            $roles_to_add[] = $this->roles->firstWhere('name', 'Admin')->id;
        }
        if (in_array('Music Team', $roles_list, true)) {
            $roles_to_add[] = $this->roles->firstWhere('name', 'Music Team')->id;
        }
        if (in_array('Membership Team', $roles_list, true)) {
            $roles_to_add[] = $this->roles->firstWhere('name', 'Membership Team')->id;
        }
        if (in_array('Events Team', $roles_list, true)) {
            $roles_to_add[] = $this->roles->firstWhere('name', 'Events Team')->id;
        }
        $singer->roles()->syncWithoutDetaching($roles_to_add);

        // Add SingerCategory
        if (in_array('Archived Members', $roles_list, true)) {
            $category = $this->archivedCategory;
        } else {
            $category = $this->activeCategory;
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
        $height_float = (float) preg_replace('/[^0-9.]/', '', $height_raw);

        if ($height_float > 10_000) {
            return 0;
        }

        return ($height_float < 1000) ? $height_float : $height_float / 10;
    }
}
