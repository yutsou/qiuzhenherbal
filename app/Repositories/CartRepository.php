<?php

namespace App\Repositories;

use App\Models\Cart;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartRepository implements RepositoryInterface
{
    protected $model;

    public function __construct(Cart $cart)
    {
        $this->model = $cart;
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
        if (null == $cart = $this->model->find($id)) {
            throw new ModelNotFoundException("Cart not found");
        }

        return $cart;
    }

    public function fill(array $data, $id)
    {
        return $this->model->find($id)->fill($data)->save();
    }
}
