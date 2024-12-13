<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ApiResponsesTrait
{
    function successResponse($data, $message = "Success Response", $code = 200): JsonResponse
    {
        return response()->json(
            [
                'status' => 1,
                'code' => $code,
                'message' => $message,
                'oData' => $data,
                'errors' => (object)array()
            ], 200);

    }


    function successPaginatedResponse($data, $message = "Success Response", $code = 200): JsonResponse
    {
        return new JsonResponse(
            [
                'status' => 1,
                'code' => $code,
                'message' => $message,
                'oData' => $data->getCollection(),
                'pagination' => [
                    'links' => $data->links(),
                    'page' => $data->currentPage(),
                    'total' => $data->total(),
                    'perPage' => $data->perPage(),
                ],
                'errors' => []
            ], 200);
    }



    function errorResponse($errors, $message = "Error Response", $code = 400): JsonResponse
    {
        return new JsonResponse(
            [
                'status' => 0,
                'code' => $code,
                'message' => $message,
//                'oData' => [],
                'errors' => (object)$errors
            ], 200);
    }

    function notFoundResponse($message = "Resource Not Found", $code = 404): JsonResponse
    {
        return new JsonResponse(
            [
                'status' => 0,
                'code' => $code,
                'message' => $message,
//                'oData' => [],
                'errors' => (object)[]
            ], 200);
    }

    function validationErrorResponse($errors, $message = "Validation errors", $code = 422): JsonResponse
    {
        return new JsonResponse(
            [
                'status' => 0,
                'code' => $code,
                'message' => $message,
//                'oData' => [],
                'errors' => (object)$errors
            ], 200);
    }


}
