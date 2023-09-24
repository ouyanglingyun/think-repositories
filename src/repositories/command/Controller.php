<?php

declare(strict_types=1);

namespace think\repositories\command;

use think\console\Input;
use think\console\Output;
use think\console\command\Make;
use think\console\input\Option;

class Controller extends Make
{
    protected $type = "Controller";
    protected function configure()
    {
        parent::configure();
        $this->setName('make:C')
            ->addOption('middleware', "M", Option::VALUE_OPTIONAL, 'The middleware of the Controller class.')
            ->addOption('extends', "E", Option::VALUE_OPTIONAL, 'Generate extends an Controller class.')
            ->setDescription('Create a new resource Controller class');
    }

    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('name'));
        if ($input->hasOption('extends')) {
            $extends = $input->getOption('extends');
        } else {
            $extends = "BaseController";
        }

        if ($input->hasOption('middleware')) {
            $middlewareArray = explode(',', $input->getOption('middleware'));

            foreach ($middlewareArray as $key => $value) {
                $middlewareArray[$key] = "'{$value}'";
            }
            $middlewareArray = "[" . implode(',', $middlewareArray) . "]";
        } else {
            $middlewareArray = "[]";
        }

        $extendsClassname = $this->getClassName($extends);

        $classname = $this->getClassName($name);

        $pathname = $this->getPathName($classname);

        if (is_file($pathname)) {
            $output->writeln('<error>' . $this->type . ':' . $classname . ' already exists!</error>');
            return false;
        }

        if (!is_dir(dirname($pathname))) {
            mkdir(dirname($pathname), 0755, true);
        }

        file_put_contents($pathname, $this->buildController($classname, $middlewareArray, $extendsClassname));

        $output->writeln('<info>' . $this->type . ':' . $classname . ' created successfully.</info>');
    }

    protected function buildController(string $name, string $middlewareArray, string $extends)
    {
        $stub = file_get_contents($this->getStub());

        $namespace = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');

        $extends_namespace = trim(implode('\\', array_slice(explode('\\', $extends), 0, -1)), '\\');

        $class = str_replace($namespace . '\\', '', $name);

        $extendsClass = str_replace($extends_namespace . '\\', '', $extends);

        return str_replace([
            '{%className%}',
            '{%actionSuffix%}',
            '{%namespace%}',
            '{%app_namespace%}',
            '{%middlewareArray%}',
            '{%extendsClass%}',
            '{%useExtendsClass%}',
        ], [
            $class,
            $this->app->config->get('route.action_suffix'),
            $namespace,
            $this->app->getNamespace(),
            $middlewareArray,
            $extendsClass,
            $extends,
        ], $stub);
    }

    protected function getStub(): string
    {
        $stubPath = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR;

        return $stubPath . 'controller.api.stub';
    }

    protected function getClassName(string $name): string
    {
        return parent::getClassName($name) . ($this->app->config->get('route.controller_suffix') ? 'Controller' : '');
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\controller';
    }
}
