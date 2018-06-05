<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Page as PageResource;

class PagesController extends Controller
{
    public function get($alias) {

        $item = \App\MenuItem::whereAlias($alias)->first();

        if (!$item) {
            return response()->json('not_found', 404);
        }

        $pages = $item->pages()
            ->orderBy('order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        return response()->json(PageResource::collection($pages));
    }

    public function agreement() {

        $page = \App\Page::whereName('Пользовательское соглашение')->first();

        return response($page->content);
    }
}
