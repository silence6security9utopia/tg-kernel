<?php


namespace Zcell\Kernel\Exceptions;


class BadRequestHttpException extends HttpException
{
    /**
     * BadRequestHttpException constructor.
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct(400, $message, $code, $previous);
    }
}