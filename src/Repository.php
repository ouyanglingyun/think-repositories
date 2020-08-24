<?php
declare (strict_types = 1);
namespace lingyun\repositories;

use lingyun\repositories\Contracts\RepositoryInterface;
use lingyun\repositories\Exceptions\RepositoryException;
use think\App;
use think\Container;
use think\helper\Str;
use think\Http;
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
    protected $app;
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
    public function __construct(App $app, Request $request, Http $http)
    {
        $this->app              = $app;
        $this->request          = $request;
        $this->model            = $this->makeModel();
        $this->query            = $request->param(); //当前请求参数
        $this->application_name = $http->getName(); //当前应用名
        $this->controller_name  = str_replace('.', '/', preg_replace('/v\d+./u', '', $request->controller())); //当前控制器
        $this->action_name      = $request->action(); //当前操作
        unset($this->query[str_replace('.', '_', $request->baseUrl())]); //过滤请求参数
        // 初始化
        $this->initialize();
    }
    // 初始化
    protected function initialize()
    {}

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    abstract protected function model();

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = true)
    {
        return $this->model->field($columns)->findOrEmpty($id);
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
        return Container::getInstance()->make($class ?: App::class, $args, $newInstance);
    }

    /**
     * @return \think\Model
     * @throws RepositoryException
     */
    protected function makeModel()
    {
        $calss = $this->model();
        $model = Container::getInstance()->make($this->model() ?: Model::class); //$this->app->make($this->model());
        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be an instance of think\\Model");
        }
        return $this->model = $model; //->newInstance();
    }
    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }
}
