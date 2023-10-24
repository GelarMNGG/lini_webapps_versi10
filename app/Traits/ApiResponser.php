<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponser
{
    /**
     * Building success response
     * @param $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($data, $message, $code = Response::HTTP_OK)
    {
        return response()->json(['code' => $code, 'message' => $message, 'data' => $data,], Response::HTTP_OK);
    }


    public function errorResponse($message, $code = Response::HTTP_OK)
    {
        return response()->json(['code' => $code, 'message' => $message, 'data' => null], Response::HTTP_OK);
    }
}
