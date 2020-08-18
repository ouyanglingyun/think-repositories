<?php
declare (strict_types = 1);

namespace lingyun\repositories\command;

use think\console\command\Make;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class Model extends Make
{
    protected $type = "Model";
    protected function configure()
    {
        parent::configure();
        $this->setName('make:M')
            ->addOption('extends', "E", Option::VALUE_OPTIONAL, 'Generate extends an Controller class.')
            ->setDescription('Create a new model class');
    }

    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('name'));
        if ($input->hasOption('extends')) {
            $extends = $input->getOption('extends');
        } else {
            $extends = "common@Common";
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

        file_put_contents($pathname, $this->buildModel($classname, $extendsClassname));

        $output->writeln('<info>' . $this->type . ':' . $classname . ' created successfully.</info>');
    }

    protected function buildModel(string $name, string $extends)
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
            '{%extendsClass%}',
            '{%useExtendsClass%}',
        ], [
            $class,
            $this->app->config->get('route.action_suffix'),
            $namespace,
            $this->app->getNamespace(),
            $extendsClass,
            $extends,
        ], $stub);
    }

    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'model.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\model';
    }
}
