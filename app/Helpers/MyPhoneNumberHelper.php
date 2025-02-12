<?php

namespace App\Helpers;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class MyPhoneNumberHelper
{
    public static function formatSaudiPhoneNumber(string $phone): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            // Check if the phone number starts with a '+' (international format)
            $defaultRegion = str_starts_with($phone, '+') ? null : 'SA';  // Saudi Arabia as default region

            // Parse the phone number with the default region
            $phoneProto = $phoneUtil->parse($phone, $defaultRegion);

            // Ensure the number is a valid Saudi number
            if ($phoneProto->getCountryCode() !== 966) {
                throw new \libphonenumber\NumberParseException(
                    \libphonenumber\NumberParseException::INVALID_COUNTRY_CODE,
                    'Not a Saudi number'
                );
            }

            // Format the phone number to E.164 format
            return $phoneUtil->format($phoneProto, PhoneNumberFormat::E164);

        } catch (\libphonenumber\NumberParseException $e) {
            // Handle parsing error (log or return raw number if desired)
            return $phone;  // Return raw phone number or handle error as needed
        }
    }
}
