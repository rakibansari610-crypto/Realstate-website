<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\ProjectSearchIndex;
use App\Services\VServeAIAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AiChatController extends Controller
{
    public function page()
    {
        $this->resetStateIfMissing();

        return view('ai.chat');
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'project_id' => ['nullable', 'integer'],
            'budget_max' => ['nullable', 'numeric'],
            'preferred_bhk' => ['nullable', 'string', 'max:20'],
            'property_type' => ['nullable', 'string', 'max:255'],
            'property_build' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($v->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $v->errors(),
            ], 422);
        }

        $lastLeadNo = Lead::max('lead_no');
        $lead_no = $lastLeadNo ? $lastLeadNo + 1 : 1001;

        try {
            $lead = Lead::create([
                'org_id' => null,
                'lead_no' => $lead_no,

                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),

                'source' => 'vServeAI',
                'status' => 'new',

                'project_id' => $request->input('project_id'),
                'preferred_bhk' => $request->input('preferred_bhk'),
                'budget_max' => $request->input('budget_max'),

                'property_type' => $request->input('property_type'),
                'property_build' => $request->input('property_build'),

                'notes' => $request->input('notes'),
            ]);

            return response()->json([
                'ok' => true,
                'lead_id' => $lead->id,
            ]);
        } catch (\Throwable $e) {
            \Log::error('ai.chat.lead store failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);

            $debug = app()->environment(['local', 'development']);

            return response()->json([
                'ok' => false,
                'message' => 'Lead save failed',
                'error' => $debug ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function message(Request $request, VServeAIAgent $agent)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $msg = trim((string) $request->input('message'));
        $lower = Str::lower($msg);

        // Reset
        if (in_array($lower, ['reset', 'restart', 'start over', 'clear'], true)) {
            session()->forget('vserveai_memory');
            session()->forget('vserveai_country');

            return response()->json([
                'ok' => true,
                'reply' => "Hello dear 👋 I’m **vServeAI**.\nI can help with **UAE 🇦🇪 and India 🇮🇳**.\nWhich one do you prefer — **UAE** or **India**?",
                'results' => [],
                'show_cards' => false,
                'suggest_capture' => false,
                'intent' => null,
            ]);
        }

        // Out-of-scope block
        if ($this->detectOutOfScopePlace($msg)) {
            return response()->json([
                'ok' => true,
                'reply' => "Dear 😊 For now I can help with **UAE 🇦🇪 and India 🇮🇳 only**.\nDo you want **UAE** or **India**? (and which city/community)",
                'results' => [],
                'show_cards' => false,
                'suggest_capture' => false,
                'intent' => null,
            ]);
        }

        // Country pick: store as 'ae' or 'in'
        $picked = $this->detectAllowedPlace($msg); // we will update detectAllowedPlace to return ae/in below
        if ($picked) {
            session()->put('vserveai_country', $picked);
        }

        // If country not selected yet and message looks generic: ask once
        $country = session('vserveai_country');
        if (! $country) {
            $looksGeneric = preg_match('/\b(apartment|flat|villa|townhouse|penthouse|studio|1\s*br|2\s*br|3\s*br|\d+\s*bhk|bhk|budget|under|below|max|ready|off[-\s]?plan)\b/i', $msg);
            if ($looksGeneric) {
                return response()->json([
                    'ok' => true,
                    'reply' => 'Nice ✅ Are you looking in **UAE 🇦🇪** or **India 🇮🇳**?',
                    'results' => [],
                    'show_cards' => false,
                    'suggest_capture' => false,
                    'intent' => null,
                ]);
            }
        }

        $memory = session('vserveai_memory', []);

        // quick project match (optional)
        $maybeProject = $this->matchProjectByName($msg);
        if ($maybeProject && $this->isAllowedRow($maybeProject)) {
            return response()->json([
                'ok' => true,
                'reply' => "Sure dear ✅ Here’s **{$maybeProject->name}**.\nWant payment plan / availability / floor plans?",
                'results' => [[
                    'project_id' => $maybeProject->project_id,
                    'name' => $maybeProject->name,
                    'city' => $maybeProject->city,
                    'location' => $maybeProject->location,
                    'min_price' => $maybeProject->min_price,
                    'currency' => $maybeProject->currency,
                ]],
                'show_cards' => true,
                'suggest_capture' => true,
                'intent' => ['filters' => []],
            ]);
        }

        // 1) Ask LLM
        $first = $agent->respond($msg, $memory);

        // Save memory
        $memory = array_merge($memory, $first['memory_append'] ?? []);
        session()->put('vserveai_memory', $memory);

        // Normal message
        if (($first['type'] ?? 'message') === 'message') {
            return response()->json([
                'ok' => true,
                'reply' => $first['reply'] ?? 'Okay dear ✅',
                'results' => [],
                'show_cards' => false,
                'suggest_capture' => false,
                'intent' => null,
            ]);
        }

        // Tool call
        $toolName = $first['tool_name'] ?? null;
        $toolArgs = $first['tool_args'] ?? [];

        // If tool mismatch in prod, fail gracefully
        if ($toolName !== 'search_properties') {
            \Log::warning('AI tool mismatch', ['tool_name' => $toolName, 'first' => $first]);

            return response()->json([
                'ok' => true,
                'reply' => $first['assistant_message'] ?? $first['reply'] ?? 'Sorry dear 😅 Can you repeat that in one line? (area + budget + type)',
                'results' => [],
                'show_cards' => false,
                'suggest_capture' => false,
                'intent' => null,
            ]);
        }

        // 2) Run DB search
        $results = $this->runSearch($toolArgs);

        $results = collect($results)
            ->filter(fn ($r) => $this->isAllowedRow($r)) // keeps UAE/India only
            ->take(10)
            ->values();

        $rows = $results->map(fn ($r) => [
            'project_id' => $r->project_id,
            'name' => $r->name,
            'city' => $r->city,
            'location' => $r->location,
            'min_price' => $r->min_price,
            'currency' => $r->currency,
        ])->all();

        // 3) Ask LLM to format results
        $final = $agent->respondWithToolResult($msg, $memory, $toolName, $toolArgs, $rows);

        $memory = array_merge($memory, $final['memory_append'] ?? []);
        session()->put('vserveai_memory', $memory);

        return response()->json([
            'ok' => true,
            'reply' => $final['reply'] ?? 'Okay dear ✅',
            'results' => $rows,
            'show_cards' => true,
            'suggest_capture' => true,
            'intent' => ['filters' => $toolArgs],
        ]);
    }

    /**
     * ✅ Supports multiple agent response shapes (local/prod differences)
     */
    private function normalizeAgentResponse(array $firstRaw): array
    {
        $type = $firstRaw['type'] ?? null;

        // If agent returns plain reply only
        if (! $type && isset($firstRaw['reply'])) {
            return [
                'type' => 'message',
                'reply' => (string) $firstRaw['reply'],
                'tool_name' => null,
                'tool_args' => [],
            ];
        }

        if ($type === 'message') {
            return [
                'type' => 'message',
                'reply' => (string) ($firstRaw['reply'] ?? ''),
                'tool_name' => null,
                'tool_args' => [],
            ];
        }

        // Common format
        if (! empty($firstRaw['tool_name'])) {
            return [
                'type' => 'tool',
                'reply' => (string) ($firstRaw['reply'] ?? ''),
                'tool_name' => (string) $firstRaw['tool_name'],
                'tool_args' => is_array($firstRaw['tool_args'] ?? null) ? ($firstRaw['tool_args'] ?? []) : [],
            ];
        }

        // OpenAI-style: tool_calls[0].function.name + arguments (JSON string)
        if (! empty($firstRaw['tool_calls']) && is_array($firstRaw['tool_calls'])) {
            $call = $firstRaw['tool_calls'][0] ?? null;
            $fn = $call['function'] ?? null;

            $name = $fn['name'] ?? ($call['name'] ?? null);
            $args = $fn['arguments'] ?? ($call['arguments'] ?? null);

            $decoded = [];
            if (is_string($args)) {
                $try = json_decode($args, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($try)) {
                    $decoded = $try;
                }
            } elseif (is_array($args)) {
                $decoded = $args;
            }

            return [
                'type' => 'tool',
                'reply' => (string) ($firstRaw['reply'] ?? ''),
                'tool_name' => $name ? (string) $name : null,
                'tool_args' => $decoded,
            ];
        }

        // Some agents return function_call
        if (! empty($firstRaw['function_call']) && is_array($firstRaw['function_call'])) {
            $name = $firstRaw['function_call']['name'] ?? null;
            $args = $firstRaw['function_call']['arguments'] ?? null;

            $decoded = [];
            if (is_string($args)) {
                $try = json_decode($args, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($try)) {
                    $decoded = $try;
                }
            } elseif (is_array($args)) {
                $decoded = $args;
            }

            return [
                'type' => 'tool',
                'reply' => (string) ($firstRaw['reply'] ?? ''),
                'tool_name' => $name ? (string) $name : null,
                'tool_args' => $decoded,
            ];
        }

        // Fallback: treat as message if reply exists
        if (isset($firstRaw['reply'])) {
            return [
                'type' => 'message',
                'reply' => (string) $firstRaw['reply'],
                'tool_name' => null,
                'tool_args' => [],
            ];
        }

        return [
            'type' => 'message',
            'reply' => '',
            'tool_name' => null,
            'tool_args' => [],
        ];
    }

    private function isAllowedRow($r): bool
    {
        $country = strtolower((string) ($r->country ?? ''));

        if ($country) {
            return in_array($country, ['uae', 'united arab emirates', 'india'], true);
        }

        $city = strtolower((string) ($r->city ?? ''));

        $uaeCities = [
            'dubai', 'abu dhabi', 'abudhabi', 'sharjah', 'ajman', 'ras al khaimah', 'rak', 'fujairah', 'umm al quwain', 'uaq', 'al ain',
        ];

        $indiaCities = [
            'delhi', 'new delhi', 'mumbai', 'bombay', 'bangalore', 'bengaluru', 'hyderabad', 'chennai', 'pune', 'ahmedabad', 'kolkata', 'gurgaon', 'gurugram', 'noida',
        ];

        return in_array($city, $uaeCities, true) || in_array($city, $indiaCities, true);
    }

    private function detectOutOfScopePlace(string $msg): ?string
    {
        $t = Str::lower($msg);

        // If the msg contains allowed place keywords, do not block.
        if ($this->detectAllowedPlace($msg)) {
            return null;
        }

        $blocked = [
            'uk', 'united kingdom', 'london', 'england', 'usa', 'united states', 'america', 'canada', 'australia', 'singapore',
            'pakistan', 'qatar', 'kuwait', 'saudi', 'saudi arabia', 'riyadh', 'oman', 'bahrain', 'turkey', 'germany', 'france', 'spain', 'italy',
        ];

        foreach ($blocked as $kw) {
            if (Str::contains($t, $kw)) {
                return $kw;
            }
        }

        return null;
    }

    private function detectAllowedPlace(string $msg): ?string
    {
        $t = Str::lower($msg);

        // UAE
        if (preg_match('/\b(ae|uae|united arab emirates|dubai|abu dhabi|abudhabi|sharjah|ajman|ras al khaimah|rak|fujairah|umm al quwain|uaq|al ain)\b/i', $t)) {
            return 'ae';
        }

        // India
        if (preg_match('/\b(in|india|delhi|new delhi|mumbai|bombay|bangalore|bengaluru|hyderabad|chennai|pune|ahmedabad|kolkata|gurgaon|gurugram|noida|kochi|cochin|jaipur|lucknow|indore|surat|nagpur)\b/i', $t)) {
            return 'in';
        }

        return null;
    }

    private function matchProjectByName(string $text): ?ProjectSearchIndex
    {
        $t = trim($text);
        $lower = Str::lower($t);

        $blocked = [
            'dubai', 'abu dhabi', 'abudhabi', 'sharjah', 'uae', 'india',
            'jvc', 'marina', 'downtown', 'business bay', 'palm',
            'buy', 'rent', 'apartment', 'villa', 'townhouse', 'ready', 'off plan', 'off-plan',
            '1m', '2m', '3m',
        ];

        if (mb_strlen($t) < 5) {
            return null;
        }
        if (in_array($lower, $blocked, true)) {
            return null;
        }
        if (count(preg_split('/\s+/', $t)) < 2) {
            return null;
        }
        if (! preg_match('/[a-z]/i', $t)) {
            return null;
        }

        return ProjectSearchIndex::query()
            ->where('name', 'like', '%'.$t.'%')
            ->orderByRaw('CASE WHEN name = ? THEN 0 ELSE 1 END', [$t])
            ->first();
    }

    private function runSearch(array $filters)
    {
        $q = ProjectSearchIndex::query();

        if (! empty($filters['city'])) {
            $q->where('city', 'like', '%'.$filters['city'].'%');
        }

        if (! empty($filters['location'])) {
            $q->where('location', 'like', '%'.$filters['location'].'%');
        }

        if (! empty($filters['max_price'])) {
            $q->where(function ($qq) use ($filters) {
                $qq->whereNull('min_price')
                    ->orWhere('min_price', '<=', $filters['max_price'])
                    ->orWhere('max_price', '<=', $filters['max_price']);
            });
        }

        if (! empty($filters['wants_post_handover'])) {
            $q->where('has_post_handover', true);
        }

        if (! empty($filters['max_booking_percent'])) {
            $q->whereNotNull('booking_percent_min')
                ->where('booking_percent_min', '<=', $filters['max_booking_percent']);
        }

        if (! empty($filters['plan_ratio'])) {
            $q->where('plan_ratio', $filters['plan_ratio']);
        }

        return $q->orderBy('min_price', 'asc')->limit(10)->get();
    }

    // ---- existing session helpers (kept) ----
    private function defaultState(): array
    {
        return [
            'stage' => 'collect',
            'slots' => [
                'city' => null,
                'location' => null,
                'budget_max' => null,
                'property_type' => null,
                'bedrooms' => null,
                'timing' => null,
                'preferred_handover_year' => null,
                'max_booking_percent' => null,
                'must_have_features' => null,
                'wants_ready_now' => null,
                'wants_off_plan' => null,
                'wants_post_handover' => null,
                'plan_ratio' => null,
            ],
            'last_user_raw' => null,
            'last_bot_reply' => null,
        ];
    }

    private function resetStateIfMissing(): void
    {
        if (! session()->has('ai_chat_state')) {
            $this->resetState();
        }
    }

    private function resetState(): void
    {
        session()->put('ai_chat_state', $this->defaultState());
    }
}
