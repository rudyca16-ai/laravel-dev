<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StudentTest extends TestCase
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
    public function test_se_puede_listar_estudiantes()
    {
        Student::factory()->count(5)->create();

        $response = $this->getJson('/api/students');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data', 'message'])
            ->assertJsonCount(5, 'data');
    }

    #[Test]
    public function test_se_puede_crear_estudiante()
    {
        $data = [
            'name' => 'Rudy',
            'email' => 'rudy@example.com',
            'birthdate' => '1990-01-01',
            'nationality' => 'Paraguaya',
        ];

        $response = $this->postJson('/api/students', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Rudy']);
        $this->assertDatabaseHas('students', ['email' => 'rudy@example.com']);
    }

    #[Test]
    public function test_se_puede_mostrar_estudiante()
    {
        $student = Student::factory()->create();

        $response = $this->getJson("/api/students/$student->id");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $student->id]);
    }

    #[Test]
    public function test_se_puede_actualizar_un_estudiante()
    {
        $student = Student::factory()->create();

        $data = ['name' => 'Nombre Nuevo'];

        $response = $this->putJson("/api/students/$student->id", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Nombre Nuevo']);
        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => 'Nombre Nuevo']);
    }

    #[Test]
    public function test_se_puede_borrar_un_estudiante()
    {
        $student = Student::factory()->create();

        $response = $this->deleteJson("/api/students/$student->id");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $student->id]);
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

}
