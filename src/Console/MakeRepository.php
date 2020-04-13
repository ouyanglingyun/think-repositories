<?php

namespace Lingyun\Repositories\Console;

use think\console\command\Make;

class MakeRepository extends Make
{

    protected $type = "Repository";

    protected function configure()
    {
        parent::configure();
        $this->setName('make:repository')
            ->setDescription('Create a new repository class');
    }

    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'repository.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\repository';
    }
}
