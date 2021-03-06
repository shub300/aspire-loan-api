<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


        /**
     * Validates data and sends json response if validation fails.
     *
     * @param array $data
     * @param array $rules
     * @return array|bool
     */
    protected function validateWithJson($data = [], $rules = [])
    {
        $validator = Validator::make($data, $rules);

        if ($validator->passes()) {
            return true;
        }

        return $validator->getMessageBag();
    }
    
        /**
     * JSON response for success type response.
     *
     * @param string $message
     * @param array $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithSuccess($message = '', $data = [], $code = 200)
    {
        $res_data = [
            'success' => true,
            'message' => $message
        ];
        if($data)
            $res_data['data'] = $data;

        return response()->json($res_data, $code);
    }

    /**
     * JSON response for error type response.
     *
     * @param string $message
     * @param array $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithError($message = '', $data = [], $code = 400)
    {
        $res_data = [
            'error' => true,
            'message' => $message
        ];
        
        if($data)
            $res_data['data'] = $data;

        return response()->json($res_data, $code);
    }
}
