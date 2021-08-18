<?php


namespace Zcell\Kernel\MicroKernel;

use Laravel\Lumen\Http\Request;

class ApiMicroKernel extends BaseMicroKernel
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function handle(Request $request)
    {
        return $this->app->dispatch($request);
    }
}