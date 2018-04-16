<?php

namespace App\Http\Controllers;

use App\Http\Resources\PersonalCode as PersonalCodeResource;
use App\PersonalCode;
use App\Services\PersonalCodeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PersonalCodeController extends Controller
{
    /**
     * @var PersonalCodeService
     */
    private $personalCodeService;

    public function __construct(PersonalCodeService $personalCodeService)
    {

        $this->personalCodeService = $personalCodeService;
    }

    /**
     * Generates personal code
     *
     * @param $birthDate
     * @param $sex
     * @return array|JsonResponse
     */
    public function generate($birthDate, $sex)
    {
        $data = compact('birthDate', 'sex');

        $validator = Validator::make($data, [
            'birthDate' => 'date',
            'sex' => 'in:0,1',
        ]);

        if ($validator->fails()) {
            return new JsonResponse(["error" => 'Invalid request'], 400);
        }

        $formattedDate = Carbon::createFromFormat('Y-m-d', $birthDate);
        $codes = $this->personalCodeService->getCodes($formattedDate, $sex);

        return compact('codes');
    }

    /**
     * Checks if personal code is valid
     *
     * @param $personalCode
     * @return JsonResponse
     */
    public function validation($personalCode)
    {

        $data = compact('personalCode');
        $validator = Validator::make($data, [
            'personalCode' => 'digits:11',
        ]);

        if ($validator->fails()) {
            return new JsonResponse(["error" => 'Invalid request'], 400);
        }

        if($this->personalCodeService->checkCode($personalCode) == true){
            return new JsonResponse(["value" => 'true'], 200);
        } else {
            return new JsonResponse(["value" => 'false'], 400);
        }
    }
}
