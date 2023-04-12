<?php

namespace App\Http\Controllers;

use App\Http\Filters\UserFilter;
use App\Http\Requests\User\FilterRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index(FilterRequest $request)
    {
        $this->authorize('view', auth()->user());
        $data = $request->validated();
        $filter = app()->make(UserFilter::class, ['queryParams' => array_filter($data)]);
        $query = User::filter($filter);
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
            case 'email':
                $query->orderBy('email', $sort);
                break;
        }
        return $query->paginate(10);
    }

    public function store(StoreRequest $request)
    {
        $this->authorize('create', auth()->user());
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        if ($user)
        {
            return response()->json([
                'message' => 'Пользователь успешно добавлен'
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
        $user = User::find($id);
        if($user)
        {
            return response()->json($user, 200);
        }
        else
        {
            return response()->json([
                'message' => 'Пользователь не найден'
            ], 404);
        }
    }
    public function edit($id)
    {
        $this->authorize('view', auth()->user());
        $user = User::find($id);
        if($user)
        {
            return response()->json($user,200);
        }
        else
        {
            return response()->json([
                'message' => 'Пользователь не найден'
            ], 404);
        }
    }
    public function update(UpdateRequest $request, int $id)
    {
        $this->authorize('update', auth()->user());
        $data = $request->validated();
        $user = User::find($id);
        if($user)
        {
            $data['password'] = bcrypt($data['password']);
            $user->update($data);
            return response()->json([
                'message' => 'Пользователь успешно обновлён'
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Пользователь не найден'
            ], 404);
        }
    }
    public function destroy($id)
    {
        $this->authorize('delete', auth()->user());
        $user = User::find($id);
        if($user)
        {
            $user->delete();
            return response()->json([
                'message' => 'Пользователь успешно удалён'
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Пользователь не найден'
            ], 404);
        }
    }
    public function restore($id)
    {
        $this->authorize('restore', auth()->user());
        $user = User::withTrashed()->find($id);
        if($user)
        {
            $user->restore();
            return response()->json([
                'message' => 'Пользователь успешно восстановлен'
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'Пользователь не найден'
            ], 404);
        }
    }
}
