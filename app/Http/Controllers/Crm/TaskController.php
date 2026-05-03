<?php

namespace App\Http\Controllers\Crm;

use App\Crm\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends BaseCrmController
{
    public function index(Request $request): View
    {
        $task = $request->query('edit')
            ? Task::findOrFail((int) $request->query('edit'))
            : new Task(['status' => 'open', 'priority' => 'medium']);

        $tasks = Task::with(['assignee', 'creator'])->latest()->paginate(12);

        return $this->crmView('crm.tasks.index', compact('task', 'tasks') + [
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Task::create($this->validated($request) + ['created_by' => $request->user()->id]);

        return back()->with('status', 'Task saved.');
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $task->update($this->validated($request));

        return redirect()->route('crm.tasks.index')->with('status', 'Task updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return back()->with('status', 'Task deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'max:40'],
            'priority' => ['required', 'string', 'max:30'],
            'due_at' => ['nullable', 'date'],
            'reminder_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);
    }
}
