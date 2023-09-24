<?php

namespace App\Traits;

use stdClass;
use Exception;
use Throwable;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{

    /** 
     * @var Exception|Throwable $exception 
     * */
    public $exception;

    /**
     * Parse the response data
     * 
     * @param array|null $data
     * @param array|null $meta
     * @param int|null $statusCode
     * 
     * @return JsonResponse
     */
    public function parseResponseData($data = [], array $meta = [], int $statusCode = 200)
    {
        $metaData = [
            "status_code" => $statusCode,
            "message" => @$meta['message'],
            "exception" => null,
            "errors"   => null,
            "pagination"   => null,
        ];

        $exceptStatusCodes = [
            Response::HTTP_NOT_FOUND, 
            Response::HTTP_FORBIDDEN, 
            Response::HTTP_UNPROCESSABLE_ENTITY,
            Response::HTTP_UNAUTHORIZED,
        ];

        $errorStackTrace = $this->getErrorStackTrace();
        if (count($errorStackTrace) && !in_array($statusCode, $exceptStatusCodes)) {
            $metaData['exception'] = $errorStackTrace;
        }

        // add meta value
        if (!empty($meta)) {
            $metaData = array_merge($metaData, $meta);
        }

        // empty data
        if (empty($data) || $data == new stdClass()) {
            return response()->json([
                'data' => $data,
                ...$metaData,
            ], $statusCode);
        }

        // add meta pagination key
        if ($data instanceof LengthAwarePaginator) {
            $metaData['pagination'] = $this->getPaginate($data);
            $data = $data->items();
        }

        if ($data instanceof ResourceCollection) {
            $metaData['pagination'] = $this->getPaginate($data);
        }

        return response()->json([
            'data' => $data,
            ...$metaData,
        ], $statusCode);
    }

    /**
     * Reponse success
     * 
     * @param array|null $data
     * @param array|null $meta
     * @param int|null $statusCode
     * 
     * @return JsonResponse
     */
    public function apiResponse($data, array $meta = [], $statusCode = 200)
    {
        return $this->parseResponseData($data, $meta, $statusCode);
    }

    /**
     * Response error
     * 
     * @param array|null $errors
     * @param int|null $statusCode
     * 
     * @return JsonResponse
     */
    public function apiResponseError(array $errors = [], $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return $this->parseResponseData(null, $errors, $statusCode);
    }

    /**
     * Respond with no content.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    protected function respondNoContent($message = 'No Content')
    {
        return $this->apiResponse(null, [
            'message' => $message
        ], Response::HTTP_NO_CONTENT);
    }

    /**
     * Respond record is created.
     *
     * @param $data
     * @param $message
     *
     * @return JsonResponse
     */
    public function respondCreated($data, $message = "Created")
    {
        return $this->apiResponse($data, [
            'message' => $message
        ], Response::HTTP_CREATED);
    }

    /**
     * Respond with forbidden.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    protected function respondForbidden($message = 'Forbidden')
    {
        return $this->apiResponseError([
            'message' => $message,
        ], Response::HTTP_FORBIDDEN);
    }


    /**
     * Response validation errors
     * 
     * @param \Illuminate\Validation\ValidationException $exception
     * 
     * @return JsonResponse
     */
    public function respondValidationErrors(ValidationException $exception)
    {
        return $this->apiResponseError([
            'message' => $exception->getMessage(),
            'errors' => $exception->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Respond with not found.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function respondNotFound(string $message = '404 Not Found')
    {
        return $this->apiResponseError([
            'message' => $message
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * Respond internal server error.
     *
     * @param Exception|Throwable $exception
     *
     * @return JsonResponse
     */
    public function respondInternalError(Exception|Throwable $exception)
    {

        return $this->withException($exception)->apiResponseError([
            'message' => "Internal Server Error"
        ],  Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Respond with unauthorized.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function respondUnAuthorized($message = 'Unauthorized')
    {
        return $this->apiResponseError([
            'message' => $message
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Set error exception
     *
     * @param Exception $exception
     *
     * @return $this
     */
    public function withException(Exception|Throwable $exception)
    {
        $this->exception = $exception;
        return $this;
    }

    /** 
     * Get error stack trace
     * 
     * @return array
     */
    public function getErrorStackTrace()
    {
        if (empty($this->exception)) {
            return [];
        }

        $exception = $this->exception;
        $enableTraceError = config('core.error_debug_trace_enabled');

        if (!$enableTraceError) {
            return [];
        }

        return [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
            'trace' => $exception->getTrace(),
        ];
    }

    /**
     * Get Pagination data
     *
     * @param array|LengthAwarePaginator|ResourceCollection|object $data
     *
     * @return array|null
     */
    private function getPaginate($data)
    {
        if ($data instanceof LengthAwarePaginator) {
            return [
                "current_page" => $data->currentPage(),
                "last_page" => $data->lastPage(),
                "per_page"     => (int) $data->perPage(),
                "total"   => $data->total(),
                "count"   => $data->count(),
                "from"    => $data->firstItem(),
                "to"      => $data->lastItem()
            ];
        }

        if (isset($data->resource)) {
            $resource = !is_array($data->resource)
                ? (array) $data->resource->toArray()
                : $data->resource;
            if (isset($resource['current_page'])) {
                return [
                    "current_page" => $resource['current_page'],
                    "last_page"    => $resource['last_page'],
                    "per_page"     => (int)$resource['per_page'],
                    "total"   => $resource['total'],
                    "count"   => count($resource['data']),
                    "from"    => $resource['from'],
                    "to"      => $resource['to']
                ];
            }
        }

        return null;
    }
}
