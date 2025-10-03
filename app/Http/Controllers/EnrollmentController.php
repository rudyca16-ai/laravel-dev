<?php

namespace App\Http\Controllers;

use App\Helpers\Logger;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollmentController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $enrollments = Enrollment::query()
            ->estudianteId($request->student_id)
            ->cursoId($request->course_id)
            ->with(['student:id,name,email', 'course:id,title,start_date,end_date']);

        if ($request->pagination)
            $enrollments = $enrollments->paginate($request->per_page ?? 10);
        else
            $enrollments = $enrollments->get();

        // Log
        Logger::info('Enrollment listed', $enrollments->toArray());

        return $this->sendResponse($enrollments, 'Lista de inscripciones obtenida con éxito');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Agrego la fecha de hoy, será la fecha de inscripción
            $request->request->add(['enrolled_at' => date('Y-m-d')]);
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'course_id' => 'required|exists:courses,id',
                'enrolled_at' => 'required|date_format:Y-m-d',
            ]);

            // Validar que no hayan duplicados en la bbdd
            if (Enrollment::where('student_id',$request->student_id)
                ->where('course_id',$request->course_id)
                ->exists()
            )
                return $this->sendError('El estudiante ya está inscrito a este curso.', [], 422);

            $enrollment = Enrollment::create($validated);

            // Log
            Logger::info('Enrollment created', $enrollment->toArray());

            DB::commit();
            return $this->sendResponse($enrollment, 'Inscripción creado con éxito', 201);

        } catch (ValidationException $e) {
            Logger::warning('Validation failed on creating enrollment', [
                'errors' => $e->errors()
            ]);
            DB::rollback();
            return $this->sendError('Validation Error', $e->errors(), 422);
        } catch (\Exception $e) {
            Logger::error('Error creating enrollment', [
                'message' => $e->getMessage(),
            ]);
            DB::rollback();
            return $this->sendError('Server Error', [$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollment $enrollment): JsonResponse
    {
        DB::beginTransaction();
        try {
            $enrollment->delete();

            Logger::info('Enrollment deleted', $enrollment->toArray());

            DB::commit();

            return $this->sendResponse($enrollment, 'Inscripción eliminada con éxito');
        } catch (\Exception $e) {
            Logger::error('Error deleting enrollment', [
                'enrollment_id' => $enrollment->id,
                'message'    => $e->getMessage(),
            ]);
            DB::rollback();
            return $this->sendError('Server Error', [$e->getMessage()], 500);
        }
    }
}
