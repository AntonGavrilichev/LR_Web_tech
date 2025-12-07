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

    public function saveToDatabase($fullName, $userAnswers, $results) {
        require_once 'config/database.php';

        $answersJson = json_encode($userAnswers);
        $correctJson = json_encode($this->correctAnswers);

        $db = Database::getConnection();

        // Преобразуем результаты в формат для БД
        $allCorrect = true;
        foreach ($results['details'] as $detail) {
            if (!$detail['is_correct']) {
                $allCorrect = false;
                break;
            }
        }

        $isCorrectOverall = $allCorrect ? 1 : 0;

        try {
            $stmt = $db->prepare("INSERT INTO test_results (full_name, user_answers, correct_answers, is_correct, score, total_questions, correct_count) 
                                  VALUES (:full_name, :user_answers, :correct_answers, :is_correct, :score, :total, :correct)");

            $stmt->execute([
                ':full_name' => $fullName,
                ':user_answers' => $answersJson,
                ':correct_answers' => $correctJson,
                ':is_correct' => $isCorrectOverall,
                ':score' => $results['percentage'],
                ':total' => $results['total'],
                ':correct' => $results['correct']
            ]);

            return $db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Ошибка сохранения в БД: " . $e->getMessage());
            return false;
        }
    }
}
?>