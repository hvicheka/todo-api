<?php

namespace App\Http\Controllers;

use App\Http\Requests\Todo\TodoFilterRequest;
use App\Http\Requests\Todo\TodoRequest;
use App\Models\Todo;
use App\Http\Resources\Todo\TodoResource;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param  \App\Http\Requests\Todo\TodoFilterRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TodoFilterRequest $request)
    {
        $todos = Todo::query()
            ->when($request->status, function ($query) use($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->q, function($query) use($request){
                return $query->search($request->q);
            })
            ->latest('id')
            ->paginate();
        $response = TodoResource::collection($todos);
        return $this->apiResponse($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TodoRequest $request)
    {
        $todo = Todo::create($request->validated());
        $response = new TodoResource($todo);
        return $this->respondCreated($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Todo $todo)
    {
        $response = new TodoResource($todo);
        return $this->apiResponse($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TodoRequest $request, Todo $todo)
    {
        $todo->update($request->validated());
        $response = new TodoResource($todo);
        return $this->apiResponse($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();
        return response()->noContent();
    }
}
