<?php

namespace App\Repositories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CouponRepository implements RepositoryInterface
{
    protected $model;

    public function __construct(Coupon $coupon)
    {
        $this->model = $coupon;
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
        if (null == $coupon = $this->model->find($id)) {
            throw new ModelNotFoundException("Coupon not found");
        }

        return $coupon;
    }

    public function fill(array $data, $id)
    {
        return $this->model->find($id)->fill($data)->save();
    }
}
