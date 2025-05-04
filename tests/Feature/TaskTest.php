<?php

namespace Tests\Feature;

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
}
