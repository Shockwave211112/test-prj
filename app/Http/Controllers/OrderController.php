<?php

namespace App\Http\Controllers;

use App\Http\Filters\OrderFilter;
use App\Http\Requests\Order\AddDishRequest;
use App\Http\Requests\Order\DelDishRequest;
use App\Http\Requests\Order\FilterRequest;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Requests\Order\UpdateRequest;
use App\Models\Dish;
use App\Models\DishOrder;
use App\Models\Order;
use App\Services\ImgService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

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
    public function addDish(AddDishRequest $request)
    {
        $this->authorize('view', auth()->user());
        $data = $request->validated();
        $order = Order::firstWhere('number', $data['number']);
        if($order)
        {
            $count = $order->count + $data['count'];    //счетчик всего заказа
            $total_price = $order->total_cost + Dish::find($data['dish'])->price;
            if(!$order->dishes()->find($data['dish']))  //добавление если такого блюда ещё нет
            {
                $order->dishes()->attach($order->id, ['dish_id' => $data['dish'], 'count' => $data['count']]);
            }
            else
            {   //изменение счетчика блюда в таблице DISH_ORDERS, если такое блюдо уже есть в заказе
                $dish_order_count = DishOrder::firstWhere([
                    ['dish_id', '=', $data['dish']],
                    ['order_id', '=', $order->id]
                ]);
                $dish_order_count->count = $dish_order_count->count + $data['count'];
                $dish_order_count->update($dish_order_count->attributesToArray());
            }
            $order->update(['count' => $count, 'total_cost' => $total_price]);

            return response()->json([
                "message" => "Блюдо добавлено в заказ"
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Заказ не найден'
            ], 404);
        }
    }
    public function delDish(Request $request, $id, $dish_id)
    {
        $this->authorize('view', auth()->user());
        $order = Order::find($id);
        if($order)
        {
            if($order->dishes()->find($dish_id) == null)
            {
                return response()->json([
                    "message" => "Блюдо в заказе не найдено"
                ], 200);
            }
            $newCount = $order->count - $order->dishes()->find($dish_id)->pivot->count;
            $newTotalCost = $order->total_cost - Dish::find($dish_id)->price;
            $order->dishes()->detach($dish_id);
            $order->update(['total_cost' => $newTotalCost, 'count' => $newCount]);

            return response()->json([
                "message" => "Блюдо удалено из заказа"
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Заказ не найден'
            ], 404);
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
        $this->authorize('view', auth()->user());
        $data = $request->validated();
        $order = Order::firstWhere('number', $data['number']);
        if ($order)
        {
            $order->update($data);
            return response()->json([
                'message' => 'Состояние заказа успешно обновлено'
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Заказ не найден'
            ], 500);
        }
    }
    public function destroy($id)
    {
        $this->authorize('delete', auth()->user());
        $order = Order::find($id);
        if($order)
        {
            $order->delete();
            return response()->json([
                'message' => 'Заказ успешно удалён'
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Заказ не найден'
            ], 404);
        }
    }
}
