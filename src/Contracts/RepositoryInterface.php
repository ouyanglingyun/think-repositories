<?php
declare (strict_types = 1);
namespace lingyun\repositories\Contracts;

/**
 * Interface RepositoryInterface
 * @package lingyun\Repositories\Contracts
 */
interface RepositoryInterface
{
    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = array('*'));

}
