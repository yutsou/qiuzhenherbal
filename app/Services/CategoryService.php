<?php

namespace App\Services;

use App\Repositories\CategoryRepository;

class CategoryService extends CategoryRepository
{
    public function storeCategory($request)
    {
        $input = $request->all();
        $category = CategoryRepository::create($input);
        return $category;
    }

    public function getRoots()
    {
        return CategoryRepository::getRoots();
    }

    public function getAllCategories()
    {
        $categories = CategoryRepository::all();
        return $categories;
    }

    public function getCategory($categoryId)
    {
        $category = CategoryRepository::find($categoryId);
        return $category;
    }

    public function updateCategory($request, $categoryId)
    {
        $input = $request->all();
        CategoryRepository::fill($input, $categoryId);
    }
}
