<?php

use Stichoza\GoogleTranslate\GoogleTranslate;

use function PHPUnit\Framework\isList;

if (!function_exists('Success')) {
    function Success($message, $is_success = true)
    {
        return response()->json([
            'success' => $is_success,
            'message' => $message,
            'code' => 200,
            'data' => null,

        ], 200);
    }
}

if (!function_exists('SuccessData')) {
    function SuccessData($message, $data, $is_success = true)
    {
        // if ($lang) {
        //     $tr = new GoogleTranslate('ar');
        //     foreach ($data as $items) {
        //         try {
        //             $attributes = array_keys($items->toArray());
        //             foreach ($attributes as $item) {
        //                 if (is_object($items[$item])) {
        //                     foreach ($items as $object) {
        //                         $attributes = array_keys($object->toArray());
        //                         foreach ($attributes as $attribute) {
        //                             if ($attribute != 'id' && $object[$attribute] != null && $attribute != 'course_id') {
        //                                 $object[$attribute] = $tr->setSource('ar')->setTarget('en')->translate($object[$attribute]);
        //                             }
        //                         }
        //                     }
        //                 } else {
        //                     if ($item != 'id' && $items[$item] != null && $item != 'course_id') {
        //                         $items[$item] = $tr->setSource('ar')->setTarget('en')->translate($items[$item]);
        //                     }
        //                 }
        //             }
        //         } catch (\Throwable $th) {
        //         }
        //     }
        // }

        return response()->json([
            'success' => $is_success,
            'message' => $message,
            'code' => 200,
            'data' => $data,
        ], 200);
    }
}

if (!function_exists('Pagination')) {
    function Pagination($data)
    {
        $data = $data->toArray();
        // $tr = new GoogleTranslate('ar'); // Translates into English
        // foreach ($data as $items) {
        //     $attributes = array_keys($items->toArray());
        //     foreach ($attributes as $item) {
        //         if ($item != 'id' && $items[$item] != null) {
        //             $items[$item] = $tr->setSource('en')->setTarget('ar')->translate($items[$item]);
        //         }
        //     }
        // }
        return response()->json([
            'success' => true,
            'message' => 'Found Successfully',
            'per_page' => $data['per_page'],
            'total' => $data['total'],
            'current_page' => $data['current_page'],
            'last_page' => $data['last_page'],
            'data' => $data['data'],
        ], 200);
    }
}


if (!function_exists('uploadImage')) {
    function uploadImage($image, $path)
    {
        $name = $image->getClientOriginalName();
        $newName = rand(9999999999, 99999999999) . $name;
        $image->move(public_path($path), $newName);
        return  $newName;
    }
}


if (!function_exists('returnPerPage')) {
    function returnPerPage()
    {
        if (request()->hasHeader('perPage') && is_numeric(request()->header('perPage')) && request()->header('perPage') > 0) {
            $perPage = request()->header('perPage');
        } else {
            $perPage = 10;
        }
        return $perPage;
    }
}

if (!function_exists('CheckVideoOrImage')) {
    function CheckVideoOrImage($image)
    {
        if (!$image) {
            return null;
        }
        $image_extensions = ['.jpg', '.png', 'jpeg', '.gif', 'tiff', '.bmp', '.svg', 'webp', 'heic'];
        $extension = substr($image, -4);
        if (!in_array($extension, $image_extensions)) {
            return true;
        }
        return false;
    }
}
