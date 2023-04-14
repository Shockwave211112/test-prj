<?php

namespace App\Http\Controllers;

use App\Http\Filters\OrderFilter;
use App\Http\Requests\Order\AddDishRequest;
use App\Http\Requests\Order\FilterRequest;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Requests\Order\UpdateDishRequest;
use App\Http\Requests\Order\UpdateRequest;
use App\Models\Dish;
use App\Models\DishOrder;
use App\Models\Order;
use App\Models\User;
use App\Services\ImgService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(FilterRequest $request)
    {
        $this->authorize('view', auth()->user());
        $data = $request->validated();
        $filter = app()->make(OrderFilter::class, ['queryParams' => array_filter($data)]);
        $query = Order::filter($filter);

        if ($request->sort == null) {
            $sort = 'asc';
        }
        else
        {
            $sort = $request->sort;
        }
        switch($request->orderBy)
        {
            case 'number':
                $query->orderBy('number', $sort);
                break;
            case 'total_cost':
                $query->orderBy('total_cost', $sort);
                break;
            case 'closed_at':
                $query->orderBy('closed_at', $sort);
                break;
            case 'waiter':
                $query->orderBy('user_id', $sort);
                break;
        }
        if($request->waiter)
        {
            $query->where('user_id', '=',
                User::where('name',
                    'like',
                    "%{$request->waiter}%")->first()->id);
        }
        return $query->paginate(10);
    }

    public function store(StoreRequest $request)
    {
        $this->authorize('create', auth()->user());
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
    public function updateDish(UpdateDishRequest $request, $id)
    {
        $this->authorize('update', auth()->user());
        $data = $request->validated();
        $order = Order::find($id);
        if($order)
        {
            $dish = Dish::find($data['dish']);  //блюдо из запроса
            if(!$order->dishes()->find($data['dish']) && ($data['count'] < 0))
            {
                return response()->json([
                    "message" => "Блюдо в заказе не найдено"
                ], 404);
            }
            elseif(!$order->dishes()->find($data['dish']))  //добавление если такого блюда ещё нет
            {
                $order->dishes()->attach($order->id, ['dish_id' => $data['dish'], 'count' => $data['count']]);
                $total_price = $order->total_cost + $dish->price * $data['count'];
                $count = $order->count + $data['count'];
                $order->update(['count' => $count, 'total_cost' => $total_price]);
                return response()->json([
                    "message" => "Блюдо добавлено в заказ"
                ], 200);
            }
            else
            {   //изменение счетчика блюда в таблице DISH_ORDERS, если такое блюдо уже есть в заказе
                $dish_order = DishOrder::firstWhere([
                    ['dish_id', '=', $data['dish']],
                    ['order_id', '=', $order->id]
                ]);
                $total_price = $order->total_cost - $dish->price * $dish_order->count;
                $count = $order->count - $dish_order->count;
                $dish_order->count = $dish_order->count + $data['count'];
                if($dish_order->count <= 0)
                {
                    $order->dishes()->detach(['dish_id' => $data['dish']]);
                    $order->update(['count' => $count, 'total_cost' => $total_price]);
                    return response()->json([
                        'message' => 'Блюдо '.$dish->name .' удалено'
                    ], 200);
                }
                else
                {
                    $count = $order->count + $data['count'];
                    $total_price = $order->total_cost + $dish->price * $data['count'];
                    $dish_order->update($dish_order->attributesToArray());
                    $order->update(['count' => $count, 'total_cost' => $total_price]);
                    return response()->json([
                        'message' => 'Количество блюда '.$dish->name .' изменено'
                    ], 200);
                }
            }
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
        $this->authorize('update', auth()->user());
        $order = Order::find($id);
        if($order)
        {
            if($order->dishes()->find($dish_id) == null)
            {
                return response()->json([
                    "message" => "Блюдо в заказе не найдено"
                ], 404);
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
                'message' => 'Заказ не найден'
            ], 404);
        }
    }
    public function edit($id)
    {
        $this->authorize('update', auth()->user());
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
        $order = Order::firstWhere('number', $data['number']);
        if ($order)
        {
            if($data['is_closed'])
            {
                $data['closed_at'] = Carbon::now();
            }
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
