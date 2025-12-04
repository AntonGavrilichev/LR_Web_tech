<?php
class ResultsVerification extends CustomFormValidation {
    private $correctAnswers = [
        'q1' => '1',
        'q2' => '2',
        'q3' => '3'
    ];

    public function checkAnswers($userAnswers) {
        $results = [
            'total' => count($this->correctAnswers),
            'correct' => 0,
            'details' => []
        ];

        foreach ($this->correctAnswers as $question => $correctAnswer) {
            $userAnswer = $userAnswers[$question] ?? null;
            $isCorrect = ($userAnswer == $correctAnswer);

            $results['details'][$question] = [
                'user' => $userAnswer,
                'correct' => $correctAnswer,
                'is_correct' => $isCorrect
            ];

            if ($isCorrect) {
                $results['correct']++;
            }
        }

        $results['percentage'] = round(($results['correct'] / $results['total']) * 100, 2);

        return $results;
    }
}
?>