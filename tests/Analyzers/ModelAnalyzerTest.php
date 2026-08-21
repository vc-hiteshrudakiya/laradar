<?php

namespace Vcian\Laradar\Tests\Analyzers;

use Vcian\Laradar\Analyzers\ModelAnalyzer;
use Vcian\Laradar\Tests\TestCase;
use Illuminate\Support\Facades\File;

class ModelAnalyzerTest extends TestCase
{
    private string $modelsPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modelsPath = app_path('Models');
        File::makeDirectory($this->modelsPath, 0755, true, true);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->modelsPath);

        parent::tearDown();
    }

    public function test_it_returns_empty_when_no_models_exist(): void
    {
        $result = (new ModelAnalyzer($this->modelsPath))->analyze();

        $this->assertSame([], $result['items']);
        $this->assertSame([], $result['errors']);
    }

    public function test_it_detects_model_files(): void
    {
        File::put($this->modelsPath . '/User.php', '<?php namespace App\Models; class User {}');
        File::put($this->modelsPath . '/Post.php', '<?php namespace App\Models; class Post {}');
        File::put($this->modelsPath . '/Category.php', '<?php namespace App\Models; class Category {}');

        $result = (new ModelAnalyzer($this->modelsPath))->analyze();

        $this->assertCount(3, $result['items']);

        $names = array_column($result['items'], 'name');
        $this->assertContains('User', $names);
        $this->assertContains('Post', $names);
        $this->assertContains('Category', $names);

        $paths = array_column($result['items'], 'path');
        $this->assertContains('app/Models/User.php', $paths);
    }

    public function test_it_detects_model_properties(): void
    {
        File::put($this->modelsPath . '/User.php', <<<'PHP'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden   = ['password', 'remember_token'];
}
PHP);

        $result = (new ModelAnalyzer($this->modelsPath))->analyze();
        $user   = $result['items'][0];

        $this->assertSame('App\Models', $user['namespace']);
        $this->assertSame('App\Models\User', $user['full_class']);
        $this->assertSame('users', $user['table']);
        $this->assertContains('name', $user['fillable']);
        $this->assertContains('password', $user['hidden']);
    }

    public function test_it_detects_relationships(): void
    {
        File::put($this->modelsPath . '/User.php', <<<'PHP'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class User extends Model
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
PHP);

        $result        = (new ModelAnalyzer($this->modelsPath))->analyze();
        $relationships = $result['items'][0]['relationships'];

        $this->assertCount(1, $relationships);
        $this->assertSame('hasMany', $relationships[0]['type']);
        $this->assertSame('posts', $relationships[0]['method']);
        $this->assertSame('Post', $relationships[0]['related']);
    }
}
