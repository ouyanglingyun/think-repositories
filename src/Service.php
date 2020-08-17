<?php

namespace lingyun\repositories;

class Service extends \think\Service
{
    public function boot()
    {
        $this->commands([
            \lingyun\repositories\command\Repository::class,
            \lingyun\repositories\command\Model::class,
            \lingyun\repositories\command\Controller::class,
        ]);
    }
}
