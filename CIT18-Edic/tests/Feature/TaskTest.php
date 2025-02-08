<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
class TaskTest extends TestCase
{
    use RefreshDatabase;
    public function test_can_create_task()
    {
        $response = $this->postJson('/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'is_completed' => false
        ]);
        $response->assertStatus(201)
                ->assertJson([
                    'title' => 'Test Task',
                    'description' => 'Test Description',
                    'is_completed' => false
                ]);
    }
    public function test_can_list_tasks()
    {
        Task::create([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'is_completed' => false
        ]);
        $response = $this->getJson('/tasks');
        $response->assertStatus(200)
                ->assertJsonCount(1);
    }
    public function test_can_show_task()
    {
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'is_completed' => false
        ]);
        $response = $this->getJson("/tasks/{$task->id}");
        $response->assertStatus(200)
                ->assertJson([
                    'title' => 'Test Task',
                    'description' => 'Test Description'
                ]);
    }
    public function test_can_update_task()
    {
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'is_completed' => false
        ]);
        $response = $this->putJson("/tasks/{$task->id}", [
            'title' => 'Updated Task',
            'is_completed' => true
        ]);
        $response->assertStatus(200)
                ->assertJson([
                    'title' => 'Updated Task',
                    'is_completed' => true
                ]);
    }
    public function test_can_delete_task()
    {
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'is_completed' => false
        ]);
        $response = $this->deleteJson("/tasks/{$task->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}