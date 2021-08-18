<?php


namespace Zcell\Kernel\KernelListener;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Config\Repository;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Illuminate\Http\JsonResponse;
use Zcell\Kernel\AttributeMicroKernel\BotResponseInterface;
use Zcell\Kernel\AttributeMicroKernel\MessageResponse;
use Zcell\Kernel\AttributeMicroKernel\PoolMessage;
use Zcell\Kernel\Kernel;
use Zcell\Kernel\KernelEvent\MessageDataEvent;
use Zcell\Kernel\KernelEvent\RequestEvent;
use Zcell\Kernel\KernelEvent\ResponseEvent;

class KernelListener implements EventSubscriberInterface
{
    /**
     * @var Repository
     */
    protected Repository $repository;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var string
     */
    protected string $requestUri;

    /**
     * @var string
     */
    protected string $prefixElement;

    /**
     * @var string
     */
    protected string $commandPrefixMode;

    /**
     * @var string
     */
    protected string $exchangeApiPrefixMode;

    /**
     * KernelListener constructor.
     * @param Repository $repository
     * @param LoggerInterface $logger
     */
    public function __construct(Repository $repository, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * @param RequestEvent $event
     */
    public function bootstrapParseRequestParams(RequestEvent $event): void
    {
        $requestUri = trim($event->getRequest()->getRequestUri(), '/');

        $this->requestUri = $requestUri;
        $this->prefixElement = explode('/', $requestUri)[0];
        $this->commandPrefixMode = $this->repository->get('telegram.mode.prefix_command');
        $this->exchangeApiPrefixMode = $this->repository->get('telegram.mode.prefix_exchange_api');;
    }

    public function setChatIdForKernel(MessageDataEvent $event)
    {
        $event->getKernel()->setChatId($event->getMessageData()->getChatId());
    }

    /**
     * @param RequestEvent $event
     */
    public function parseCommand(RequestEvent $event): void
    {
        if ($this->getPrefixElement() === $this->getCommandPrefixMode()) {
            $event->setMode(Kernel::COMMAND_MODE);
        }
    }

    /**
     * @param RequestEvent $event
     */
    public function parseExchangeApiMethod(RequestEvent $event): void
    {
        if ($this->getPrefixElement() === $this->getExchangeApiPrefixMode()) {
            $event->setMode(Kernel::API_MODE);
        }
    }

    /**
     * @param RequestEvent $event
     */
    public function setKernelMode(RequestEvent $event)
    {
        $event->setKernelMode($event->getMode());
    }

    /**
     * @param ResponseEvent $event
     */
    public function prepareResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if (is_string($response)) {
            $event->updateResponse(new JsonResponse(['message' => $response]));
        } elseif (is_array($response)) {
            $event->updateResponse(new JsonResponse($response));
        } elseif ($response instanceof MessageResponse) {
            $event->updateResponse(new JsonResponse(['message' => 'OK']));
        } elseif ($response instanceof PoolMessage) {
            $event->updateResponse(new JsonResponse(['message' => 'OK']));
        }
    }

    public function sendBotMessage(ResponseEvent $event)
    {
        if ($event->getKernel()->getCurrentMode() === Kernel::COMMAND_MODE && ($response = $event->getResponse()) instanceof MessageResponse) {
            $this->requestMessage($response);
        }
    }

    public function sendPoolMessage(ResponseEvent $event)
    {
        if ($event->getKernel()->getCurrentMode() === Kernel::COMMAND_MODE && ($response = $event->getResponse()) instanceof PoolMessage) {
            $this->requestPoolMessage($response);
        }
    }

    /**
     * @return string
     */
    protected function getPrefixElement(): string
    {
        return $this->prefixElement;
    }

    /**
     * @return string
     */
    protected function getCommandPrefixMode(): string
    {
        return $this->commandPrefixMode;
    }

    /**
     * @return string
     */
    protected function getExchangeApiPrefixMode(): string
    {
        return $this->exchangeApiPrefixMode;
    }

    /**
     * @param BotResponseInterface $message
     */
    protected function requestMessage(BotResponseInterface $message)
    {
        $url = $this->repository->get('telegram.url');
        $token = $this->repository->get('telegram.token');

        try {
            $client = new Client(['base_uri' => $url.$token.'/']);
            $request = new Request('POST', $message->getMethod(), ['content-type' => 'application/json'], $message->getMessage());

            $client->sendRequest($request);
        } catch (RequestException|ClientExceptionInterface $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * @param PoolMessage $poolMessage
     */
    protected function requestPoolMessage(PoolMessage $poolMessage)
    {
        $url = $this->repository->get('telegram.url');
        $token = $this->repository->get('telegram.token');

        $client = new Client(['base_uri' => $url.$token.'/']);

        $pool = new Pool($client, $poolMessage->getMessages(), [
            'fulfilled' => function (Response $response, $index) {
                if ($response->getStatusCode() !== 200) {
                    $this->logger->error($response->getBody()->getContents());
                }
            },
            'rejected' => function (RequestException $reason, $index) {
                $this->logger->error($reason->getMessage(), ['exception' => $reason]);
            },
        ]);

        $promise = $pool->promise();

        $promise->wait();
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            //
        ];
    }
}