<?php

namespace think\repositories;

class Service extends \think\Service
{
    public function boot()
    {
        $this->commands([
            \think\repositories\command\Repository::class,
            \think\repositories\command\Model::class,
            \think\repositories\command\Controller::class,
        ]);
    }
}
