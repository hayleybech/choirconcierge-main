<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class ImportSingerTemplateController extends Controller
{
    public function __invoke(): Response
    {
        // ChoirConcierge import expected headers
        $headers = [
            'email',
            'first_name',
            'last_name',
            'pronouns',
            'password',
            'dob',
            'phone',
            'ice_name',
            'ice_phone',
            'address_street_1',
            'address_street_2',
            'address_suburb',
            'address_state',
            'address_postcode',
            'profession',
            'skills',
            'height',
            'bha_id',
            'dietary_requirements',
            'medical_conditions',
            'reason_for_joining',
            'referrer',
            'membership_details',
            'joined_at',
            'paid_until',
            'voice_part',
            'roles',
        ];

        $csv = implode(',', $headers) . "\r\n"; // header row only

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="choirconcierge-singers-template.csv"',
        ]);
    }
}
