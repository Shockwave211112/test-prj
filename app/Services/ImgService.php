<?php

namespace App\Services;

use App\Http\Requests\Category\StoreRequest;
use App\Models\Category;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class ImgService
{
    public function storeImage($request)
    {
        $data = $request->validated();
        if($request->hasFile('img'))
        {
            $image = $request->file('img');
            $path = Storage::putFile('img', new File($image));
        }
        $data['img'] = $path;
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
}
