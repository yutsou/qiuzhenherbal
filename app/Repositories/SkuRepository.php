<?php

namespace App\Repositories;

use App\Models\Sku;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SkuRepository implements RepositoryInterface
{
    protected $model;

    public function __construct(Sku $sku)
    {
        $this->model = $sku;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->model->where('id', $id)
            ->update($data);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function find($id)
    {
        if (null == $sku = $this->model->find($id)) {
            throw new ModelNotFoundException("Sku not found");
        }

        return $sku;
    }

    public function fill(array $data, $id)
    {
        return $this->model->find($id)->fill($data)->save();
    }
}
