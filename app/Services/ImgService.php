<?php

namespace App\Services;

use App\Http\Requests\Category\StoreRequest;
use App\Models\Category;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class ImgService
{
    public function storeImage($data, $type)
    {
        if($data->hasFile('img'))
        {
            $image = $data->file('img');
            $path = Storage::putFile('img/'.$type, new File($image));
            return $path;
        }
        else
        {
            return response()->json([
                'message' => 'Ошибка, что-то пошло не так'
            ], 500);
        }
    }
    public function updateImage($newData, $oldData, $type)
    {
        if($newData->hasFile('img'))
        {
            $this->deleteImage($oldData);
            return $this->storeImage($newData, $type);
        }
        else
        {
            return response()->json([
                'message' => 'Ошибка, что-то пошло не так'
            ], 500);
        }
    }
    public function deleteImage($oldData)
    {
        Storage::delete($oldData);
    }
}
