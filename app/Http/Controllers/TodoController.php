<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todos =
            Auth::user()->todos()->latest()->get();
        // dd($todos);
        return Inertia::render('Todo/Index', [
            'todos' => $todos,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoRequest $request)
    {
        // $this->authorize('create', Auth::user());

        $validated = $request->validated();

        $validated['due_date'] = $request->due_date ? Carbon::parse($request->due_date)->format('Y-m-d') : null;

        // dd($validated);

        $request->user()->todos()->create($validated);

        return redirect(route('todos.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todo $todo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodoRequest $request, Todo $todo)
    // public function update(Request $request, Todo $todo)
    {
        // $this->authorize('update', $todo);

        $validated = $request->validated();

        // dump($request->description);
        // dump($request->completed);
        // dump($request->priority);
        // dd($request->due_date);

        $validated['due_date'] = $request->due_date ? Carbon::parse($request->due_date)->format('Y-m-d') : null;

        // dd($validated);

        $todo->update($validated);

        return redirect(route('todos.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        // $this->authorize('delete', $todo);

        $todo->delete();

        return redirect(route('todos.index'));
    }
}
