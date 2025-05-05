<?php

namespace App\Services\Integration;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    public function __construct(
        protected string $apiKey,
        protected string $apiUrl,
        protected string $model,
        protected float $temperature,
        protected int $maxTokens
    ) {
    }

    public function sendRequest(string $prompt): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an AI assistant that helps generate personalized learning recommendations for IT employees or students.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => $this->temperature,
                'max_tokens'  => $this->maxTokens,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return $data['choices'][0]['message']['content'] ?? null;
            } else {
                Log::error('OpenAI API Error', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('OpenAI API Exception: ' . $e->getMessage());
        }

        return null;
    }

    public function generateRecommendations(array $data): ?string
    {
        $prompt = $this->buildPromptFromTestResult($data);

        return $this->sendRequest($prompt);
    }

    protected function buildPromptFromTestResult(array $data): string
    {
        $prompt = "The user passed the test in category " . $data['category'] . " with the following results:\n";

        foreach ($data['questions'] as $question) {
            $questionId = $question['id'];
            $questionText = $question['question_text'];
            $correctAnswer = $question['correct_answer'];
            $userAnswer = $data['answers'][$questionId] ?? 'No answer provided';

            $prompt .= "{$questionId}. {$questionText}\n";
            $prompt .= "- User answer: {$userAnswer}\n";
            $prompt .= "- Correct answer: {$correctAnswer}\n\n";
        }

        $prompt .= "User Score: " . $data['score'] . " of max score: 20\n";
        $prompt .= "Based on this data, create detailed recommendations for training and skills development using Timebox method.";

        return $prompt;
    }
}
