<?php

namespace App\Http\Interfaces;

interface TableInterface
{
    public function getColumns();

    public function getRouteName();
}
