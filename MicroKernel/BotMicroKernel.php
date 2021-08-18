<?php


namespace Zcell\Kernel\MicroKernel;


use FastRoute\Dispatcher;
use Laravel\Lumen\Routing\Pipeline;
use Laravel\Lumen\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zcell\Kernel\AttributeMicroKernel\MessageData;
use Zcell\Kernel\AttributeMicroKernel\MessageResponse;
use Zcell\Kernel\CommandController\CommandController;
use Zcell\Kernel\Exceptions\SystemException;
use Zcell\Kernel\KernelEvent\MessageDataEvent;
use Zcell\Kernel\KernelEvent\TerminateFormEvent;
use Zcell\Kernel\KernelEvents;

class BotMicroKernel extends BaseMicroKernel
{
    /**
     * @var MessageData
     */
    protected MessageData $messageData;

    /**
     * @param Request $request
     * @return mixed
     * @throws SystemException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(Request $request)
    {
        $this->parseMessageData($request);

        $this->app->boot();

        // message data
        $event = new MessageDataEvent($this->kernel, $request, $this->messageData);
        $this->dispatcher->dispatch($event, KernelEvents::KERNEL_MESSAGE_DATA);

        if ($event->isFormEvent()) {
            return $this->handleEvent($event->getFormEvent());
        }

        return $this->handleCommand();
    }

    /**
     * @return mixed
     * @throws SystemException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function handleCommand()
    {
        [$method, $command] = $this->parseMessage();

        return $this->sendThroughPipeline($this->app->getMiddleware(), function ($messageData) use ($method, $command) {
            if (isset($this->router->getRoutes()[$method.$command])) {
                return $this->handleFoundCommand([true, $this->router->getRoutes()[$method.$command]['action'], []]);
            }

            return $this->handleDispatcherResponse(
                $this->createDispatcher()->dispatch($method, $command)
            );
        });
    }

    /**
     * @param array $commandInfo
     * @return mixed
     * @throws SystemException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function handleFoundCommand(array $commandInfo)
    {
        $action = $commandInfo[1];

        if (isset($action['middleware'])) {

            $middleware = $this->app->getGatherMiddlewareClassNames($action['middleware']);

            return $this->sendThroughPipeline($middleware, function () use ($commandInfo) {
                return $this->callControllerMethod($commandInfo);
            });
        }

        return $this->callControllerMethod($commandInfo);
    }

    /**
     * @param array $commandInfo
     * @return MessageResponse
     * @throws SystemException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function callControllerMethod(array $commandInfo)
    {
        $uses = $commandInfo[1]['uses'];

        [$controller, $method] = explode('@', $uses);

        if (! method_exists($instance = $this->app->make($controller), $method)) {
            throw new SystemException('Method '.$method.' is not resolve this current controller '.$controller.'.');
        }

        if (! $instance instanceof CommandController) {
            throw new SystemException('Controller '.$controller.' is not instanceof '.CommandController::class.' class.');
        }

        return $this->app->call([$instance, $method], $commandInfo[2]);
    }

    /**
     * @param $routeInfo
     * @return mixed|MessageResponse
     */
    protected function handleDispatcherResponse($routeInfo)
    {
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                throw new NotFoundHttpException;
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedHttpException($routeInfo[1]);
            case Dispatcher::FOUND:
                return $this->handleFoundCommand($routeInfo);
        }
    }

    /**
     * @return Dispatcher
     */
    protected function createDispatcher(): Dispatcher
    {
        return \FastRoute\simpleDispatcher(function ($r) {
            foreach ($this->router->getRoutes() as $route) {
                $r->addRoute($route['method'], $route['uri'], $route['action']);
            }
        });
    }

    /**
     * @param array $middleware
     * @param \Closure $then
     * @return mixed
     */
    protected function sendThroughPipeline(array $middleware, \Closure $then)
    {
        if (count($middleware) > 0) {
            return (new Pipeline($this->app))
                ->send($this->messageData)
                ->through($middleware)
                ->then($then);
        }

        return $then($this->messageData);
    }

    /**
     * @return array
     */
    protected function parseMessage(): array
    {
        return [$this->messageData->getMethod(), '/'.trim($this->messageData->getCommand() ?? $this->messageData->getText(), '/')];
    }

    /**
     * @param Request $request
     */
    protected function parseMessageData(Request $request)
    {
        $this->messageData = MessageData::createFromRequest($request);

        $this->app->instance(MessageData::class, $this->messageData);
    }

    /**
     * @param string $event
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function handleEvent(string $event)
    {
        $event = new $event();

        $event->setMessageData($this->messageData);

        $result = $event->handle();

        // terminate form event
        $event = new TerminateFormEvent($this->kernel, $this->app->make(Request::class), $this->messageData);
        $this->dispatcher->dispatch($event, KernelEvents::KERNEL_TERMINATE_FORM_EVENT);

        return $result;
    }

}