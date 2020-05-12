<?php

namespace lingyun\repositories;

use lingyun\repositories\Contracts\RepositoryInterface;
use lingyun\repositories\Exceptions\RepositoryException;
use think\App;
use think\helper\Str;
use think\Model;
use think\Request;

/**
 * Class Repository
 * @package lingyun\repositories
 */
abstract class Repository implements RepositoryInterface
{

    /**
     * @var \think\App
     */
    private $app;
    /**
     * @var think\Model
     */
    protected $model;
    /**
     * @var \think\Request
     */
    protected $request;

    /**
     * @param App $app
     * @param Collection $collection
     * @throws \lingyun\Repositories\Exceptions\RepositoryException
     */
    public function __construct(App $app, Request $request)
    {
        $this->app     = $app;
        $this->request = $request;
        $this->makeModel();
    }
    // 调用实际类的方法
    public function __call($method, $params)
    {
        return call_user_func_array([$this->model, $method], $params);
    }

    // 调用实际类的方法
    public static function __callStatic($method, $params)
    {
        // Facade
        if (Str::endsWith($method, 'Facade')) {
            $method = substr($method, 0, -6);
        }
        return call_user_func_array([static::createFacade(), $method], $params);
    }

    /**
     * 创建Facade实例
     * @static
     * @access protected
     * @param  string $class       类名或标识
     * @param  array  $args        变量
     * @param  bool   $newInstance 是否每次创建新的实例
     * @return object
     */
    protected static function createFacade(string $class = '', array $args = [], bool $newInstance = false)
    {
        $class = $class ?: static::class;
        return (new $class((new App()), (new Request())));
    }
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    abstract public function model();

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = true)
    {
        return $this->model->field($columns)->find($id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws RepositoryException
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());
        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be an instance of think\\Model");
        }
        return $this->model = $model->newInstance();
    }
}
