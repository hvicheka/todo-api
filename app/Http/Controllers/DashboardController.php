<?php

namespace App\Http\Controllers;

use App\Models\Todo;

class DashboardController extends Controller
{
    /**
     * Get Task Summary
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Todo::query()
            ->toBase()
            ->selectRaw("count(case when status = 'todo' then 1 end) as todo_count")
            ->selectRaw("count(case when status = 'progressing' then 1 end) as progressing_count")
            ->selectRaw("count(case when status = 'completed' then 1 end) as completed_count")
            ->first();
        return $this->apiResponse($data);
    }
}
