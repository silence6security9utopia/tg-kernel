<?php


namespace Zcell\Kernel\Exceptions;


class ModeNotFoundHttpException extends HttpException
{
    /**
     * ModeNotFoundHttpException constructor.
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct(404, $message, $code, $previous);
    }
}