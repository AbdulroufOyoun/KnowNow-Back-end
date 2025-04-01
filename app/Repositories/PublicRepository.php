<?php

namespace App\Repositories;

use App\Interfaces\PublicRepositoryInterface;


class PublicRepository  implements PublicRepositoryInterface
{

    public function Create($model, $arr)
    {
        // TODO: Implement Create() method.
        return $model::create($arr);
    }

    public function Update($model, $id, $arr)
    {
        // TODO: Implement Update() method.
        $model::find($id)->update($arr);
    }

    public function ActiveOrNot($model, $id)
    {
        // TODO: Implement ActiveOrNot() method.
        $object = $model::find($id);
        $object->is_active = !$object->is_active;
        $object->save();
    }

    public function ShowById($model, $id)
    {
        // TODO: Implement ShowById() method.
        return $model::find($id);
    }

    public function ShowAll($model, $where)
    {
        // TODO: Implement ShowAll() method.
        return $model::where($where);
    }

    public function DeleteById($model, $id)
    {
        // TODO: Implement DeleteById() method.
        return $model::find($id)->delete();
    }
}
