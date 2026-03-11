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
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;

class GroupanizerSingersImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsEmptyRows
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
                'password' => random_int(0, 100000),
                'dob' => isset($rowArr['birthday']) && trim($rowArr['birthday']) ? date_create($rowArr['birthday']) : null,
                'phone' => $rowArr['mobile_phone'] ?? '',
                'address_street_1' => $rowArr['street'] ?? '',
                'address_street_2' => $rowArr['additional'] ?? '',
                'address_suburb' => $rowArr['city'] ?? '',
                'address_state' => $rowArr['province'] ?? '',
                'address_postcode' => $rowArr['postal_code'] ?? '',
                'skills' => $rowArr['skills'] ?? '',
                'height' =>  isset($rowArr['height']) ? $this->make_valid_height($rowArr['height'] ?? null) : null,
            ]
        );

        if(! $user) {
            return;
        }

        $member = $user->memberships()->updateOrCreate(['user_id' => $user->id], [
            'onboarding_enabled' => false,
            'joined_at' => $this->make_valid_mysql_datetime($rowArr['member_since'] ?? null),
            'membership_details' => $rowArr['member_id'] ?? '',
        ]);

        // Add an enrolment to the ensembles
        $this->handleEnrolments($member, $rowArr);

        // Add Roles
        $roles_list = isset($rowArr['roles']) ? explode(',', $rowArr['roles']) : [];

        $roles_to_add = [$this->roles->firstWhere('name', 'User')->id];
        if (in_array('Site Admin', $roles_list, true)) {
            $roles_to_add[] = $this->roles->firstWhere('name', 'Admin')->id;
        }
        if (in_array('Music Team', $roles_list, true)) {
            $roles_to_add[] = $this->roles->firstWhere('name', 'Music Team')->id;
        }
        if (in_array('User Admin', $roles_list, true)) {
            $roles_to_add[] = $this->roles->firstWhere('name', 'Membership Team')->id;
        }
        if (in_array('Event Admin', $roles_list, true)) {
            $roles_to_add[] = $this->roles->firstWhere('name', 'Events Team')->id;
        }
        $member->roles()->syncWithoutDetaching($roles_to_add);

        // Add SingerCategory
        if (in_array('Inactive Member', $roles_list, true)) {
            $category = $this->archivedCategory;
        } else {
            $category = $this->activeCategory;
        }
        $member->category()->associate($category);

        $member->save();
    }

    private function handleEnrolments($member, array $rowArr): void
    {
        $voicePartRaw = $rowArr['voice_part'] ?? '';

        if (empty($voicePartRaw)) {
            $ensemble = tenant()->ensembles?->first();
            if ($ensemble) {
                $member->enrolments()->updateOrCreate([
                    'ensemble_id' => $ensemble->id,
                ], [
                    'voice_part_id' => null,
                ]);
            }

            return;
        }

        // Support for "Ensemble 1 - Alto;Ensemble 2 - Soprano"
        if (str_contains($voicePartRaw, ';') || str_contains($voicePartRaw, ' - ')) {
            $parts = explode(';', $voicePartRaw);
            foreach ($parts as $part) {
                $part = trim($part);
                if (str_contains($part, ' - ')) {
                    [$ensembleName, $voicePartTitle] = explode(' - ', $part, 2);
                    $ensemble = tenant()->ensembles()->where('name', trim($ensembleName))->first();
                    $voicePart = VoicePart::firstWhere('title', trim($voicePartTitle));
                } else {
                    $ensemble = tenant()->ensembles?->first();
                    $voicePart = VoicePart::firstWhere('title', $part);
                }

                if ($ensemble) {
                    $member->enrolments()->updateOrCreate([
                        'ensemble_id' => $ensemble->id,
                    ], [
                        'voice_part_id' => $voicePart?->id,
                    ]);
                }
            }
        } else {
            // Default behavior: just a voice part name
            $ensemble = tenant()->ensembles?->first();
            if ($ensemble) {
                $voicePart = VoicePart::firstWhere('title', $voicePartRaw);
                $member->enrolments()->updateOrCreate([
                    'ensemble_id' => $ensemble->id,
                ], [
                    'voice_part_id' => $voicePart?->id,
                ]);
            }
        }
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

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'first_name' => 'required|max:127',
            'last_name' => 'required|max:127',
        ];
    }
}
