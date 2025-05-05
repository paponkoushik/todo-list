<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_tasks()
    {
        Task::factory()->count(15)->create();

        $response = $this->getJson('/api/tasks?page=1');

        $response->assertOk()
                ->assertJsonStructure([
                    'current_page',
                    'data',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ]);

        $this->assertCount(10, $response->json('data'));
    }

    public function test_it_returns_remaining_tasks_on_second_page()
    {
        Task::factory()->count(15)->create();

        $response = $this->getJson('/api/tasks?page=2');

        $response->assertOk()
                ->assertJsonStructure([
                    'current_page',
                    'data',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ])
                ->assertJson([
                    'current_page' => 2
                ]);

        $this->assertCount(5, $response->json('data'));
    }

    public function test_it_shows_a_specific_task()
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response
            ->assertOk()
            ->assertJsonFragment([
                'title' => $task->title,
                'body' => $task->body,
            ]);
    }

    public function test_it_creates_task_with_valid_data()
    {
        $data = ['title' => 'Test Task', 'body' => 'Test body'];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertCreated()
                 ->assertJsonFragment($data);

        $this->assertDatabaseHas('tasks', $data);
    }

    public function test_it_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/tasks', []);

        $response->assertStatus(422)->assertJsonValidationErrors(['title', 'body']);
    }

    public function test_it_updates_task_with_valid_data()
    {
        $task = Task::create([
            'title' => 'Initial Title',
            'body'  => 'Initial Body',
        ]);

        $data = [
            'title' => 'Updated Title',
            'body'  => 'Updated Body',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $data);

        $response->assertOk()
                ->assertJsonFragment($data);

        $this->assertDatabaseHas('tasks', $data);
    }

    public function test_it_fails_to_update_with_invalid_data()
    {
        $task = Task::create([
            'title' => 'Initial Title',
            'body'  => 'Initial Body',
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'body']);
    }

    public function test_it_soft_deletes_a_task()
    {
        $task = Task::factory()->create();
    
        $response = $this->deleteJson("/api/tasks/{$task->id}");
    
        $response->assertOk()
                 ->assertJson(['message' => 'Task deleted successfully.']);
    
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }    

}
