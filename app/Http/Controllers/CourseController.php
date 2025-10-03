<?php

namespace App\Http\Controllers;

use App\Helpers\Logger;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CourseController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $courses = Course::query()->title($request->title)->orderBy('title');

        if ($request->pagination)
            $courses = $courses->paginate($request->per_page ?? 10);
        else
            $courses = $courses->get();

        // Log
        Logger::info('Course listed', $courses->toArray());

        return $this->sendResponse($courses, 'Lista de cursos obtenida con éxito');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            $validated = $request->validate([
                'title' => 'required',
                'description' => 'required',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after:start_date',
            ]);

            $course = Course::create($validated);

            // Log
            Logger::info('Course created', $course->toArray());

            DB::commit();
            return $this->sendResponse($course, 'Curso creado con éxito', 201);

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
    public function show(Course $course): JsonResponse
    {
        // Log
        Logger::info('Course showed', $course->toArray());

        return $this->sendResponse($course, 'Curso encontrado');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'title' => 'sometimes',
                'description' => 'sometimes',
                'start_date' => 'sometimes|date_format:Y-m-d',
                'end_date' => 'sometimes|date_format:Y-m-d|after:start_date',
            ]);

            $course->update($validated);

            // Log
            Logger::info('Course updated', $validated);

            DB::commit();
            return $this->sendResponse($course, 'Curso actualizado con éxito');

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
    public function destroy(Course $course): JsonResponse
    {
        DB::beginTransaction();
        try {
            $course->delete();

            Logger::info('Course deleted', $course->toArray());

            DB::commit();

            return $this->sendResponse($course, 'Curso eliminado con éxito');
        } catch (\Exception $e) {
            Logger::error('Error deleting student', [
                'course_id' => $course->id,
                'message'    => $e->getMessage(),
            ]);
            DB::rollback();
            return $this->sendError('Server Error', [$e->getMessage()], 500);
        }
    }
}
