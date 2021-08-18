<?php


namespace Zcell\Kernel;

use Laravel\Lumen\Http\Request;
use Vttrue\BotTemplate\Application;

interface KernelInterface
{
    /**
     *
     */
    public const COMMAND_MODE = 'bot';

    /**
     *
     */
    public const API_MODE = 'api';

    /**
     * @param Request $request
     * @return void
     */
    public function handle(Request $request): void;

    /**
     * @return Application
     */
    public function getApplication(): Application;
}