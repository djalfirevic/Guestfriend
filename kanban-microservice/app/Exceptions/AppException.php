<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class AppException
 *
 * @package App\Exceptions
 */
class AppException extends Exception
{
    /**
     * @var mixed
     */
    protected $errors;

    /**
     * AppException constructor.
     *
     * @param string $message
     * @param int $code
     * @param null $errors
     */
    public function __construct($message = '', int $code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $errors = null)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    /**
     * @return mixed|null
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
