<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Category;

class TaskManager extends Component
{
    public $tasks;
    public $filter = 'all';
    public $search = '';
    public $showModal = false;
    public $editingTask = null;
    public $title = '';
    public $description = '';
    public $category_id = '';
    public $categories;

    protected function rules()
    {
        return [
            'title' => 'required|min:3',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ];
    }

    public function mount()
    {
        $this->categories = Category::all();
        $this->loadTasks();
    }

    public function loadTasks()
    {
        $query = Task::with('category');

        if ($this->filter === 'pending') {
            $query->where('completed', false);
        } elseif ($this->filter === 'completed') {
            $query->where('completed', true);
        }

        if (!empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        $this->tasks = $query->latest()->get();
    }

    public function updatedFilter()
    {
        $this->loadTasks();
    }

    public function updatedSearch()
    {
        $this->loadTasks();
    }

    public function createTask()
    {
        $this->validate();

        Task::create([
            'title' => $this->title,
            'description' => $this->description ?: null,
            'category_id' => $this->category_id ?: null,
        ]);

        $this->reset(['title', 'description', 'category_id', 'showModal']);
        $this->loadTasks();
        session()->flash('message', 'Tarea creada correctamente.');
    }

    public function editTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        $this->editingTask = $task;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->category_id = $task->category_id;
        $this->showModal = true;
    }

    public function updateTask()
    {
        $this->validate();

        $this->editingTask->update([
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $this->category_id,
        ]);

        $this->reset(['title', 'description', 'category_id', 'editingTask', 'showModal']);
        $this->loadTasks();
        session()->flash('message', 'Tarea actualizada correctamente.');
    }

    public function deleteTask($taskId)
    {
        Task::destroy($taskId);
        $this->loadTasks();
        session()->flash('message', 'Tarea eliminada.');
    }

    public function toggleCompleted($taskId)
    {
        $task = Task::find($taskId);
        $task->completed = !$task->completed;
        $task->save();
        $this->loadTasks();
    }

    public function render()
    {
        return view('livewire.task-manager')->layout('layouts.app');
    }
}
