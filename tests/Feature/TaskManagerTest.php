<?php

use App\Livewire\TaskManager;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('renders successfully', function () {
    Livewire::test(TaskManager::class)
        ->assertStatus(200);
});

test('can create a task', function () {
    Livewire::test(TaskManager::class)
        ->set('title', 'New Livewire Task')
        ->set('description', 'Test Description')
        ->call('createTask');

    $this->assertDatabaseHas('tasks', [
        'title' => 'New Livewire Task',
        'description' => null, // Confirming our change that it ignores description on creation
        'completed' => 0,
    ]);
});

test('can filter tasks by pending or completed status', function () {
    Task::factory()->create(['title' => 'Task 1', 'completed' => false]);
    Task::factory()->create(['title' => 'Task 2', 'completed' => true]);

    Livewire::test(TaskManager::class)
        ->set('filter', 'pending')
        ->assertSee('Task 1')
        ->assertDontSee('Task 2');

    Livewire::test(TaskManager::class)
        ->set('filter', 'completed')
        ->assertSee('Task 2')
        ->assertDontSee('Task 1');
});
