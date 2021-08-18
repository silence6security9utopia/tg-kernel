<?php


namespace Zcell\Kernel\MicroKernel;


use Laravel\Lumen\Http\Request;

interface MicroKernelInterface
{
    /**
     * @param Request $request
     * @throws \Throwable
     * @return mixed
     */
    public function handle(Request $request);
}