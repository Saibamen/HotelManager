<?php

namespace App\Http\Interfaces;

interface ManageTableInterface
{
    public function delete($objectId);

    public function getFields();
}
