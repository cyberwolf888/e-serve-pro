<?php

// FR-GR-09 / DATA-09 / DATA-10 / M5

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Repositories\QuizQuestionRepository;

class QuizQuestionService
{
    public function __construct(private QuizQuestionRepository $repo) {}

    /**
     * $data: ['question_text' => string, 'options' => string[], 'correct_option' => int]
     * A radio-button "correct_option" index guarantees exactly one correct answer —
     * no separate "exactly one correct" validation needed.
     */
    public function create(Quiz $quiz, array $data): QuizQuestion
    {
        $question = $this->repo->create([
            'quiz_id' => $quiz->id,
            'question_text' => $data['question_text'],
            'order' => $this->repo->nextOrder($quiz),
        ]);

        $this->repo->createOptions($question, $this->buildOptions($data['options'], (int) $data['correct_option']));

        return $question;
    }

    public function update(QuizQuestion $question, array $data): QuizQuestion
    {
        $this->repo->update($question, ['question_text' => $data['question_text']]);
        $this->repo->replaceOptions($question, $this->buildOptions($data['options'], (int) $data['correct_option']));

        return $question;
    }

    public function delete(QuizQuestion $question): void
    {
        $this->repo->delete($question);
    }

    /** @param string[] $optionTexts */
    private function buildOptions(array $optionTexts, int $correctIndex): array
    {
        return collect($optionTexts)->values()->map(fn (string $text, int $index) => [
            'option_text' => $text,
            'is_correct' => $index === $correctIndex,
            'label' => chr(65 + $index),
        ])->all();
    }
}
