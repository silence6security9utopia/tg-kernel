<?php


namespace Zcell\Kernel\AttributeMicroKernel;


use GuzzleHttp\Psr7\Request;

class PoolMessage
{
    /**
     * @var BotResponseInterface[]
     */
    protected array $messages = [];

    /**
     * @var array|string[]
     */
    protected array $headers = [
        'content-type' => 'application/json'
    ];

    /**
     * @param BotResponseInterface $message
     */
    public function push(BotResponseInterface $message)
    {
        array_push($this->messages, $message);
    }

    /**
     * @return \Generator
     */
    public function getMessages(): \Generator
    {
        foreach ($this->messages as $message) {
            yield new Request('POST', $message->getMethod(), $this->headers, $message->getMessage());
        }
    }
}