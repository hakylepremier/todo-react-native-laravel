<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class ToggleCompletedController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Todo $todo)
    {
        $todo->update(['completed' => !$todo->completed]);

        return redirect(route('todos.index'));
    }
}
