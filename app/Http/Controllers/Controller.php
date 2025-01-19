<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Controller
{
    protected function paginateArray(array $items, Request $request, $perPage = 10): LengthAwarePaginator
    {
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;

        $paginatedItems = array_slice($items, $offset, $perPage);

        return new LengthAwarePaginator(
            $paginatedItems,
            count($items),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );
    }
}
