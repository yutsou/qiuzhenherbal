<?php

namespace App\Repositories;

use App\Models\InviteCode;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InviteCodeRepository implements RepositoryInterface
{
    protected $model;

    public function __construct(InviteCode $inviteCode)
    {
        $this->model = $inviteCode;
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
        if (null == $inviteCode = $this->model->find($id)) {
            throw new ModelNotFoundException("InviteCode not found");
        }

        return $inviteCode;
    }

    public function fill(array $data, $id)
    {
        return $this->model->find($id)->fill($data)->save();
    }
}
