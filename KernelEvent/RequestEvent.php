<?php


namespace Zcell\Kernel\KernelEvent;


class RequestEvent extends BaseEvent
{
    /**
     * @var string|null
     */
    protected ?string $mode = null;

    /**
     * @param $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return string|null
     */
    public function getMode(): ?string
    {
        return $this->mode;
    }

    /**
     * @return bool
     */
    public function hasMode(): bool
    {
        return ! is_null($this->mode);
    }

    /**
     * @param string|null $mode
     */
    public function setKernelMode(?string $mode)
    {
        $this->kernel->setCurrentMode($mode);
    }
}