<?php


namespace Zcell\Kernel;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Http\Request;
use Zcell\Kernel\AttributeMicroKernel\MessageResponse;
use Zcell\Kernel\Exceptions\BadRequestHttpException;
use Zcell\Kernel\Exceptions\ModeNotFoundHttpException;
use Zcell\Kernel\KernelEvent\ErrorEvent;
use Zcell\Kernel\KernelEvent\RequestEvent;
use Zcell\Kernel\KernelEvent\ResponseEvent;
use Zcell\Kernel\MicroKernel\MicroKernelInterface;
use Vttrue\BotTemplate\Application;

class Kernel implements KernelInterface
{
    /**
     * @var Application
     */
    protected Application $app;

    /**
     * @var EventDispatcher
     */
    protected EventDispatcher $dispatcher;

    /**
     * @var array
     */
    protected array $middleware = [];

    /**
     * @var array
     */
    protected array $routeMiddleware = [];

    /**
     * @var string|null
     */
    protected ?string $currentMode = null;

    /**
     * @var
     */
    protected $chatId = null;

    /**
     * Kernel constructor.
     * @param Application $app
     * @param EventDispatcher $dispatcher
     */
    public function __construct(Application $app, EventDispatcher $dispatcher)
    {
        $this->app = $app;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Request $request
     */
    public function handle(Request $request): void
    {
        $this->setRequest($request);

        try {
            $response = $this->handleRow($request);
        } catch (\Throwable $e) {
            if ($e instanceof RequestExceptionInterface) {
                $e = new BadRequestHttpException($e->getMessage());
            }

            $response = $this->handleThrowable($request, $e);
        }

        // response
        $event = new ResponseEvent($this, $request, $response);
        $this->dispatcher->dispatch($event, KernelEvents::KERNEL_RESPONSE);

        $response = $event->getResponse();

        $response->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse|MessageResponse|mixed
     * @throws \Throwable
     */
    protected function handleRow(Request $request)
    {
        $event = new RequestEvent($this, $request);
        $this->dispatcher->dispatch($event, KernelEvents::KERNEL_REQUEST);

        if (! $event->hasMode()) {
            throw new ModeNotFoundHttpException('Mode is not found in the current request uri.');
        }

        $microKernel = $this->makeMicroKernel($event->getMode());

        return $microKernel->handle($request);
    }

    /**
     * @param Request $request
     */
    protected function setRequest(Request $request)
    {
        $this->app->instance(Request::class, $request);
    }

    /**
     * @param Request $request
     * @param \Throwable $e
     * @return JsonResponse
     */
    protected function handleThrowable(Request $request, \Throwable $e)
    {
        // error
        $event = new ErrorEvent($this, $request, $e);
        $this->dispatcher->dispatch($event, KernelEvents::KERNEL_ERROR);

        return $event->getResponse();
    }

    /**
     * @param string|null $mode
     */
    public function setCurrentMode(?string $mode)
    {
        $this->currentMode = $mode;
    }

    /**
     * @param string $mode
     * @return MicroKernelInterface
     */
    protected function makeMicroKernel(string $mode): MicroKernelInterface
    {
        return $this->getMicroKernelFactory()->make($mode);
    }

    /**
     * @return MicroKernelFactory
     */
    protected function getMicroKernelFactory(): MicroKernelFactory
    {
        return new MicroKernelFactory($this);
    }

    /**
     * @return Application
     */
    public function getApplication(): Application
    {
        return $this->app;
    }

    public function getCurrentMode()
    {
        return $this->currentMode;
    }

    public function setChatId($chatId)
    {
        $this->chatId = $chatId;
    }

    public function getChatId()
    {
        return $this->chatId;
    }
}