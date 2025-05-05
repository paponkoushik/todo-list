<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

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
}
