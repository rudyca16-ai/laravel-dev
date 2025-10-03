<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EnrollmentTest extends TestCase
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
    public function test_se_puede_listar_inscripciones()
    {
        Enrollment::factory()->count(5)->create();

        $response = $this->getJson('/api/enrollments');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data', 'message'])
            ->assertJsonCount(5, 'data');
    }

    #[Test]
    public function test_se_puede_crear_inscripcion()
    {
        // Create related models
        $student = Student::factory()->create();
        $course  = Course::factory()->create();

        $data = [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'enrolled_at' => '1990-01-01',
        ];

        $response = $this->postJson('/api/enrollments', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'student_id' => $student->id,
                'course_id' => $course->id,
            ]);
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_id'  => $course->id,
        ]);
    }

    #[Test]
    public function test_se_puede_borrar_una_inscripcion()
    {
        $enrollment = Enrollment::factory()->create();

        $response = $this->deleteJson("/api/enrollments/$enrollment->id");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $enrollment->id]);
        $this->assertDatabaseMissing('enrollments', ['id' => $enrollment->id]);
    }

}
