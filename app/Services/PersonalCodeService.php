<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PersonalCodeService
{
    /**
     * @param $birthDate
     * @param $sex
     * @return array
     */
    public function getCodes($birthDate, $sex)
    {
        $first = $this->firstNumber($birthDate, $sex);

        $second = $this->secondToSixNumber($birthDate);

        $codes = $this->finalPersonalCode($first, $second);

        return $codes;
    }

    public function checkCode($personalCode)
    {
        if($this->checkingFirstDigit($personalCode) == true){
            if($this->checkingSecondToNineDigit($personalCode) == true){
                if ($this->checkingLastDigit($personalCode)){
                    return true;
                }
            }
        } else {
            return false;
        }


    }

    /**
     * Checking what century given year is
     *
     * @param $date
     * @return float
     */
    protected function century($date)
    {
        $year = $date->year;
        return ceil($year / 100);
    }


    /**
     * Generating first number of personal code
     *
     * @param $date
     * @param $sex
     * @return string
     */
    protected function firstNumber($date, $sex)
    {
        $century = $this->century($date);

        if ($century == '19' && $sex == '0') {
            return '2';
        }
        if ($century == '19' && $sex == '1') {
            return '1';
        }
        if ($century == '20' && $sex == '0') {
            return '4';
        }
        if ($century == '20' && $sex == '1') {
            return '3';
        }
        if ($century == '21' && $sex == '0') {
            return '6';
        }
        if ($century == '21' && $sex == '1') {
            return '5';
        }
    }


    /**
     * Generating six numbers of personal code from birth date
     *
     * @param $date
     * @return bool|string
     */
    protected function secondToSixNumber($date)
    {
        $stringDate = $date->format("Ymd");
        $secondToSixDigit = substr($stringDate, 2);
        return $secondToSixDigit;
    }


    /**
     * Number of person born on that day
     *
     * @param $number
     * @return string
     */
    protected function sevenToNineNumber($number)
    {
        $sevenToNineNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
        return $sevenToNineNumber;
    }


    /**
     * Generating last digit
     *
     * @param $uncompletedPersonalCode
     * @return int
     */
    protected function lastNumber($uncompletedPersonalCode)
    {
        return $lastDigit = (
                $uncompletedPersonalCode[0] * 1 +
                $uncompletedPersonalCode[1] * 2 +
                $uncompletedPersonalCode[2] * 3 +
                $uncompletedPersonalCode[3] * 4 +
                $uncompletedPersonalCode[4] * 5 +
                $uncompletedPersonalCode[5] * 6 +
                $uncompletedPersonalCode[6] * 7 +
                $uncompletedPersonalCode[7] * 8 +
                $uncompletedPersonalCode[8] * 9 +
                $uncompletedPersonalCode[9] * 1
            ) % 11;

    }


    /**
     * Alternative formula for last digit
     *
     * @param $uncompletedPersonalCode
     * @return int
     */
    protected function altLastNumber($uncompletedPersonalCode)
    {
        return $altLast = (
                $uncompletedPersonalCode[0] * 3 +
                $uncompletedPersonalCode[1] * 4 +
                $uncompletedPersonalCode[2] * 5 +
                $uncompletedPersonalCode[3] * 6 +
                $uncompletedPersonalCode[4] * 7 +
                $uncompletedPersonalCode[5] * 8 +
                $uncompletedPersonalCode[6] * 9 +
                $uncompletedPersonalCode[7] * 1 +
                $uncompletedPersonalCode[8] * 2 +
                $uncompletedPersonalCode[9] * 3
            ) % 11;
    }

    /**
     * Checking Residue for last personal code number
     *
     * @param $tenDigitPersonalCode
     * @return int
     */
    protected function checkingResidue($tenDigitPersonalCode)
    {
        if ($this->lastNumber($tenDigitPersonalCode) !== 10) {
            return $last = $this->lastNumber($tenDigitPersonalCode);
        } else {
            if ($this->altLastNumber($tenDigitPersonalCode) !== 10) {
                return $last = $this->altLastNumber($tenDigitPersonalCode);
            } else {
                return $last = 0;
            }
        }
    }


    /**
     * Generates full personal code
     *
     * @param $firstPersonalCodeDigit
     * @param $secondToSixthPersonalCodeDigits
     * @return array
     */
    protected function finalPersonalCode($firstPersonalCodeDigit, $secondToSixthPersonalCodeDigits)
    {
        $codes = [];
        for ($a = 0; $a < 1000; $a++) {
            $sevenToNineDigits = $this->sevenToNineNumber($a);

            $personalCode = $firstPersonalCodeDigit . $secondToSixthPersonalCodeDigits . $sevenToNineDigits;

            $last = $this->checkingResidue($personalCode);

            $codes[] = $firstPersonalCodeDigit . $secondToSixthPersonalCodeDigits . $sevenToNineDigits . $last;

        }
        return $codes;
    }

    public function fullYear($first)
    {
        if ($first == 1 || $first == 2){
            return '18';
        }
        if ($first == 3 || $first == 4 ){
            return '19';
        }
        if ($first == 5 || $first == 6){
            return '20';
        }
    }

    public function checkingFirstDigit($personalCode)
    {
        $first = $personalCode[0];
        if ($first > 0 && $first < 7){
            if($this->fullYear($first)){
                return true;
            }
        } else {
            return false;
        }
        return true;
    }

    public function checkingSecondToNineDigit($personalCode)
    {
        $firstPartOfBirthDate = $personalCode[0];
        $secondPartOfBirthDate = substr($personalCode, 1, 6);

        $fullBirthDate = $firstPartOfBirthDate . $secondPartOfBirthDate;

        $data = compact ('fullBirthDate');

        $validator = Validator::make($data, [
            'fullBirthDate' => 'date',
        ]);

        if ($validator->fails()) {
            return false;
        }
        return true;
    }

    public function checkingLastDigit($personalCode)
    {
        $uncompletedPersonalCode = substr($personalCode, 0,10);
        $last = $personalCode[10];

        if ($this->checkingResidue($uncompletedPersonalCode) == $last){
            return true;
        } else {
            return false;
        }
    }



}