<?php


namespace Zcell\Kernel\KernelEvent;


use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Zcell\Kernel\AttributeMicroKernel\MessageResponse;
use Zcell\Kernel\Exceptions\HttpException;
use Zcell\Kernel\Kernel;

class ErrorEvent extends BaseEvent
{
    /**
     * @var \Throwable
     */
    protected \Throwable $error;

    /**
     * @var string
     */
    protected string $message;

    /**
     * @var JsonResponse|MessageResponse
     */
    protected $response;

    /**
     * @var int
     */
    protected int $status;

    /**
     * ErrorEvent constructor.
     * @param Kernel $kernel
     * @param Request $request
     * @param \Throwable $error
     */
    public function __construct(Kernel $kernel, Request $request, \Throwable $error)
    {
        parent::__construct($kernel, $request);

        $this->error = $error;
    }

    /**
     * @return MessageResponse|JsonResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return \Throwable
     */
    public function getError(): \Throwable
    {
        return $this->error;
    }

    /**
     * @param JsonResponse|MessageResponse $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message ?? $this->message = ($this->error instanceof HttpException || $this->error instanceof HttpExceptionInterface)?($this->error->getMessage() === '')?'Not found route':$this->error->getMessage():'server_error';
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status ?? $this->status = ($this->error instanceof HttpException || $this->error instanceof HttpExceptionInterface)?$this->error->getStatusCode():500;
    }
}