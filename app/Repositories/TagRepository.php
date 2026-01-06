<?php

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TagRepository implements RepositoryInterface
{
    protected $model;

    public function __construct(Tag $tag)
    {
        $this->model = $tag;
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
        if (null == $tag = $this->model->find($id)) {
            throw new ModelNotFoundException("Tag not found");
        }

        return $tag;
    }

    public function fill(array $data, $id)
    {
        return $this->model->find($id)->fill($data)->save();
    }
}
