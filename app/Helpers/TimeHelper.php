<?php

namespace App\Helpers;

class TimeHelper
{
    public static function formatTimeSpent(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds.' '.($seconds === 1 ? 'second' : 'seconds');
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        if ($minutes < 60) {
            $minuteText = $minutes.' '.($minutes === 1 ? 'minute' : 'minutes');
            $secondText = $remainingSeconds.' '.($remainingSeconds === 1 ? 'second' : 'seconds');

            return $remainingSeconds > 0 ? $minuteText.' and '.$secondText : $minuteText;
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        $hourText = $hours.' '.($hours === 1 ? 'hour' : 'hours');
        $minuteText = $remainingMinutes.' '.($remainingMinutes === 1 ? 'minute' : 'minutes');
        $secondText = $remainingSeconds.' '.($remainingSeconds === 1 ? 'second' : 'seconds');

        if ($remainingMinutes > 0 && $remainingSeconds > 0) {
            return $hourText.', '.$minuteText.' and '.$secondText;
        } elseif ($remainingMinutes > 0) {
            return $hourText.' and '.$minuteText;
        } elseif ($remainingSeconds > 0) {
            return $hourText.' and '.$secondText;
        } else {
            return $hourText;
        }
    }
}
