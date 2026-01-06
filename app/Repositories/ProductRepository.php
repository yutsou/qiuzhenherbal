<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductRepository implements RepositoryInterface
{
    protected $model;

    public function __construct(Product $product)
    {
        $this->model = $product;
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
        if (null == $product = $this->model->find($id)) {
            throw new ModelNotFoundException("Product not found");
        }

        return $product;
    }

    public function fill(array $data, $id=null)
    {
        if ($id !== null) {
            return $this->model->find($id)->fill($data)->save();
        } else {
            return $this->fill($data)->save();
        }
    }
}
