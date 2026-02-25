<?php

use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a task can be created', function () {
    $task = Task::create([
        'title' => 'Test Task',
        'description' => null,
    ]);

    expect($task->title)->toBe('Test Task')
        ->and($task->description)->toBeNull()
        ->and($task->completed)->toBeFalse();

    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
        'description' => null,
    ]);
});

test('a task can be edited', function () {
    $task = Task::factory()->create([
        'title' => 'Original Title',
        'description' => null,
    ]);

    $task->update([
        'title' => 'Updated Title',
        'description' => 'Now it has a description',
        'completed' => true,
    ]);

    expect($task->title)->toBe('Updated Title')
        ->and($task->description)->toBe('Now it has a description')
        ->and($task->completed)->toBeTrue();

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Updated Title',
        'description' => 'Now it has a description',
        'completed' => 1,
    ]);
});

test('a task can be deleted', function () {
    $task = Task::factory()->create();

    $taskId = $task->id;
    $task->delete();

    $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
});

test('a task can belong to a category (read details)', function () {
    $category = Category::factory()->create(['name' => 'Work']);
    
    $task = Task::factory()->create([
        'category_id' => $category->id,
    ]);

    expect($task->category)->not->toBeNull()
        ->and($task->category->name)->toBe('Work');
});
