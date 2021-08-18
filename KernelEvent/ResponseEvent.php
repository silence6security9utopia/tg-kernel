<?php


namespace Zcell\Kernel\KernelEvent;


use Laravel\Lumen\Http\Request;
use Illuminate\Http\JsonResponse;
use Zcell\Kernel\AttributeMicroKernel\MessageResponse;
use Zcell\Kernel\Kernel;

class ResponseEvent extends BaseEvent
{
    /**
     * @var JsonResponse|MessageResponse|mixed
     */
    protected $response;

    /**
     * ResponseEvent constructor.
     * @param Kernel $kernel
     * @param Request $request
     * @param JsonResponse|MessageResponse|mixed $response
     */
    public function __construct(Kernel $kernel, Request $request, $response)
    {
        parent::__construct($kernel, $request);

        $this->response = $response;
    }

    /**
     * @return JsonResponse|MessageResponse|mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param JsonResponse|MessageResponse|mixed $response
     */
    public function updateResponse($response)
    {
        $this->response = $response;
    }
}