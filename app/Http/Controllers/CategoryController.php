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
        $this->authorize('view', auth()->user());
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
            $query->orderBy('name', $sort)->paginate(5);
        }
        $categories = $query->paginate(10);
        return $categories;
    }

    public function store(StoreRequest $request)
    {
        $this->authorize('view', auth()->user());

        $this->service->storeImage($request);
    }
    public function show($id)
    {
        $this->authorize('view', auth()->user());
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
        $this->authorize('view', auth()->user());
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
        $this->authorize('update', auth()->user());
        $data = $request->validated();
        $category = Category::find($id);
        if($category)
        {
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
        $this->authorize('delete', auth()->user());
        $category = Category::find($id);
        if($category)
        {
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
    public function restore($id)
    {
        $this->authorize('restore', auth()->user());
        $category = Category::withTrashed()->find($id);
        if($category)
        {
            $category->restore();
            return response()->json([
                'message' => 'Категория меню успешно восстановлена'
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
