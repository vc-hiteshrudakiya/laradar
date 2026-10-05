@php
    // ── Data ─────────────────────────────────────────────────────────────────
    $aiEnabled  = (bool) config('laradar.ai.enabled',  env('AI_ENABLED', false));
    $aiProvider = strtolower((string) config('laradar.ai.provider', env('AI_PROVIDER', 'openai')));

    $providers = [
        'openai'     => ['label' => 'OpenAI',     'hint' => 'Paid · API key required'],
        'anthropic'  => ['label' => 'Anthropic',  'hint' => 'Paid · API key required'],
        'gemini'     => ['label' => 'Gemini',      'hint' => 'Paid · API key required'],
        'mistral'    => ['label' => 'Mistral',     'hint' => 'Paid · API key required'],
        'openrouter' => ['label' => 'OpenRouter',  'hint' => 'Free tier available'],
        'ollama'     => ['label' => 'Ollama',      'hint' => 'Local · No API key'],
    ];
    if (! array_key_exists($aiProvider, $providers)) { $aiProvider = 'openai'; }

    $keySet = [
        'openai'     => filled(env('OPENAI_API_KEY')),
        'anthropic'  => filled(env('ANTHROPIC_API_KEY')),
        'gemini'     => filled(env('GEMINI_API_KEY')),
        'mistral'    => filled(env('MISTRAL_API_KEY')),
        'openrouter' => filled(env('OPENROUTER_API_KEY')),
        'ollama'     => true,
    ];
    $currentKeySet = $keySet[$aiProvider] ?? false;

    $quickFixes = [
        ['q' => 'Changed .env but nothing happened',    'fix' => 'php artisan config:clear'],
        ['q' => 'AI pages say "AI is disabled"',        'fix' => 'AI_ENABLED=true'],
        ['q' => 'A section shows zero items',           'fix' => 'composer dump-autoload'],
    ];

    $installSteps = [
        ['title' => 'Install the package',     'kind' => 'Terminal', 'prompt' => '$ ', 'cmd' => 'composer require vcian/laradar --dev',                 'note' => 'Adds Laradar as a dev dependency. Auto-discovery handles the rest.',           'optional' => false],
        ['title' => 'Publish the config file', 'kind' => 'Terminal', 'prompt' => '$ ', 'cmd' => 'php artisan vendor:publish --tag=laradar-config',       'note' => 'Creates config/laradar.php so you can customise scan paths and defaults.',     'optional' => true],
        ['title' => 'Open the dashboard',      'kind' => 'Browser',  'prompt' => '',   'cmd' => route('laradar.dashboard'),                              'note' => 'The scan runs live on every page load — no database, no build step.',          'optional' => false],
    ];

    $envBlocks = [
        'openai'     => [['AI_ENABLED','true'],['AI_PROVIDER','openai'],   ['OPENAI_API_KEY','sk-...'],    ['OPENAI_MODEL','openai-model-name']],
        'anthropic'  => [['AI_ENABLED','true'],['AI_PROVIDER','anthropic'],['ANTHROPIC_API_KEY','sk-ant-...'],['ANTHROPIC_MODEL','anthropic-model-name']],
        'gemini'     => [['AI_ENABLED','true'],['AI_PROVIDER','gemini'],   ['GEMINI_API_KEY','AIza...'],   ['GEMINI_MODEL','gemini-model-name']],
        'mistral'    => [['AI_ENABLED','true'],['AI_PROVIDER','mistral'],  ['MISTRAL_API_KEY','...'],      ['MISTRAL_MODEL','mistral-model-name']],
        'openrouter' => [['AI_ENABLED','true'],['AI_PROVIDER','openrouter'],['OPENROUTER_API_KEY','sk-or-...'],['OPENROUTER_MODEL','openrouter-model-name']],
        'ollama'     => [['AI_ENABLED','true'],['AI_PROVIDER','ollama'],   ['OLLAMA_MODEL','ollama-model-name'],['OLLAMA_BASE_URL','http://localhost:11434/v1']],
    ];

    // [name, group, default, description, provider]
    $envVars = [
        ['AI_ENABLED',             'AI',     'false',               'Turns all AI features on or off.',                                              'All'],
        ['AI_PROVIDER',            'AI',     'openai',              'Provider to use: openai, anthropic, gemini, mistral, openrouter, ollama.',      'All'],
        ['OPENAI_API_KEY',         'Key',    '—',                   'OpenAI secret key. Get from platform.openai.com.',                              'OpenAI'],
        ['ANTHROPIC_API_KEY',      'Key',    '—',                   'Anthropic secret key. Get from console.anthropic.com.',                         'Anthropic'],
        ['GEMINI_API_KEY',         'Key',    '—',                   'Google Gemini key. Get from aistudio.google.com.',                              'Gemini'],
        ['MISTRAL_API_KEY',        'Key',    '—',                   'Mistral AI key. Get from console.mistral.ai.',                                  'Mistral'],
        ['OPENROUTER_API_KEY',     'Key',    '—',                   'OpenRouter key. Free tier available at openrouter.ai.',                         'OpenRouter'],
        ['OPENAI_MODEL',           'Model',  'required',            'Model name from your OpenAI account.',                                          'OpenAI'],
        ['ANTHROPIC_MODEL',        'Model',  'required',            'Model name from your Anthropic console.',                                       'Anthropic'],
        ['GEMINI_MODEL',           'Model',  'required',            'Model name from Google AI Studio.',                                             'Gemini'],
        ['MISTRAL_MODEL',          'Model',  'required',            'Model name from your Mistral console.',                                         'Mistral'],
        ['OPENROUTER_MODEL',       'Model',  'required',            'Model name from openrouter.ai/models.',                                         'OpenRouter'],
        ['OLLAMA_MODEL',           'Model',  'required',            'Locally pulled Ollama model. Run ollama list to see installed.',                 'Ollama'],
        ['OLLAMA_BASE_URL',        'Model',  'http://localhost:11434/v1',  'Ollama server endpoint.',                                              'Ollama'],
        ['AI_TEMPERATURE',         'Tuning', '0.2',                 'Response creativity: 0.0 = deterministic, 1.0 = most creative.',               'All'],
        ['AI_MAX_TOKENS',          'Tuning', '8192',                'Max tokens per response. Increase if AI Docs are cut off.',                     'All'],
        ['AI_MAX_MESSAGE_LENGTH',  'Tuning', '5000',                'Max characters per AI Chat message.',                                           'All'],
        ['AI_FALLBACK_PROVIDER',   'Tuning', '—',                   'Secondary provider if the primary fails.',                                      'All'],
        ['LARADAR_AI_ASYNC',       'Queue',  'false',               'Run AI as background queue jobs instead of blocking the browser.',              'All'],
        ['LARADAR_AI_QUEUE',       'Queue',  'default',             'Queue name to dispatch async AI jobs onto.',                                    'All'],
        ['LARADAR_AI_JOB_TIMEOUT', 'Queue',  '300',                 'Seconds before a queued AI job times out.',                                    'All'],
    ];

    $commandGroups = [
        ['name' => 'Setup', 'desc' => 'Run once', 'items' => [
            ['cmd' => 'composer require vcian/laradar --dev',               'what' => 'Install Laradar into your project.',                       'when' => 'First time'],
            ['cmd' => 'php artisan vendor:publish --tag=laradar-config',    'what' => 'Publish config/laradar.php to change defaults.',           'when' => 'Optional'],
        ]],
        ['name' => 'Scan & Export', 'desc' => 'Generate architecture reports', 'items' => [
            ['cmd' => 'php artisan laradar:scan',                           'what' => 'Export all formats to storage/architecture/.',             'when' => 'Anytime'],
            ['cmd' => 'php artisan laradar:scan --format=html',             'what' => 'Export HTML report only.',                                 'when' => 'HTML only'],
            ['cmd' => 'php artisan laradar:scan --format=json',             'what' => 'Export JSON report only.',                                 'when' => 'JSON only'],
            ['cmd' => 'php artisan laradar:scan --format=markdown',         'what' => 'Export Markdown report only.',                             'when' => 'MD only'],
        ]],
        ['name' => 'Cache', 'desc' => 'When changes do not show up', 'items' => [
            ['cmd' => 'php artisan config:clear',                           'what' => 'Reload settings so .env changes take effect.',             'when' => 'After .env edit'],
            ['cmd' => 'php artisan view:clear',                             'what' => 'Clear compiled views after Laradar updates.',              'when' => 'Stale UI'],
            ['cmd' => 'php artisan optimize:clear',                         'what' => 'Clear every Laravel cache at once.',                       'when' => 'When unsure'],
        ]],
        ['name' => 'Discovery', 'desc' => 'When something is missing', 'items' => [
            ['cmd' => 'composer dump-autoload',                             'what' => 'Refresh class map if a section shows empty.',              'when' => 'Missing classes'],
            ['cmd' => 'php artisan package:discover',                       'what' => 'Re-run package auto-discovery.',                           'when' => '404 after install'],
        ]],
    ];

    $faqs = [
        ['q' => 'AI pages say "AI is disabled"',    'a' => 'Set AI_ENABLED=true in your .env, then run config:clear so Laravel picks up the new value.',   'fix' => 'php artisan config:clear'],
        ['q' => 'I changed .env but nothing happened', 'a' => 'Laravel caches its config. Clear it so the new values are read. If you run config:cache in production, re-run that too.', 'fix' => 'php artisan config:clear'],
        ['q' => 'Invalid API key error',            'a' => 'Copy the key in full with no spaces or quotes. Make sure AI_PROVIDER matches the provider whose key you set, and that the key has sufficient quota.', 'fix' => null],
        ['q' => 'AI response was cut off mid-document', 'a' => 'The response hit the token limit. Increase AI_MAX_TOKENS and run config:clear. Check your provider docs for the max output tokens their model supports.', 'fix' => 'AI_MAX_TOKENS=16000'],
        ['q' => 'AI Chat says my message is too long', 'a' => 'The default limit is 5,000 characters. Increase AI_MAX_MESSAGE_LENGTH in .env then run config:clear.', 'fix' => 'AI_MAX_MESSAGE_LENGTH=10000'],
        ['q' => 'Ollama does not respond',          'a' => 'Make sure Ollama is running on your machine and OLLAMA_BASE_URL points to it. Pull your model first with ollama pull <model-name>.', 'fix' => 'ollama serve'],
        ['q' => 'A section shows zero items',       'a' => 'Laradar uses Composer autoloading to find classes. Refresh it and reload. If you use a non-standard directory, publish the config and update the scan paths.', 'fix' => 'composer dump-autoload'],
        ['q' => 'The dashboard returns 403 or 404', 'a' => 'For 404: run php artisan package:discover to re-register routes. For 403: middleware is blocking /laradar — check config/laradar.php access settings.', 'fix' => null],
    ];

    $toc = [
        ['id' => 'gd-qs',    'label' => 'Quick start'],
        ['id' => 'gd-ai',    'label' => 'AI setup'],
        ['id' => 'gd-env',   'label' => '.env variables'],
        ['id' => 'gd-cmd',   'label' => 'Commands'],
        ['id' => 'gd-fixes', 'label' => 'Common fixes'],
        ['id' => 'gd-ts',    'label' => 'Troubleshooting'],
    ];

    $groupColors = [
        'AI'    => 'gd-grp--ai',
        'Key'   => 'gd-grp--key',
        'Model' => 'gd-grp--model',
        'Tuning'=> 'gd-grp--tune',
        'Queue' => 'gd-grp--queue',
    ];
@endphp

<style>
/* ── Guide Page — design tokens + layout ── */
.gd {
    --ld-red:       #FF2D20;
    --ld-red-t:     rgba(255,45,32,.06);
    --ld-red-l:     rgba(255,45,32,.2);
    --ld-ink:       var(--text);
    --ld-ink2:      var(--text-dim);
    --ld-muted:     var(--text-faint);
    --ld-line:      var(--border);
    --ld-surf:      var(--bg);
    --ld-soft:      var(--bg-elevated);
    --ld-sunken:    var(--bg-sunken);
    --ld-code-bg:   #17181C;
    --ld-code-line: #2A2C31;
    --ld-code-txt:  #EDEEF0;
    --ld-code-dim:  #6F727A;
    --ld-code-key:  #FF8579;
    --ld-green:     #15753F;
    --ld-green-dot: #22C55E;
    --ld-warn-bg:   rgba(245,158,11,.06);
    --ld-warn-l:    rgba(245,158,11,.25);
    --ld-r:         10px;
    --ld-mono:      var(--font-mono);
    max-width: 940px;
    margin: 0 auto;
    padding: 0 0 80px;
}

/* ─── Anchor scroll offset ─── */
[data-section] {
    scroll-margin-top: 64px;
}

/* ─── Hero ─── */
.gd-hero {
    position: relative;
    overflow: hidden;
    border-bottom: 1px solid var(--ld-line);
}

.gd-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: linear-gradient(var(--ld-line) 1px, transparent 1px),
                      linear-gradient(90deg, var(--ld-line) 1px, transparent 1px);
    background-size: 36px 36px;
    -webkit-mask-image: radial-gradient(ellipse 80% 100% at 50% 0%, #000 30%, transparent 80%);
    mask-image: radial-gradient(ellipse 80% 100% at 50% 0%, #000 30%, transparent 80%);
    pointer-events: none;
    opacity: .7;
}

.gd-hero__inner {
    position: relative;
    padding: 52px 40px 44px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.gd-badge {
    align-self: flex-start;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 5px 12px;
    border-radius: 999px;
    border: 1px solid var(--ld-red-l);
    background: var(--ld-red-t);
    color: var(--ld-red);
    font-family: var(--ld-mono);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
}

.gd-badge::before {
    content: '';
    width: 5px;
    height: 5px;
    border-radius: 999px;
    background: var(--ld-red);
    animation: gdPulse 2s ease infinite;
}

@keyframes gdPulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: .35; }
}

.gd h1 {
    margin: 0;
    font-size: 48px;
    line-height: 1.05;
    font-weight: 800;
    letter-spacing: -.04em;
    color: var(--ld-ink);
}

.gd h1 span {
    color: var(--ld-red);
}

.gd-sub {
    margin: 0;
    max-width: 520px;
    font-size: 14.5px;
    line-height: 1.65;
    color: var(--ld-muted);
}

.gd-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    font-family: var(--ld-mono);
    font-size: 11.5px;
    color: var(--ld-muted);
}

/* ─── Hero anchor pills ─── */
.gd-hero-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.gd-hero-nav a {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    height: 28px;
    padding: 0 12px;
    border-radius: 999px;
    border: 1px solid var(--ld-line);
    background: var(--ld-surf);
    font-family: var(--ld-mono);
    font-size: 11px;
    color: var(--ld-ink2);
    text-decoration: none;
    white-space: nowrap;
    transition: border-color .15s, color .15s;
}

.gd-hero-nav a:hover {
    border-color: var(--ld-red-l);
    color: var(--ld-red);
}

.gd-hero-nav a b {
    color: var(--ld-red);
    font-weight: 700;
}

/* ─── Shared label ─── */
.gd-label {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.gd-label__num {
    font-family: var(--ld-mono);
    font-size: 11px;
    font-weight: 600;
    color: var(--ld-red);
}

.gd-label__text {
    font-family: var(--ld-mono);
    font-size: 10.5px;
    font-weight: 600;
    letter-spacing: .12em;
    color: var(--ld-muted);
    white-space: nowrap;
    text-transform: uppercase;
    margin: 0;
}

.gd-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--ld-line);
}

/* ─── Main layout ─── */
.gd-outer {
    padding: 0 40px;
}

.gd-layout {
    padding: 48px 0 80px;
}

.gd-main {
    display: flex;
    flex-direction: column;
    gap: 56px;
}

/* ─── AI section layout ─── */
.gd-ai-section {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.gd-ai-section .gd-label {
    margin-bottom: 0;
}

/* ─── Quick fixes ─── */
.gd-fixes-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}

.gd-fix {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 18px;
    border: 1px solid var(--ld-line);
    border-left: 3px solid var(--ld-red-l);
    border-radius: var(--ld-r);
    background: var(--ld-soft);
}

.gd-fix:hover {
    border-left-color: var(--ld-red);
}

.gd-fix__head {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--ld-ink);
    line-height: 1.4;
}

.gd-fix__icon {
    width: 28px;
    height: 28px;
    flex-shrink: 0;
    border-radius: 8px;
    background: var(--ld-red-t);
    border: 1px solid var(--ld-red-l);
    color: var(--ld-red);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 1px;
}

.gd-codeline {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 5px 5px 5px 12px;
    border-radius: 8px;
    background: var(--ld-code-bg);
}

.gd-codeline code {
    flex: 1;
    min-width: 0;
    overflow-x: auto;
    white-space: nowrap;
    font-family: var(--ld-mono);
    font-size: 12px;
    color: var(--ld-code-txt);
}

.gd-copy-dark {
    flex-shrink: 0;
    height: 28px;
    min-width: 56px;
    padding: 0 10px;
    border-radius: 6px;
    border: 1px solid #34363C;
    background: transparent;
    color: var(--ld-code-txt);
    font-family: var(--ld-mono);
    font-size: 10.5px;
    cursor: pointer;
    transition: border-color .15s;
}

.gd-copy-dark:hover {
    border-color: #5A5D66;
}

.gd-copy-light {
    flex-shrink: 0;
    height: 36px;
    min-width: 64px;
    padding: 0 12px;
    border-radius: 8px;
    border: 1px solid var(--ld-line);
    background: var(--ld-surf);
    color: var(--ld-ink2);
    font-family: var(--ld-mono);
    font-size: 11.5px;
    cursor: pointer;
    transition: border-color .15s;
}

.gd-copy-light:hover {
    border-color: var(--ld-ink);
}

.is-copied {
    color: #34D399 !important;
    border-color: #2d6b49 !important;
}

/* ─── Steps (timeline) ─── */
.gd-steps {
    list-style: none;
    margin: 0;
    padding: 0;
}

.gd-step {
    display: flex;
    gap: 18px;
}

.gd-step__rail {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 30px;
    flex-shrink: 0;
}

.gd-step__num {
    width: 30px;
    height: 30px;
    border-radius: 999px;
    border: 1.5px solid var(--ld-red);
    color: var(--ld-red);
    font-family: var(--ld-mono);
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: var(--ld-surf);
}

.gd-step__line {
    width: 1.5px;
    flex: 1;
    background: var(--ld-red-l);
    min-height: 20px;
}

.gd-step:last-child .gd-step__line {
    background: transparent;
}

.gd-step__body {
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-width: 0;
    flex: 1;
    padding: 4px 0 28px;
}

.gd-step__title {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    font-size: 14.5px;
    font-weight: 700;
    margin: 0;
    color: var(--ld-ink);
}

.gd-tag-opt {
    font-family: var(--ld-mono);
    font-size: 10.5px;
    font-weight: 500;
    padding: 1px 7px;
    border-radius: 4px;
    border: 1px solid var(--ld-line);
    color: var(--ld-muted);
}

.gd-step__note {
    font-size: 13px;
    color: var(--ld-muted);
    margin: 0;
}

.gd-term {
    border-radius: var(--ld-r);
    background: var(--ld-code-bg);
    overflow: hidden;
}

.gd-term__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 7px 7px 14px;
    border-bottom: 1px solid var(--ld-code-line);
    font-family: var(--ld-mono);
    font-size: 10px;
    letter-spacing: .1em;
    color: var(--ld-code-dim);
    text-transform: uppercase;
}

.gd-term__body {
    padding: 12px 14px;
    font-family: var(--ld-mono);
    font-size: 12.5px;
    color: var(--ld-code-txt);
    overflow-x: auto;
    white-space: nowrap;
}

.gd-term__body span {
    color: var(--ld-code-key);
}

/* ─── Callout ─── */
.gd-callout {
    padding: 16px 20px;
    border: 1px solid var(--ld-line);
    border-left: 3px solid var(--ld-red);
    border-radius: var(--ld-r);
    font-size: 13.5px;
    line-height: 1.7;
    color: var(--ld-ink2);
    background: var(--ld-surf);
}

/* ─── Provider selector ─── */
.gd-providers {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}

.gd-provider {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 6px;
    padding: 14px 16px;
    border-radius: var(--ld-r);
    border: 1px solid var(--ld-line);
    background: var(--ld-surf);
    text-align: left;
    color: var(--ld-ink);
    cursor: pointer;
    transition: border-color .15s, box-shadow .15s;
    font-family: inherit;
}

.gd-provider:hover {
    border-color: rgba(255,45,32,.3);
}

.gd-provider[aria-pressed="true"] {
    border: 1.5px solid var(--ld-red);
    box-shadow: 0 0 0 3px rgba(255,45,32,.08);
}

.gd-provider__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    font-size: 13.5px;
    font-weight: 700;
}

.gd-radio {
    width: 16px;
    height: 16px;
    border-radius: 999px;
    border: 1.5px solid var(--ld-line);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: border-color .15s;
}

.gd-provider[aria-pressed="true"] .gd-radio {
    border-color: var(--ld-red);
}

.gd-provider[aria-pressed="true"] .gd-radio::after {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: var(--ld-red);
}

.gd-provider__hint {
    font-family: var(--ld-mono);
    font-size: 11px;
    color: var(--ld-muted);
}

/* ─── .env code block ─── */
.gd-env {
    border-radius: var(--ld-r);
    background: var(--ld-code-bg);
    overflow: hidden;
}

.gd-env__head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 8px 8px 16px;
    border-bottom: 1px solid var(--ld-code-line);
}

.gd-env__file {
    font-family: var(--ld-mono);
    font-size: 11.5px;
    color: var(--ld-code-txt);
}

.gd-env__tag {
    font-family: var(--ld-mono);
    font-size: 10.5px;
    padding: 2px 7px;
    border-radius: 4px;
    background: var(--ld-code-line);
    color: #9BA0A8;
}

.gd-env__head .gd-copy-dark {
    margin-left: auto;
}

.gd-env__body {
    margin: 0;
    padding: 12px 0;
    font-family: var(--ld-mono);
    font-size: 12.5px;
    line-height: 2;
    overflow-x: auto;
}

.gd-env__line {
    display: flex;
    gap: 16px;
    padding: 0 16px;
    white-space: nowrap;
}

.gd-env__no {
    width: 14px;
    text-align: right;
    color: #5A5D66;
    user-select: none;
}

.gd-env__k {
    color: var(--ld-code-key);
}

.gd-env__eq {
    color: var(--ld-code-dim);
}

.gd-env__v {
    color: var(--ld-code-txt);
}

/* ─── Connection status grid ─── */
.gd-status {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1px;
    border: 1px solid var(--ld-line);
    border-radius: var(--ld-r);
    overflow: hidden;
    background: var(--ld-line);
}

.gd-status__cell {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 14px 16px;
    background: var(--ld-surf);
}

.gd-status__lbl {
    font-family: var(--ld-mono);
    font-size: 10px;
    letter-spacing: .12em;
    color: var(--ld-muted);
    text-transform: uppercase;
}

.gd-status__val {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--ld-ink);
}

.gd-status__val.is-on {
    color: var(--ld-green);
}

.gd-status__val.is-off {
    color: var(--ld-red);
}

.gd-sdot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: var(--ld-code-dim);
    flex-shrink: 0;
}

.is-on .gd-sdot {
    background: var(--ld-green-dot);
}

.is-off .gd-sdot {
    background: var(--ld-red);
}

.gd-status__action {
    display: flex;
    align-items: center;
    padding: 10px 12px;
    background: var(--ld-surf);
}

.gd-btn-dark {
    width: 100%;
    height: 40px;
    border-radius: 8px;
    border: 0;
    background: var(--ld-ink);
    color: var(--ld-surf);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity .15s;
}

.gd-btn-dark:hover {
    opacity: .85;
}

/* ─── Warning ─── */
.gd-warn {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding: 14px 18px;
    border-radius: var(--ld-r);
    background: var(--ld-warn-bg);
    border: 1px solid var(--ld-warn-l);
    font-size: 13px;
    line-height: 1.65;
    color: var(--ld-ink2);
}

.gd-warn svg {
    flex-shrink: 0;
    margin-top: 2px;
    color: #D97706;
}

/* ─── Info box ─── */
.gd-info {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding: 14px 18px;
    border-radius: var(--ld-r);
    background: rgba(99,102,241,.05);
    border: 1px solid rgba(99,102,241,.2);
    font-size: 13px;
    line-height: 1.65;
    color: var(--ld-ink2);
}

.gd-info svg {
    flex-shrink: 0;
    margin-top: 2px;
    color: #6366f1;
}

/* ─── Inline code in prose boxes ─── */
.gd-callout code,
.gd-info code,
.gd-warn code {
    font-family: var(--ld-mono);
    font-size: 12px;
}

/* ─── Variables table ─── */
.gd-table-wrap {
    border: 1px solid var(--ld-line);
    border-radius: var(--ld-r);
    overflow-x: auto;
}

.gd-table {
    width: 100%;
    min-width: 520px;
    border-collapse: collapse;
    font-size: 13px;
}

.gd-table th {
    text-align: left;
    padding: 10px 14px;
    background: var(--ld-soft);
    border-bottom: 1px solid var(--ld-line);
    font-family: var(--ld-mono);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: .12em;
    color: var(--ld-muted);
    text-transform: uppercase;
}

.gd-table th:nth-child(1) {
    width: 220px;
}

.gd-table th:nth-child(2) {
    width: 110px;
}

.gd-table td {
    padding: 12px 14px;
    vertical-align: middle;
    border-top: 1px solid var(--ld-line);
}

.gd-table tbody tr:hover td {
    background: var(--ld-soft);
}

.gd-table tr.gd-group-row td {
    padding: 7px 14px;
    background: var(--ld-sunken);
    border-top: 1px solid var(--ld-line);
}

.gd-group-label {
    font-family: var(--ld-mono);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.gd-group-label::after {
    content: '';
    display: inline-block;
    width: 40px;
    height: 1px;
    background: currentColor;
    opacity: .3;
}

.gd-grp--ai    .gd-group-label { color: var(--ld-red); }
.gd-grp--key   .gd-group-label { color: #6366f1; }
.gd-grp--model .gd-group-label { color: #B45309; }
.gd-grp--tune  .gd-group-label { color: #15803D; }
.gd-grp--queue .gd-group-label { color: var(--ld-ink2); }

.gd-var-cell {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.gd-var {
    font-family: var(--ld-mono);
    font-size: 12px;
    font-weight: 700;
    color: var(--ld-red);
    white-space: nowrap;
}

.gd-provider-tag {
    font-family: var(--ld-mono);
    font-size: 10px;
    font-weight: 500;
    color: var(--ld-muted);
    white-space: nowrap;
}

.gd-provider-tag--all {
    opacity: .6;
}

.gd-default {
    font-family: var(--ld-mono);
    font-size: 11.5px;
    padding: 3px 8px;
    border-radius: 4px;
    border: 1px solid var(--ld-line);
    background: var(--ld-soft);
    white-space: nowrap;
    color: var(--ld-ink2);
}

.gd-desc {
    font-size: 13px;
    color: var(--ld-muted);
    line-height: 1.55;
}

/* ─── Command groups ─── */
.gd-cmd-group {
    border: 1px solid var(--ld-line);
    border-radius: var(--ld-r);
    overflow: hidden;
    margin-bottom: 12px;
}

.gd-cmd-group:last-child {
    margin-bottom: 0;
}

.gd-cmd-group__head {
    display: flex;
    align-items: baseline;
    gap: 8px;
    padding: 10px 16px;
    background: var(--ld-soft);
    border-bottom: 1px solid var(--ld-line);
}

.gd-cmd-group__name {
    font-size: 13px;
    font-weight: 700;
    margin: 0;
    color: var(--ld-ink);
}

.gd-cmd-group__desc {
    font-size: 12.5px;
    color: var(--ld-muted);
}

.gd-cmd {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 16px;
    background: var(--ld-surf);
}

.gd-cmd + .gd-cmd {
    border-top: 1px solid var(--ld-line);
}

.gd-cmd:hover {
    background: var(--ld-soft);
}

.gd-cmd__text {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 0;
    flex: 1;
}

.gd-cmd__text code {
    font-family: var(--ld-mono);
    font-size: 12.5px;
    font-weight: 600;
    overflow-x: auto;
    white-space: nowrap;
    color: var(--ld-ink);
}

.gd-cmd__text span {
    font-size: 12.5px;
    color: var(--ld-muted);
}

.gd-when {
    font-family: var(--ld-mono);
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 999px;
    border: 1px solid var(--ld-line);
    color: var(--ld-ink2);
    white-space: nowrap;
    flex-shrink: 0;
}

/* ─── FAQ accordion ─── */
.gd-faqs {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.gd-faq {
    border-radius: var(--ld-r);
    border: 1px solid var(--ld-line);
    border-left: 3px solid var(--ld-line);
    background: var(--ld-surf);
    transition: border-color .15s;
    overflow: hidden;
}

.gd-faq.is-open {
    border-color: var(--ld-red-l);
    border-left-color: var(--ld-red);
}

.gd-faq__q {
    width: 100%;
    min-height: 52px;
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    border: 0;
    background: transparent;
    color: var(--ld-ink);
    font-size: 13.5px;
    font-weight: 600;
    text-align: left;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s;
}

.gd-faq__q:hover {
    background: var(--ld-soft);
}

.gd-faq__num {
    font-family: var(--ld-mono);
    font-size: 11px;
    color: var(--ld-muted);
    width: 20px;
    flex-shrink: 0;
}

.gd-faq__text {
    flex: 1;
}

.gd-faq__chev {
    flex-shrink: 0;
    color: var(--ld-muted);
    transition: transform .2s ease;
}

.gd-faq.is-open .gd-faq__chev {
    transform: rotate(180deg);
    color: var(--ld-red);
}

.gd-faq__a {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 14px 16px 16px 50px;
    border-top: 1px solid var(--ld-line);
}

.gd-faq__a p {
    margin: 0;
    font-size: 13px;
    line-height: 1.75;
    color: var(--ld-ink2);
}

.gd-faq__fix {
    width: 100%;
}

/* ─── Still stuck ─── */
.gd-stuck {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding: 22px 24px;
    border: 1px solid var(--ld-line);
    border-left: 3px solid var(--ld-red);
    border-radius: var(--ld-r);
    background: var(--ld-surf);
}

.gd-stuck h3 {
    margin: 0 0 4px;
    font-size: 15px;
    font-weight: 700;
    color: var(--ld-ink);
}

.gd-stuck p {
    margin: 0;
    max-width: 460px;
    font-size: 13px;
    color: var(--ld-muted);
}

.gd-stuck__actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.gd-btn {
    display: inline-flex;
    align-items: center;
    height: 40px;
    padding: 0 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
}

.gd-btn--ghost {
    border: 1px solid var(--ld-line);
    color: var(--ld-ink);
    background: var(--ld-surf);
}

.gd-btn--ghost:hover {
    border-color: var(--ld-ink);
}

.gd-btn--red {
    background: var(--ld-red);
    color: #fff;
}

.gd-btn--red:hover {
    opacity: .88;
}

/* ─── Responsive ─── */
@media (max-width: 680px) {
    .gd-hero__inner,
    .gd-outer {
        padding-left: 18px;
        padding-right: 18px;
    }

    .gd h1 {
        font-size: 36px;
    }

    .gd-providers {
        grid-template-columns: repeat(2, 1fr);
    }

    .gd-fixes-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="gd">

{{-- ═══ Hero ═══ --}}
<section class="gd-hero">
    <div class="gd-hero__inner">
        <span class="gd-badge">Help &amp; Setup</span>
        <h1>Laradar <span>Guide</span></h1>
        <p class="gd-sub">Stuck somewhere? Everything to install, configure and fix Laradar — commands, .env settings and quick answers in one place.</p>
        <div class="gd-meta">
            <span>{{ count($toc) }} sections</span>
            <span>About 6 min read</span>
            <span>Updated {{ date('d M Y') }}</span>
        </div>
        <nav class="gd-hero-nav" aria-label="On this page">
            @foreach ($toc as $i => $item)
                <a href="#{{ $item['id'] }}"><b>{{ sprintf('%02d', $i + 1) }}</b>{{ $item['label'] }}</a>
            @endforeach
        </nav>
    </div>
</section>

<div class="gd-outer">
    <div class="gd-layout">
        <main class="gd-main">

            {{-- ─── 01 Quick Start ─── --}}
            <section id="gd-qs" data-section>
                <div class="gd-label"><span class="gd-label__num">01</span><h2 class="gd-label__text">Quick start</h2></div>
                <ol class="gd-steps">
                    @foreach ($installSteps as $i => $step)
                        <li class="gd-step">
                            <div class="gd-step__rail" aria-hidden="true">
                                <span class="gd-step__num">{{ $i + 1 }}</span>
                                <span class="gd-step__line"></span>
                            </div>
                            <div class="gd-step__body">
                                <h3 class="gd-step__title">
                                    {{ $step['title'] }}
                                    @if($step['optional'])<span class="gd-tag-opt">optional</span>@endif
                                </h3>
                                <p class="gd-step__note">{{ $step['note'] }}</p>
                                <div class="gd-term">
                                    <div class="gd-term__head">
                                        {{ $step['kind'] }}
                                        <button type="button" class="gd-copy-dark" data-copy="{{ $step['cmd'] }}" aria-label="Copy {{ strtolower($step['kind']) }} command">Copy</button>
                                    </div>
                                    <div class="gd-term__body"><span>{{ $step['prompt'] }}</span>{{ $step['cmd'] }}</div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>

            {{-- ─── 02 AI Setup ─── --}}
            <section id="gd-ai" data-section class="gd-ai-section">
                <div class="gd-label"><span class="gd-label__num">02</span><h2 class="gd-label__text">AI setup (.env)</h2></div>

                <div class="gd-callout">
                    AI features are <strong>optional</strong>. Choose a provider below, copy its <code>.env</code> block into your project, and replace the placeholder values. Your API key is never stored inside Laradar.
                </div>

                <div class="gd-info">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                    <span><strong>AI_PROVIDER is optional.</strong> Laradar auto-detects your provider from whichever API key you add to <code>.env</code>. You only need <code>AI_ENABLED=true</code> and your key — no need to set <code>AI_PROVIDER</code> unless you want to force a specific one.</span>
                </div>

                <div class="gd-providers" role="group" aria-label="Choose an AI provider">
                    @foreach ($providers as $key => $prov)
                        <button type="button" class="gd-provider" data-provider="{{ $key }}" data-provider-label="{{ $prov['label'] }}" aria-pressed="{{ $key === $aiProvider ? 'true' : 'false' }}">
                            <span class="gd-provider__top">{{ $prov['label'] }}<span class="gd-radio" aria-hidden="true"></span></span>
                            <span class="gd-provider__hint">{{ $prov['hint'] }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="gd-env">
                    <div class="gd-env__head">
                        <span class="gd-env__file">.env</span>
                        <span class="gd-env__tag" data-provider-name>{{ $providers[$aiProvider]['label'] }}</span>
                        <button type="button" class="gd-copy-dark" id="gd-copy-env" aria-label="Copy .env values">Copy</button>
                    </div>
                    @foreach ($envBlocks as $key => $lines)
                        <pre class="gd-env__body" data-env-block="{{ $key }}" @if($key !== $aiProvider) hidden @endif>@foreach($lines as $n => [$k,$v])<div class="gd-env__line"><span class="gd-env__no">{{ $n + 1 }}</span><span><span class="gd-env__k">{{ $k }}</span><span class="gd-env__eq">=</span><span class="gd-env__v">{{ $v }}</span></span></div>@endforeach</pre>
                    @endforeach
                </div>

                <div class="gd-status">
                    <div class="gd-status__cell">
                        <span class="gd-status__lbl">Status</span>
                        <span class="gd-status__val {{ $aiEnabled ? 'is-on' : 'is-off' }}">
                            <span class="gd-sdot" aria-hidden="true"></span>{{ $aiEnabled ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div class="gd-status__cell">
                        <span class="gd-status__lbl">Provider</span>
                        <span class="gd-status__val" data-status-provider>{{ $providers[$aiProvider]['label'] }}</span>
                    </div>
                    <div class="gd-status__cell">
                        <span class="gd-status__lbl">API key</span>
                        <span class="gd-status__val">
                            @if($aiProvider === 'ollama') Not needed
                            @elseif($currentKeySet) Set · hidden
                            @else Not set
                            @endif
                        </span>
                    </div>
                    <div class="gd-status__action">
                        <a href="{{ route('laradar.ai') }}" class="gd-btn-dark">Check AI status</a>
                    </div>
                </div>

                <div class="gd-warn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 9v4M12 17h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/></svg>
                    <span><strong>Important:</strong> after every .env change run <code>php artisan config:clear</code> — otherwise Laravel keeps serving the old cached values.</span>
                </div>
            </section>

            {{-- ─── 03 .env Variables ─── --}}
            <section id="gd-env" data-section>
                <div class="gd-label"><span class="gd-label__num">03</span><h2 class="gd-label__text">Environment variables</h2></div>
                @php
                    $grouped = [];
                    foreach ($envVars as $row) { $grouped[$row[1]][] = $row; }
                @endphp
                <div class="gd-table-wrap">
                    <table class="gd-table">
                        <thead>
                            <tr>
                                <th>Variable</th>
                                <th>Default</th>
                                <th>What it does</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grouped as $grpName => $rows)
                                <tr class="gd-group-row {{ $groupColors[$grpName] ?? 'gd-grp--queue' }}">
                                    <td colspan="3"><span class="gd-group-label">{{ $grpName }}</span></td>
                                </tr>
                                @foreach ($rows as [$name, $group, $default, $what, $provider])
                                    <tr>
                                        <td>
                                            <div class="gd-var-cell">
                                                <code class="gd-var">{{ $name }}</code>
                                                <span class="gd-provider-tag {{ $provider === 'All' ? 'gd-provider-tag--all' : '' }}">{{ $provider }}</span>
                                            </div>
                                        </td>
                                        <td><code class="gd-default">{{ $default }}</code></td>
                                        <td class="gd-desc">{{ $what }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- ─── 04 Commands ─── --}}
            <section id="gd-cmd" data-section>
                <div class="gd-label"><span class="gd-label__num">04</span><h2 class="gd-label__text">Useful commands</h2></div>
                @foreach ($commandGroups as $group)
                    <div class="gd-cmd-group">
                        <div class="gd-cmd-group__head">
                            <h3 class="gd-cmd-group__name">{{ $group['name'] }}</h3>
                            <span class="gd-cmd-group__desc">— {{ $group['desc'] }}</span>
                        </div>
                        @foreach ($group['items'] as $cmd)
                            <div class="gd-cmd">
                                <div class="gd-cmd__text">
                                    <code>{{ $cmd['cmd'] }}</code>
                                    <span>{{ $cmd['what'] }}</span>
                                </div>
                                <span class="gd-when">{{ $cmd['when'] }}</span>
                                <button type="button" class="gd-copy-light" data-copy="{{ $cmd['cmd'] }}" aria-label="Copy: {{ $cmd['cmd'] }}">Copy</button>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </section>

            {{-- ─── 05 Most Common Fixes ─── --}}
            <section id="gd-fixes" data-section aria-labelledby="gd-fixes-hd">
                <div class="gd-label"><span class="gd-label__num">05</span><h2 class="gd-label__text" id="gd-fixes-hd">Most common fixes</h2></div>
                <div class="gd-fixes-grid">
                    @foreach ($quickFixes as $fix)
                        <div class="gd-fix">
                            <div class="gd-fix__head">
                                <span class="gd-fix__icon" aria-hidden="true">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a4 4 0 0 0 5 5L13 18l-3 3-4-4 3-3 6.7-6.7a4 4 0 0 0-1-1z"/></svg>
                                </span>
                                {{ $fix['q'] }}
                            </div>
                            <div class="gd-codeline">
                                <code>{{ $fix['fix'] }}</code>
                                <button type="button" class="gd-copy-dark" data-copy="{{ $fix['fix'] }}" aria-label="Copy fix">Copy</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- ─── 06 Troubleshooting ─── --}}
            <section id="gd-ts" data-section>
                <div class="gd-label"><span class="gd-label__num">06</span><h2 class="gd-label__text">Troubleshooting</h2></div>
                <div class="gd-faqs">
                    @foreach ($faqs as $i => $faq)
                        <div class="gd-faq">
                            <button type="button" class="gd-faq__q" aria-expanded="false" aria-controls="gd-faq-{{ $i }}">
                                <span class="gd-faq__num">{{ sprintf('%02d', $i + 1) }}</span>
                                <span class="gd-faq__text">{{ $faq['q'] }}</span>
                                <svg class="gd-faq__chev" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div class="gd-faq__a" id="gd-faq-{{ $i }}" hidden>
                                <p>{{ $faq['a'] }}</p>
                                @if($faq['fix'])
                                    <div class="gd-codeline gd-faq__fix">
                                        <code>{{ $faq['fix'] }}</code>
                                        <button type="button" class="gd-copy-dark" data-copy="{{ $faq['fix'] }}" aria-label="Copy fix">Copy</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- ─── Still stuck ─── --}}
            <section class="gd-stuck" aria-labelledby="gd-stuck-hd">
                <div>
                    <h3 id="gd-stuck-hd">Still stuck?</h3>
                    <p>Ask AI Chat about your project, or open a GitHub issue with your Laravel version, PHP version ({{ PHP_VERSION }}) and the full error message.</p>
                </div>
                <div class="gd-stuck__actions">
                    <a href="{{ route('laradar.chat') }}" class="gd-btn gd-btn--ghost">Ask AI Chat</a>
                    <a href="https://github.com/vcian/laradar/issues" class="gd-btn gd-btn--red" target="_blank" rel="noopener noreferrer">Report an issue</a>
                </div>
            </section>

        </main>

    </div>{{-- /gd-layout --}}

</div>{{-- /gd-outer --}}
</div>{{-- /gd --}}

<script>
(function () {
    'use strict';

    /* ── Copy to clipboard ── */
    function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) return navigator.clipboard.writeText(text);
        return new Promise(function (resolve, reject) {
            var ta = document.createElement('textarea');
            ta.value = text; ta.setAttribute('readonly',''); ta.style.position = 'fixed'; ta.style.opacity = '0';
            document.body.appendChild(ta); ta.select();
            try { document.execCommand('copy') ? resolve() : reject(); } catch(e) { reject(e); }
            finally { document.body.removeChild(ta); }
        });
    }

    function flashCopied(btn, ok) {
        var orig = btn.dataset.label || btn.textContent;
        btn.dataset.label = orig;
        btn.textContent = ok ? 'Copied ✓' : 'Press Ctrl+C';
        if (ok) btn.classList.add('is-copied');
        clearTimeout(btn._t);
        btn._t = setTimeout(function () { btn.textContent = orig; btn.classList.remove('is-copied'); }, 1600);
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-copy]');
        if (!btn) return;
        copyText(btn.getAttribute('data-copy'))
            .then(function () { flashCopied(btn, true); })
            .catch(function () { flashCopied(btn, false); });
    });

    /* ── Provider selector ── */
    var providerBtns = document.querySelectorAll('[data-provider]');
    var providerName = document.querySelector('[data-provider-name]');
    var statusProvider = document.querySelector('[data-status-provider]');
    var envBlocks = document.querySelectorAll('[data-env-block]');

    providerBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var key = btn.getAttribute('data-provider');
            var label = btn.getAttribute('data-provider-label');
            providerBtns.forEach(function (b) { b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'); });
            envBlocks.forEach(function (block) { block.hidden = block.getAttribute('data-env-block') !== key; });
            if (providerName) providerName.textContent = label;
            if (statusProvider) statusProvider.textContent = label;
        });
    });

    /* ── Copy .env block ── */
    var copyEnvBtn = document.getElementById('gd-copy-env');
    if (copyEnvBtn) {
        copyEnvBtn.addEventListener('click', function () {
            var visible = document.querySelector('[data-env-block]:not([hidden])');
            if (!visible) return;
            var text = Array.prototype.map.call(visible.querySelectorAll('.gd-env__line'), function (line) {
                return line.querySelector('.gd-env__k').textContent + '=' + line.querySelector('.gd-env__v').textContent;
            }).join('\n');
            copyText(text).then(function(){ flashCopied(copyEnvBtn, true); }).catch(function(){ flashCopied(copyEnvBtn, false); });
        });
    }

    /* ── FAQ accordion ── */
    document.querySelectorAll('.gd-faq__q').forEach(function (q) {
        q.addEventListener('click', function () {
            var item = q.closest('.gd-faq');
            var panel = document.getElementById(q.getAttribute('aria-controls'));
            var open = q.getAttribute('aria-expanded') === 'true';
            q.setAttribute('aria-expanded', open ? 'false' : 'true');
            item.classList.toggle('is-open', !open);
            if (panel) panel.hidden = open;
        });
    });

})();
</script>
