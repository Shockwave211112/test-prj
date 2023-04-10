<?php

namespace App\Http\Controllers;

use App\Http\Filters\OrderFilter;
use App\Http\Requests\Order\FilterRequest;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Requests\Order\UpdateRequest;
use App\Models\Dish;
use App\Models\Order;
use App\Services\ImgService;

class OrderController extends Controller
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
        $filter = app()->make(OrderFilter::class, ['queryParams' => array_filter($data)]);
        $query = Order::filter($filter);

        if ($request->sort == null) {
            $sort = 'asc';
        } else {
            $sort = $request->sort;
        }
        if(str_contains($request->getQueryString(), "orderBy=number"))
        {
            $query->orderBy('number', $sort);
        }
        elseif(str_contains($request->getQueryString(), "orderBy=total_cost"))
        {
            $query->orderBy('total_cost', $sort);
        }
        elseif(str_contains($request->getQueryString(), "orderBy=closing_date"))
        {
            $query->orderBy('closing_date', $sort);
        }
//        elseif(str_contains($request->getQueryString(), "orderBy=waiter"))
//        {
//           $query->orderBy($query->user->name, $sort);
//        }
        return $query->paginate(10);
    }

    public function store(StoreRequest $request)
    {
        $this->authorize('view', auth()->user());
        $data = $request->validated();
        $order = Order::create($data);
        if ($order)
        {
            return response()->json([
                'message' => 'Заказ успешно добавлен'
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
        $this->authorize('view', auth()->user());
        $order = Order::find($id);
        if($order)
        {
            return response()->json([
                'order' => $order,
                'dishes' => $order->dishes,
                'waiter' => $order->user->name
            ], 200);
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
        $this->authorize('view', auth()->user());
        $order = Order::find($id);
        if($order)
        {
            return response()->json([
                'order' => $order,
                'dishes' => $order->dishes,
                'waiter' => $order->user->name
            ], 200);
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
        $this->authorize('update', auth()->user());
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
        $this->authorize('delete', auth()->user());
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
}
