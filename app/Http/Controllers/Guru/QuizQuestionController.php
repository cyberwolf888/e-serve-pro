<?php

// FR-GR-09 / DATA-09 / DATA-10 / §3.2 / M5

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuizQuestionRequest;
use App\Http\Requests\UpdateQuizQuestionRequest;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\SchoolClass;
use App\Services\QuizQuestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuizQuestionController extends Controller
{
    public function __construct(private QuizQuestionService $service) {}

    public function create(SchoolClass $class, Quiz $quiz): View
    {
        $this->authorize('create', [QuizQuestion::class, $quiz]);

        return view('guru.quiz-questions.create', ['class' => $class, 'quiz' => $quiz]);
    }

    public function store(StoreQuizQuestionRequest $request, SchoolClass $class, Quiz $quiz): RedirectResponse
    {
        $this->service->create($quiz, $request->validated());

        return to_route('guru.classes.quizzes.show', [$class, $quiz])->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(SchoolClass $class, Quiz $quiz, QuizQuestion $question): View
    {
        $this->authorize('update', $question);

        return view('guru.quiz-questions.edit', [
            'class' => $class,
            'quiz' => $quiz,
            'question' => $question->load('options'),
        ]);
    }

    public function update(UpdateQuizQuestionRequest $request, SchoolClass $class, Quiz $quiz, QuizQuestion $question): RedirectResponse
    {
        $this->service->update($question, $request->validated());

        return to_route('guru.classes.quizzes.show', [$class, $quiz])->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class, Quiz $quiz, QuizQuestion $question): RedirectResponse
    {
        $this->authorize('delete', $question);
        $this->service->delete($question);

        return to_route('guru.classes.quizzes.show', [$class, $quiz])->with('success', 'Soal berhasil dihapus.');
    }
}
