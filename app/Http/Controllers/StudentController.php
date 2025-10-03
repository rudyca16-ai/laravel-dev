<?php

namespace App\Http\Controllers;

use App\Helpers\Logger;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $students = Student::query()->name($request->name)->orderBy('name');

        if ($request->pagination)
            $students = $students->paginate($request->per_page ?? 10);
        else
            $students = $students->get();

        // Log
        Logger::info('Student listed', $students->toArray());

        return $this->sendResponse($students, 'Lista de estudiantes obtenida con éxito');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            $validated = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:students,email',
                'birthdate' => 'required|date_format:Y-m-d',
                'nationality' => 'required|string',
            ]);

            $student = Student::create($validated);

            // Log
            Logger::info('Student created', $student->toArray());

            DB::commit();
            return $this->sendResponse($student, 'Estudiante creado con éxito', 201);

        } catch (ValidationException $e) {
            Logger::warning('Validation failed on creating student', [
                'errors' => $e->errors()
            ]);
            DB::rollback();
            return $this->sendError('Validation Error', $e->errors(), 422);
        } catch (\Exception $e) {
            Logger::error('Error creating student', [
                'message' => $e->getMessage(),
            ]);
            DB::rollback();
            return $this->sendError('Server Error', [$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): JsonResponse
    {
        // Log
        Logger::info('Student showed', $student->toArray());

        return $this->sendResponse($student, 'Estudiante encontrado');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'name'  => 'sometimes|string',
                'email' => 'sometimes|email|unique:students,email,' . $student->id,
                'birthdate' => 'sometimes|date_format:Y-m-d',
                'nationality' => 'sometimes|string',
            ]);

            $student->update($validated);

            // Log
            Logger::info('Student updated', $validated);

            DB::commit();
            return $this->sendResponse($student, 'Estudiante actualizado con éxito');

        } catch (ValidationException $e) {
            Logger::warning('Validation failed on updating student', [
                'errors' => $e->errors()
            ]);
            DB::rollback();
            return $this->sendError('Validation Error', $e->errors(), 422);
        } catch (\Exception $e) {
            Logger::error('Error updating student', [
                'message' => $e->getMessage(),
            ]);
            DB::rollback();
            return $this->sendError('Server Error', [$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student): JsonResponse
    {
        DB::beginTransaction();
        try {
            $student->delete();

            Logger::info('Student deleted', $student->toArray());

            DB::commit();

            return $this->sendResponse($student, 'Estudiante eliminado con éxito');
        } catch (\Exception $e) {
            Logger::error('Error deleting student', [
                'student_id' => $student->id,
                'message'    => $e->getMessage(),
            ]);
            DB::rollback();
            return $this->sendError('Server Error', [$e->getMessage()], 500);
        }
    }
}
