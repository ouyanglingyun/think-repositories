<?php

namespace lingyun\repositories;

use lingyun\repositories\command\Repository;

class Service extends \think\Service
{
    public function boot()
    {
        $this->commands([
            Repository::class,
        ]);
    }
}
