<?php

namespace Lingyun\Repositories;

use think\Service as BaseService;

class Service extends BaseService
{
    public function boot()
    {

        $this->commands([
            'make:repository' => Console\MakeRepository::class,
        ]);
    }
}
