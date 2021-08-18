<?php


namespace Zcell\Kernel\Exceptions;


class HttpException extends \Exception implements HttpExceptionInterface
{
    /**
     * @var int
     */
    protected int $statusCode;

    /**
     * HttpException constructor.
     * @param int $statusCode
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(int $statusCode = 400, $message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->statusCode = $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}