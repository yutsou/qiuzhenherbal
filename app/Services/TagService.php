<?php

namespace App\Services;

use App\Repositories\TagRepository;

class TagService extends TagRepository
{
    public function storeTag($request)
    {
        $input = $request->all();
        $tag = TagRepository::create($input);
        return $tag;
    }

    public function getAllTags()
    {
        $categories = TagRepository::all();
        return $categories;
    }

    public function getTag($tagId)
    {
        $tag = TagRepository::find($tagId);
        return $tag;
    }

    public function updateTag($request, $tagId)
    {
        $input = $request->all();
        TagRepository::fill($input, $tagId);
    }
}
