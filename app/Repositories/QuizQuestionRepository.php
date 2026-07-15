<?php

// DATA-09 / DATA-10 / FR-GR-09 / M5

namespace App\Repositories;

use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;

class QuizQuestionRepository
{
    public function nextOrder(Quiz $quiz): int
    {
        return ((int) $quiz->questions()->max('order')) + 1;
    }

    public function create(array $data): QuizQuestion
    {
        return QuizQuestion::create($data);
    }

    public function update(QuizQuestion $question, array $data): QuizQuestion
    {
        $question->update($data);

        return $question;
    }

    public function delete(QuizQuestion $question): void
    {
        $question->delete();
    }

    /** Insert options for a freshly created question. DATA-10 */
    public function createOptions(QuizQuestion $question, array $options): void
    {
        $question->options()->createMany($options);
    }

    /** Replace all options wholesale — simplest way to keep exactly-one-correct intact on edit. */
    public function replaceOptions(QuizQuestion $question, array $options): void
    {
        QuizOption::where('question_id', $question->id)->delete();
        $this->createOptions($question, $options);
    }
}
