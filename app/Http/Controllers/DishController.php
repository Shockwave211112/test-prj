<?php

namespace App\Http\Controllers;

use App\Http\Filters\DishFilter;
use App\Http\Requests\Dish\FilterRequest;
use App\Http\Requests\Dish\StoreRequest;
use App\Http\Requests\Dish\UpdateRequest;
use App\Models\Category;
use App\Models\Dish;
use App\Services\ImgService;
use Illuminate\Http\Request;

class DishController extends Controller
{
    public $service;
    public function __construct(ImgService $service)
    {
        $this->service = $service;
    }
    public function index(FilterRequest $request)
    {
        $data = $request->validated();
        $filter = app()->make(DishFilter::class, ['queryParams' => array_filter($data)]);
        $query = Dish::filter($filter);
        if ($request->sort == null) {
            $sort = 'asc';
        } else {
            $sort = $request->sort;
        }
        switch($request->orderBy)
        {
            case 'name':
                $query->orderBy('name', $sort);
                break;
            case 'calories':
                $query->orderBy('calories', $sort);
                break;
            case 'price':
                $query->orderBy('price', $sort);
                break;
            case 'category_id':
                $query->orderBy('category_id', $sort);
                break;
        }
        return $query->paginate(10);
    }

    public function store(StoreRequest $request)
    {
        $this->authorize('create', Dish::class);
        $data = $request->validated();
        $type = "dish";
        $data['img'] = $this->service->storeImage($request, $type);
        $dish = Dish::create($data);
        if ($dish)
        {
            return response()->json([
                'message' => 'Блюдо успешно добавлено'
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
        $dish = Dish::find($id);
        if($dish)
        {
            return response()->json($dish, 200);
        }
        else
        {
            return response()->json([
                'message' => 'Блюдо не найдено'
            ], 404);
        }
    }
    public function edit($id)
    {
        $this->authorize('update', Dish::class);
        $dish = Dish::find($id);
        if($dish)
        {
            return response()->json($dish,200);
        }
        else
        {
            return response()->json([
                'message' => 'Блюдо не найдено'
            ], 404);
        }
    }
    public function update(UpdateRequest $request, int $id)
    {
        $this->authorize('update', Dish::class);
        $data = $request->validated();
        $dish = Dish::find($id);
        if ($dish)
        {
            if ($request->hasFile('img'))
            {
                $type = "dish";
                $data['img'] = $this->service->updateImage($request, $dish['img'], $type);
            }
            $dish->update($data);
            return response()->json([
                'message' => 'Категория меню успешно обновлена'
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Блюдо не найдено'
            ], 404);
        }
    }
    public function destroy($id)
    {
        $this->authorize('delete', Dish::class);
        $dish = Dish::find($id);
        if($dish)
        {
            $this->service->deleteImage($dish['img']);
            $dish->delete();
            return response()->json([
                'message' => 'Блюдо успешно удалено'
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Блюдо не найдено'
            ], 404);
        }
    }
    public function menu(Request $request)
    {
        $menu = Category::all()->each->dishes;
        return response()->json([
            'categories_dishes' => $menu
        ], 200);
    }
}
