<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return 'Tous les livres';
    }

    public function store()
    {
        $post = new Post();

        // $post = 
    }
}

