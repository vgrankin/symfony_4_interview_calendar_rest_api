<?php

namespace App\Service;
/**
 * Class ResponseErrorDecoratorService
 * @package App\Service
 *
 * Helper service to format nice error response out of given status-code and message
 *                (to standardize/make consistent error response from the server)
 */
class TimeConverterService
{
    /**
     * Coverts given AM/PM string to 24H format
     *
     * Example:
     * 11:00 AM will be converted to 11:00
     * 01:00 PM will be converted to 13:00
     *
     * @param string $amPmTime
     * @return string
     */
    public function convertTo24H(string $amPmTime): string
    {
        $s = strtolower($amPmTime);

        if (strpos($s, "am") !== FALSE) {
            $arr = explode(":", $s);
            if ($arr[0] == '12') {
                $arr[0] = '00';
            }
            $s = implode(":", $arr);
            $s = str_replace("am", "", $s);

            return $s;
        } else {
            $arr = explode(":", $s);
            if ($arr[0] == '12') {

            } else {
                $arr[0] = $arr[0] + 12;
            }
            $s = implode(":", $arr);
            $s = str_replace("pm", "", $s);

            return $s;
        }
    }

    /**
     * Extracts hour from given time string
     *
     * Example:
     * input = 11:00 will result in output = 11
     * input = 01:00 will result in output = 01
     *
     * @param string $time
     * @return string
     */
    public function extractHour(string $time): string
    {
        $arr = explode(":", $time);
        return $arr[0];
    }
}