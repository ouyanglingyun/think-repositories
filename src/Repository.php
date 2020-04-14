<?php

namespace lingyun\repositories;

use lingyun\repositories\Contracts\RepositoryInterface;
use lingyun\repositories\Exceptions\RepositoryException;
use think\App;
use think\Model;

/**
 * Class Repository
 * @package lingyun\repositories
 */
abstract class Repository implements RepositoryInterface
{

    /**
     * @var App
     */
    private $app;
    /**
     * @var
     */
    protected $model;

    protected $newModel;

    /**
     * @param App $app
     * @param Collection $collection
     * @throws \lingyun\Repositories\Exceptions\RepositoryException
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
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
    public function find($id, $columns = array('*'))
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
