<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CourseTest extends TestCase
{

    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and generate token
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user); // Crea una sesión activa
    }

    #[Test]
    public function test_se_puede_listar_cursos()
    {
        Course::factory()->count(5)->create();

        $response = $this->getJson('/api/courses');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data', 'message'])
            ->assertJsonCount(5, 'data');
    }

    #[Test]
    public function test_se_puede_crear_curso()
    {
        $data = [
            'title' => 'Primer BTI',
            'description' => 'Este es un curso',
            'start_date' =>  '2024-10-29',
            'end_date'   =>  '2025-10-29',
        ];

        $response = $this->postJson('/api/courses', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Primer BTI']);
        $this->assertDatabaseHas('courses', ['title' => 'Primer BTI']);
    }

    #[Test]
    public function test_se_puede_mostrar_curso()
    {
        $course = Course::factory()->create();

        $response = $this->getJson("/api/courses/$course->id");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $course->id]);
    }

    #[Test]
    public function test_se_puede_actualizar_un_curso()
    {
        $course = Course::factory()->create();

        $data = ['title' => 'Quinto Año'];

        $response = $this->putJson("/api/courses/$course->id", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Quinto Año']);
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'title' => 'Quinto Año']);
    }

    #[Test]
    public function test_se_puede_borrar_un_curso()
    {
        $course = Course::factory()->create();

        $response = $this->deleteJson("/api/courses/$course->id");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $course->id]);
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

}
