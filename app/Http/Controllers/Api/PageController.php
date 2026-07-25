<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::all();
        if ($pages->isEmpty()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'No pages found.',
                ],
                404,
            );
        }
        return response()->json(
            [
                'success' => true,
                'message' => 'Pages retrieved successfully.',
                'data' => $pages,
            ],
            200,
        );
    }

    public function show($id)
    {
        $page = Page::wherePageId($id)->first();
        if (is_null($page)) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Page not found.',
                    'data' => [],
                ],
                404,
            );
        }
        return response()->json(
            [
                'success' => true,
                'message' => 'Page retrieved successfully.',
                'data' => $page,
            ],
            200,
        );
    }

    public function showBySlug($slug)
    {
        $page = Page::whereSlug($slug)->first();
        if (is_null($page)) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Page not found with the given slug.',
                ],
                404,
            );
        }
        return response()->json(
            [
                'success' => true,
                'message' => 'Page retrieved successfully by slug.',
                'data' => $page,
            ],
            200,
        );
    }
}
