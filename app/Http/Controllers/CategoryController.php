<?php

namespace App\Http\Controllers;

use App\Http\Filters\CategoryFilter;
use App\Http\Requests\Category\FilterRequest;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;
use App\Models\Category;
use App\Services\ImgService;

class CategoryController extends Controller
{
    public $service;

    public function __construct(ImgService $service)
    {
        $this->service = $service;
    }
    public function index(FilterRequest $request)
    {
        $data = $request->validated();
        $filter = app()->make(CategoryFilter::class, ['queryParams' => array_filter($data)]);
        $query = Category::filter($filter);
        if ($request->sort == null) {
            $sort = 'asc';
        } else {
            $sort = $request->sort;
        }
        if(str_contains($request->getQueryString(), "orderBy=name"))
        {
            $query->orderBy('name', $sort);
        }
        $categories = $query->paginate(10);
        return $categories;
    }

    public function store(StoreRequest $request)
    {
        $this->authorize('create', Category::class);
        $data = $request->validated();
        $type = "category";
        $data['img'] = $this->service->storeImage($request, $type);
        $category = Category::create($data);
        if ($category)
        {
            return response()->json([
                'message' => 'Категория меню успешно добавлена'
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Ошибка, что-то пошло не так'
            ], 500);
        }
    }
    public function show($id)
    {
        $category = Category::find($id);
        if($category)
        {
            return response()->json($category, 200);
        }
        else
        {
            return response()->json([
                'message' => 'Категория меню не найдена'
            ], 404);
        }
    }
    public function edit($id)
    {
        $this->authorize('update', Category::class);
        $category = Category::find($id);
        if($category)
        {
            return response()->json($category,200);
        }
        else
        {
            return response()->json([
                'message' => 'Категория меню не найдена'
            ], 404);
        }
    }
    public function update(UpdateRequest $request, int $id)
    {
        $this->authorize('update', Category::class);
        $data = $request->validated();
        $category = Category::find($id);
        if($category)
        {
            if($request->hasFile('img')) {
                $type = "category";
                $data['img'] = $this->service->updateImage($request, $category['img'], $type);
            }
            $category->update($data);
            return response()->json([
                'message' => 'Категория меню успешно обновлена'
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Категория меню не найдена'
            ], 404);
        }
    }
    public function destroy($id)
    {
        $this->authorize('delete', Category::class);
        $category = Category::find($id);
        if($category)
        {
            $this->service->deleteImage($category['img']);
            $category->delete();
            return response()->json([
                'message' => 'Категория меню успешно удалена'
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Категория меню не найдена'
            ], 404);
        }
    }
}
