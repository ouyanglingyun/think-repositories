<?php

declare(strict_types=1);

namespace think\repositories\Contracts;

/**
 * Interface RepositoryInterface
 * @package think\repositories\Contracts
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
