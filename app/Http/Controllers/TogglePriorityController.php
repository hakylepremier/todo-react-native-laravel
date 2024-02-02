<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TogglePriorityController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Todo $todo)
    {
        $todo->update(['priority' => !$todo->priority]);

        return redirect(route('todos.index'));
    }
}
