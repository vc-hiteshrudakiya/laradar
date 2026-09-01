<div align="center">
    <h1>Laradar</h1>
</div>

<p align="center">
    <a href="https://packagist.org/packages/vcian/laradar"><img src="https://img.shields.io/packagist/v/vcian/laradar.svg?style=flat-square" alt="Latest Version on Packagist"></a>
    <a href="https://packagist.org/packages/vcian/laradar"><img src="https://img.shields.io/packagist/dt/vcian/laradar.svg?style=flat-square" alt="Total Downloads"></a>
    <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-%5E8.1-blue.svg?style=flat-square" alt="PHP Version"></a>
    <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-green.svg?style=flat-square" alt="License"></a>
    <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-10%20|%2011%20|%2012%20|%2013-red.svg?style=flat-square" alt="Laravel"></a>
</p>

Laradar automatically **discovers, visualizes, and documents** your Laravel application architecture - without writing a single line of configuration.

Drop it into any Laravel project and instantly get an interactive dashboard that maps your models, controllers, routes, migrations, jobs, events, services, and more. Optionally enhance it with AI-powered architecture insights from any of 7 supported providers.

---

## Why Laradar?

As a Laravel application grows, understanding its full structure becomes harder. New team members spend days reading code to understand what exists. Developers duplicate logic because they didn't know a service already existed. Architecture decisions get made without a clear picture of the whole.

Laradar gives everyone on the team - from the developer who wrote it to the one who just joined - an instant, accurate map of the application. No documentation to maintain. No diagrams to keep updated. Just install and the dashboard reflects the real state of the project at all times.

---

## Quick Start

**1. Install the package:**

```bash
composer require vcian/laradar
```

**2. Publish the config:**

```bash
php artisan vendor:publish --tag=laradar-config
```

**3. Visit the dashboard:**

```
http://your-app.test/laradar
```

> The dashboard is only accessible when `APP_ENV=local` or `APP_ENV=development`. It is automatically disabled in production.

---

## Dashboard

<div align="center">

![Laradar Dashboard](art/dashboard.png)

</div>

Laradar scans your application once on page load and presents all data instantly.

| Section | What you see |
|---|---|
| **Overview** | Architecture score, component counts, and an interactive architecture flowchart |
| **Models** | Table mappings, fillable fields, hidden fields, casts, and relationships |
| **Controllers** | Methods, route bindings, and class dependencies |
| **Routes** | Full route list with HTTP method, URI, middleware, name, and controller |
| **Migrations** | Every migration file with its table operation, column types, and foreign keys |
| **Jobs** | Queue connections and class hierarchy |
| **Events** | Listeners and broadcast channels |
| **Services** | All classes discovered in `App\Services` |
| **Repositories** | All classes discovered in `App\Repositories` |
| **Observers** | Observed models and registered event hooks |
| **Policies** | Guarded models and defined abilities |
| **Middleware** | All registered middleware and their aliases |
| **Modules** | Modular structure detection from `Modules/` directory |
| **Packages** | Installed Composer packages with version information |
| **AI Insights** | AI-powered review of your architecture (requires AI configuration) |

---

## Artisan Reports

Generate a full architecture report without opening the browser:

```bash
php artisan laradar:scan
```

Export in a specific format:

```bash
php artisan laradar:scan --format=html
php artisan laradar:scan --format=json
php artisan laradar:scan --format=markdown
```

Reports are saved to `storage/architecture/`. The HTML report is also viewable in the browser at `http://your-app.test/laradar/report`.

---

## Configuration

```php
// config/laradar.php

return [
    'dashboard' => [
        'enabled'    => true,
        'path'       => 'laradar',       // dashboard URL: /laradar
        'middleware' => ['web'],
    ],

    'scan' => [
        'models'       => true,
        'controllers'  => true,
        'routes'       => true,
        'migrations'   => true,
        'jobs'         => true,
        'events'       => true,
        'services'     => true,
        'repositories' => true,
        'observers'    => true,
        'policies'     => true,
        'modules'      => true,
        'packages'     => true,
    ],

    'ai' => [
        'enabled'  => env('AI_ENABLED', false),
        'provider' => env('AI_PROVIDER', 'gemini'),
    ],
];
```

---

## AI Analysis

Laradar can send your architecture summary to an AI provider and return an architectural review directly inside the dashboard.

Enable it in your `.env`:

```env
AI_ENABLED=true
AI_PROVIDER=gemini
GEMINI_API_KEY=your-key
GEMINI_MODEL=gemini-2.0-flash   # the model used for the architecture review
```

**Supported providers:**

| Provider | `AI_PROVIDER` | API Key | Model |
|---|---|---|---|
| Google Gemini | `gemini` | `GEMINI_API_KEY` | `GEMINI_MODEL` |
| OpenAI | `openai` | `OPENAI_API_KEY` | `OPENAI_MODEL` |
| Anthropic Claude | `anthropic` | `ANTHROPIC_API_KEY` | `ANTHROPIC_MODEL` |
| Groq | `groq` | `GROQ_API_KEY` | `GROQ_MODEL` |
| Mistral | `mistral` | `MISTRAL_API_KEY` | `MISTRAL_MODEL` |
| Ollama *(local, no key needed)* | `ollama` | — | `OLLAMA_MODEL` |
| OpenRouter | `openrouter` | `OPENROUTER_API_KEY` | `OPENROUTER_MODEL` |

---

## What Gets Scanned

| Component | Detected Information |
|---|---|
| **Models** | Table, fillable, hidden, casts, relationships, observers |
| **Controllers** | Methods, route bindings, dependencies |
| **Routes** | URI, HTTP method, middleware, name, controller |
| **Migrations** | Table operations, column types, foreign keys |
| **Jobs** | Queue connection, class hierarchy |
| **Events** | Listeners, broadcast channels |
| **Services** | Classes in `App\Services` |
| **Repositories** | Classes in `App\Repositories` |
| **Observers** | Observed models, event hooks |
| **Policies** | Guarded models, defined abilities |
| **Middleware** | Registered middleware and aliases |
| **Modules** | Detection from `Modules/` directory |
| **Packages** | Composer packages with version info |

---

## License

Laradar is open-sourced software licensed under the [MIT license](LICENSE).
