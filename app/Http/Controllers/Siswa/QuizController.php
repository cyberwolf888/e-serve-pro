<?php

// FR-SW-05 / §3.2 / M5

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitQuizRequest;
use App\Models\Quiz;
use App\Services\QuizAttemptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function __construct(private QuizAttemptService $service) {}

    // 404 (not 403) for unpublished/closed/already-attempted/not-a-member quizzes — hides existence.
    public function show(Quiz $quiz): View
    {
        abort_unless(Auth::user()->can('take', $quiz), 404);

        return view('siswa.quizzes.show', ['quiz' => $quiz->load('questions.options')]);
    }

    public function submit(SubmitQuizRequest $request, Quiz $quiz): RedirectResponse
    {
        $attempt = $this->service->submit($quiz, $request->user(), $request->validated('answers'), $request);

        return to_route('siswa.classes.show', $quiz->class_id)
            ->with('success', "Kuis selesai. Skor Anda: {$attempt->score}");
    }
}
