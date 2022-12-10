<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = Todo::query()
            ->toBase()
            ->selectRaw("count(case when status = 'todo' then 1 end) as todo_count")
            ->selectRaw("count(case when status = 'progressing' then 1 end) as progressing_count")
            ->selectRaw("count(case when status = 'completed' then 1 end) as completed_count")
            ->first();
        return response()->json([
            'data' => $data
        ]);
    }
}
