@extends('layouts.crm', ['heading' => 'Tasks & Activities', 'subheading' => 'Assignments, due dates, reminders, and completion tracking.'])

@section('content')
    <div class="crm-grid-2">
        <section class="crm-card">
            <h2>{{ $task->exists ? 'Edit Task' : 'Add Task' }}</h2>
            <form class="crm-form" method="post" action="{{ $task->exists ? route('crm.tasks.update', $task) : route('crm.tasks.store') }}">
                @csrf
                @if($task->exists) @method('put') @endif
                <label><span>Title</span><input name="title" value="{{ old('title', $task->title) }}" required></label>
                <label><span>Description</span><textarea name="description">{{ old('description', $task->description) }}</textarea></label>
                <div class="crm-grid-2">
                    <label><span>Status</span><input name="status" value="{{ old('status', $task->status ?: 'open') }}"></label>
                    <label><span>Priority</span><input name="priority" value="{{ old('priority', $task->priority ?: 'medium') }}"></label>
                </div>
                <div class="crm-grid-2">
                    <label><span>Due At</span><input type="datetime-local" name="due_at" value="{{ old('due_at', optional($task->due_at)->format('Y-m-d\TH:i')) }}"></label>
                    <label><span>Reminder At</span><input type="datetime-local" name="reminder_at" value="{{ old('reminder_at', optional($task->reminder_at)->format('Y-m-d\TH:i')) }}"></label>
                </div>
                <label><span>Assigned To</span><select name="assigned_to"><option value="">None</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string) old('assigned_to', $task->assigned_to) === (string) $user->id)>{{ $user->name }}</option>@endforeach</select></label>
                <label><span>Completed At</span><input type="datetime-local" name="completed_at" value="{{ old('completed_at', optional($task->completed_at)->format('Y-m-d\TH:i')) }}"></label>
                <button class="crm-button-primary" type="submit">{{ $task->exists ? 'Update Task' : 'Create Task' }}</button>
            </form>
        </section>

        <section class="crm-card">
            <h2>Tasks</h2>
            <div class="crm-table-wrap">
                <table>
                    <thead><tr><th>Task</th><th>Assignee</th><th>Status</th><th>Due</th><th>Actions</th></tr></thead>
                    <tbody>
                    @foreach($tasks as $item)
                        <tr>
                            <td><strong>{{ $item->title }}</strong><div class="muted">{{ \Illuminate\Support\Str::limit($item->description, 60) }}</div></td>
                            <td>{{ $item->assignee?->name ?? 'Unassigned' }}</td>
                            <td><span class="crm-badge">{{ $item->status }} • {{ $item->priority }}</span></td>
                            <td>{{ optional($item->due_at)->format('d M Y H:i') ?: 'No due date' }}</td>
                            <td class="crm-actions">
                                <a class="crm-button crm-button-secondary" href="{{ route('crm.tasks.index', ['edit' => $item->id]) }}">Edit</a>
                                <form method="post" action="{{ route('crm.tasks.destroy', $item) }}">@csrf @method('delete')<button class="crm-button-danger" type="submit">Delete</button></form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:1rem;">{{ $tasks->links() }}</div>
        </section>
    </div>
@endsection
