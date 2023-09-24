<?php

declare(strict_types=1);

namespace think\repositories\command;

use think\console\Input;
use think\console\Output;
use think\console\command\Make;
use think\console\input\Argument;
use think\console\input\Option;

class Repository extends Make
{
    protected $type = "Repository";
    protected function configure()
    {
        parent::configure();
        $this->addArgument('model', Argument::OPTIONAL, "The model of the class");
        $this->setName('make:R')
            ->addOption('extends', "E", Option::VALUE_OPTIONAL, 'Generate extends an Repository class.')
            ->setDescription('Create a new resource Repository class');
    }

    protected function execute(Input $input, Output $output)
    {
        $name  = trim($input->getArgument('name'));
        $model = $input->getArgument('model');

        empty($model) || $model = trim($model);

        if ($input->hasOption('extends')) {
            $extends = $input->getOption('extends');
        } else {
            $extends = "common@BaseRepository";
        }

        $extendsClassname = $this->getClassName($extends);

        $classname = $this->getClassName($name);

        $modelname = null;

        if (!empty($model)) {
            $modelname = $this->getModelName($model);
        }

        $pathname = $this->getPathName($classname);

        if (is_file($pathname)) {
            $output->writeln('<error>' . $this->type . ':' . $classname . ' already exists!</error>');
            return false;
        }

        if (!is_dir(dirname($pathname))) {
            mkdir(dirname($pathname), 0755, true);
        }

        file_put_contents($pathname, $this->buildRepositoryClass($classname, $modelname, $extendsClassname));

        $output->writeln('<info>' . $this->type . ':' . $classname . ' created successfully.</info>');
    }

    protected function buildRepositoryClass(string $name, string $model = null, string $extends = null)
    {

        $type              = 'plain';
        $extends_namespace = $modelClass = $extendsClass = '';

        if (!empty($model)) {
            $type       = null;
            $modelClass = "\\" . $model . "::Class";
        }

        $stub = file_get_contents($this->getStub($type));

        $namespace = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
        $class     = str_replace($namespace . '\\', '', $name);

        if (!empty($extends)) {
            $extends_namespace = trim(implode('\\', array_slice(explode('\\', $extends), 0, -1)), '\\');
            $extendsClass      = str_replace($extends_namespace . '\\', '', $extends);
        }

        return str_replace([
            '{%className%}',
            '{%actionSuffix%}',
            '{%namespace%}',
            '{%app_namespace%}',
            '{%modelClass%}',
            '{%extendsClass%}',
            '{%useExtendsClass%}',
        ], [
            $class,
            $this->app->config->get('route.action_suffix'),
            $namespace,
            $this->app->getNamespace(),
            $modelClass,
            $extendsClass,
            $extends,
        ], $stub);
    }

    protected function getStub($type = null): string
    {
        $stubPath = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR;
        if (!empty($type)) {
            return $stubPath . 'repository.plain.stub';
        }
        return $stubPath . 'repository.stub';
    }
    protected function getModelName(string $name): string
    {
        return $this->getModelClassName($name);
    }

    protected function getModelClassName(string $name): string
    {
        if (strpos($name, '\\') !== false) {
            return $name;
        }

        if (strpos($name, '@')) {
            [$app, $name] = explode('@', $name);
        } else {
            $app = '';
        }

        if (strpos($name, '/') !== false) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->getModelNamespace($app) . '\\' . $name;
    }

    protected function getModelNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\model';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\repositories';
    }
}
