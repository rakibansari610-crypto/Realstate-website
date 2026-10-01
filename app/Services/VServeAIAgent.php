<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class VServeAIAgent
{
    /**
     * Main entrypoint:
     * - The LLM can reply normally OR call the search_properties tool
     * - Controller executes DB search, then we call LLM again with tool results
     */
    public function respond(string $userMessage, array $memory = []): array
    {
        // ✅ Hard fail early if API key missing (common on prod)
        $apiKey = config('openai.api_key') ?: env('OPENAI_API_KEY');
        if (empty($apiKey)) {
            Log::error('vServeAI: OPENAI_API_KEY missing');

            return [
                'type' => 'message',
                'reply' => 'AI is not configured (missing OPENAI_API_KEY). Please contact support.',
                'memory_append' => [
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ];
        }

        $system = <<<'SYS'
You are vServeAI, a warm, friendly real estate assistant for UAE 🇦🇪 and India 🇮🇳.

Behavior:
- Speak like a helpful agent (short, friendly).
- Ask ONE focused question at a time until you can shortlist.
- ONLY when ready, call the tool `search_properties`.
- Do not invent listings: if you did not call the tool, do not claim results.

Coverage:
- You support UAE and India.

Tool calling rules:
- Call tool when you have at least: (city/community) + max budget + property type + bedrooms + timing (ready/off_plan).
- If any missing, ask for it.

After tool results:
- Recommend top 3 with short bullet points and ask if user wants payment plan / availability / floor plans.
SYS;

        $messages = array_merge(
            [['role' => 'system', 'content' => $system]],
            $this->normalizeMemory($memory),
            [['role' => 'user', 'content' => $userMessage]]
        );

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4.1-mini',
                'messages' => $messages,
                'tools' => [
                    [
                        'type' => 'function',
                        'function' => [
                            'name' => 'search_properties',
                            'description' => 'Search UAE/India properties database and return matching projects',
                            'parameters' => [
                                'type' => 'object',
                                'properties' => [
                                    'city' => ['type' => 'string', 'nullable' => true],
                                    'location' => ['type' => 'string', 'nullable' => true],
                                    'max_price' => ['type' => 'number', 'nullable' => true, 'description' => 'Max budget'],
                                    'property_type' => ['type' => 'string', 'nullable' => true, 'description' => 'Apartment/Villa/Townhouse/Penthouse'],
                                    'bedrooms' => ['type' => 'integer', 'nullable' => true],
                                    'timing' => ['type' => 'string', 'nullable' => true, 'description' => 'ready or off_plan'],
                                    'wants_post_handover' => ['type' => 'boolean', 'nullable' => true],
                                    'max_booking_percent' => ['type' => 'number', 'nullable' => true],
                                    'plan_ratio' => ['type' => 'string', 'nullable' => true],
                                    'must_have_features' => ['type' => 'string', 'nullable' => true],
                                ],
                                'required' => [],
                            ],
                        ],
                    ],
                ],
                'tool_choice' => 'auto',
                'temperature' => 0.6,
            ]);

            // ✅ Support both object + array response shapes
            $choice = $response['choices'][0] ?? $response->choices[0] ?? null;

            $message = is_array($choice)
                ? ($choice['message'] ?? [])
                : ($choice?->message ?? null);

            $content = is_array($message)
                ? ($message['content'] ?? '')
                : ($message?->content ?? '');

            // ✅ Tool calls can appear as toolCalls (object SDK) OR tool_calls (array)
            $toolCalls = [];

            if (is_array($message)) {
                $toolCalls = $message['tool_calls'] ?? [];
            } else {
                $toolCalls = $message?->toolCalls ?? $message?->tool_calls ?? [];
            }

            // Normalize tool calls into array
            if ($toolCalls instanceof \Traversable) {
                $toolCalls = iterator_to_array($toolCalls);
            }
            if (! is_array($toolCalls)) {
                $toolCalls = [];
            }

            if (! empty($toolCalls)) {
                $firstCall = $toolCalls[0];

                // Read tool name + arguments (handle both shapes)
                $fn = is_array($firstCall)
                    ? ($firstCall['function'] ?? [])
                    : ($firstCall->function ?? null);

                $name = is_array($fn)
                    ? ($fn['name'] ?? null)
                    : ($fn?->name ?? null);

                $argsJson = is_array($fn)
                    ? ($fn['arguments'] ?? '{}')
                    : ($fn?->arguments ?? '{}');

                $args = json_decode($argsJson, true);
                if (! is_array($args)) {
                    $args = [];
                }

                return [
                    'type' => 'tool_call',
                    'tool_name' => $name,
                    'tool_args' => $args,
                    'assistant_message' => $content ?: 'Okay dear ✅ let me check options…',
                    'memory_append' => [
                        ['role' => 'user', 'content' => $userMessage],
                        ['role' => 'assistant', 'content' => $content ?: ''],
                    ],
                ];
            }

            return [
                'type' => 'message',
                'reply' => $content ?: 'Marhaba dear 😊 tell me city/community + budget + type.',
                'memory_append' => [
                    ['role' => 'user', 'content' => $userMessage],
                    ['role' => 'assistant', 'content' => $content ?: ''],
                ],
            ];
        } catch (\Throwable $e) {
            Log::error('vServeAI error', [
                'message' => $e->getMessage(),
                'class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'type' => 'message',
                'reply' => $e->getMessage().'Sorry dear — I had a small issue 😅 Can you repeat that in one line? (area + budget + type)',
                'memory_append' => [
                    ['role' => 'user', 'content' => $userMessage],
                    ['role' => 'assistant', 'content' => ''],
                ],
            ];
        }
    }

    /**
     * Second call: after DB results, send tool output to LLM and get final response.
     */
    public function respondWithToolResult(
        string $userMessage,
        array $memory,
        string $toolName,
        array $toolArgs,
        array $toolResultRows
    ): array {
        $system = 'You are vServeAI. Use tool results to recommend top 3 and ask if user wants details/payment plan.';

        $toolCallId = 'call_'.bin2hex(random_bytes(4));

        $messages = array_merge(
            [['role' => 'system', 'content' => $system]],
            $this->normalizeMemory($memory),
            [['role' => 'user', 'content' => $userMessage]],
            [
                [
                    'role' => 'assistant',
                    'content' => null,
                    'tool_calls' => [
                        [
                            'id' => $toolCallId,
                            'type' => 'function',
                            'function' => [
                                'name' => $toolName,
                                'arguments' => json_encode($toolArgs),
                            ],
                        ],
                    ],
                ],
                [
                    'role' => 'tool',
                    'tool_call_id' => $toolCallId,
                    'content' => json_encode([
                        'count' => count($toolResultRows),
                        'results' => $toolResultRows,
                    ], JSON_UNESCAPED_UNICODE),
                ],
            ]
        );

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4.1-mini',
                'messages' => $messages,
                'temperature' => 0.6,
            ]);

            $choice = $response['choices'][0] ?? $response->choices[0] ?? null;
            $message = is_array($choice)
                ? ($choice['message'] ?? [])
                : ($choice?->message ?? null);

            $content = is_array($message)
                ? ($message['content'] ?? '')
                : ($message?->content ?? '');

            return [
                'reply' => $content ?: 'Here are 3 nice options dear ✅',
                'memory_append' => [
                    ['role' => 'assistant', 'content' => $content ?: ''],
                ],
            ];
        } catch (\Throwable $e) {
            Log::error('vServeAI tool-followup error: '.$e->getMessage());

            return [
                'reply' => 'I found some options ✅ Pick one and I’ll share payment plan + availability.',
                'memory_append' => [
                    ['role' => 'assistant', 'content' => ''],
                ],
            ];
        }
    }

    private function normalizeMemory(array $memory): array
    {
        $memory = array_values(array_filter($memory, fn ($m) => is_array($m) && isset($m['role']) && array_key_exists('content', $m)
        ));

        return array_slice($memory, -12);
    }
}
