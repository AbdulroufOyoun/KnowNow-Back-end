<?php

namespace App\Interfaces;

interface PublicRepositoryInterface
{

    public function Create($model, $arr);

    public function Update($model, $id, $arr);

    public function ActiveOrNot($model, $id);

    public function ShowById($model, $id);

    public function ShowAll($model, $where);

    public function DeleteById($model, $id);
}
