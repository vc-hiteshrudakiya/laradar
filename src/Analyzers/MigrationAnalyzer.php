<?php

namespace Vcian\Laradar\Analyzers;

use Symfony\Component\Finder\Finder;

class MigrationAnalyzer
{
    private const COLUMN_TYPES = [
        'bigIncrements','bigInteger','binary','boolean','char',
        'dateTimeTz','dateTime','date','decimal','double',
        'enum','float','foreignId','foreignUlid','foreignUuid',
        'geometry','id','increments','integer',
        'ipAddress','json','jsonb','longText',
        'macAddress','mediumIncrements','mediumInteger','mediumText',
        'morphs','nullableMorphs','nullableTimestamps','nullableUlidMorphs',
        'nullableUuidMorphs','rememberToken','set','smallIncrements',
        'smallInteger','softDeletesTz','softDeletes','string','text',
        'timeTz','time','timestampTz','timestamp','timestamps',
        'tinyIncrements','tinyInteger','tinyText','ulid','ulidMorphs',
        'unsignedBigInteger','unsignedDecimal','unsignedInteger',
        'unsignedMediumInteger','unsignedSmallInteger','unsignedTinyInteger',
        'uuidMorphs','uuid','year',
    ];

    public function __construct(private string $migrationsPath) {}

    public function analyze(): array
    {
        $items  = [];
        $errors = [];

        if (!is_dir($this->migrationsPath)) {
            return ['items' => [], 'errors' => []];
        }

        try {
            $finder = (new Finder())->files()->name('*.php')->in($this->migrationsPath)->sortByName();
        } catch (\Throwable) {
            return ['items' => [], 'errors' => []];
        }

        foreach ($finder as $file) {
            try {
                $item = $this->parseMigration($file->getRealPath(), $file->getFilenameWithoutExtension());
                if ($item !== null) {
                    $items[] = $item;
                }
            } catch (\Throwable $e) {
                $errors[] = ['file' => $file->getFilename(), 'message' => $e->getMessage()];
            }
        }

        return ['items' => $items, 'errors' => $errors];
    }

    private function parseMigration(string $filePath, string $filename): ?array
    {
        $content = file_get_contents($filePath);
        if ($content === false) return null;

        [$table, $operation] = $this->extractTableAndOperation($content);

        $date = null;
        if (preg_match('/^(\d{4}_\d{2}_\d{2})/', $filename, $m)) {
            $date = str_replace('_', '-', $m[1]);
        }

        return [
            'filename'    => $filename,
            'table'       => $table,
            'operation'   => $operation,
            'date'        => $date,
            'columns'     => $this->parseColumns($content),
            'foreign_keys'=> $this->parseForeignKeys($content),
        ];
    }

    private function extractTableAndOperation(string $content): array
    {
        if (preg_match('/Schema\s*::\s*create\s*\(\s*[\'"]([^\'"]+)[\'"]/i', $content, $m)) {
            return [$m[1], 'create'];
        }
        if (preg_match('/Schema\s*::\s*table\s*\(\s*[\'"]([^\'"]+)[\'"]/i', $content, $m)) {
            return [$m[1], 'modify'];
        }
        if (preg_match('/Schema\s*::\s*dropIfExists\s*\(\s*[\'"]([^\'"]+)[\'"]/i', $content, $m)) {
            return [$m[1], 'drop'];
        }
        if (preg_match('/Schema\s*::\s*drop\s*\(\s*[\'"]([^\'"]+)[\'"]/i', $content, $m)) {
            return [$m[1], 'drop'];
        }
        if (preg_match('/Schema\s*::\s*rename\s*\(\s*[\'"]([^\'"]+)[\'"]/i', $content, $m)) {
            return [$m[1], 'rename'];
        }
        return [null, 'unknown'];
    }

    private function parseColumns(string $content): array
    {
        $columns  = [];
        $typesPat = implode('|', self::COLUMN_TYPES);

        preg_match_all(
            '/\$table\s*->\s*(' . $typesPat . ')\s*\(\s*(?:\'([^\']*?)\'|"([^"]*?)")?\s*(?:,[^)]+)?\s*\)((?:\s*->[a-zA-Z]+\s*\([^)]*\))*)/m',
            $content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $type      = $match[1];
            $name      = $match[2] !== '' ? $match[2] : ($match[3] !== '' ? $match[3] : null);
            $modifiers = $match[4] ?? '';

            // No-arg special types
            if ($type === 'id' && !$name)               { $name = 'id'; }
            if ($type === 'rememberToken' && !$name)    { $name = 'remember_token'; }
            if ($type === 'softDeletes' && !$name)      { $name = 'deleted_at'; }
            if ($type === 'softDeletesTz' && !$name)    { $name = 'deleted_at'; }

            if (in_array($type, ['timestamps', 'nullableTimestamps']) && !$name) {
                $columns[] = $this->makeColumn('created_at', 'timestamp', true, []);
                $columns[] = $this->makeColumn('updated_at', 'timestamp', true, []);
                continue;
            }

            if ($type === 'morphs' && $name) {
                $columns[] = $this->makeColumn($name . '_id',   'unsignedBigInteger', false, []);
                $columns[] = $this->makeColumn($name . '_type', 'string',             false, []);
                continue;
            }

            if ($type === 'nullableMorphs' && $name) {
                $columns[] = $this->makeColumn($name . '_id',   'unsignedBigInteger', true, ['nullable']);
                $columns[] = $this->makeColumn($name . '_type', 'string',             true, ['nullable']);
                continue;
            }

            if (!$name) continue;

            $mods = [];
            if (str_contains($modifiers, '->nullable()')) $mods[] = 'nullable';
            if (str_contains($modifiers, '->unique()'))   $mods[] = 'unique';
            if (str_contains($modifiers, '->index()'))    $mods[] = 'index';
            if (str_contains($modifiers, '->primary()'))  $mods[] = 'primary';
            if (str_contains($modifiers, '->unsigned()')) $mods[] = 'unsigned';
            if (preg_match('/->default\s*\(\s*([^)]+)\s*\)/', $modifiers, $dm)) {
                $mods[] = 'default:' . trim(trim($dm[1]), "'\"");
            }

            $columns[] = $this->makeColumn($name, $type, in_array('nullable', $mods), $mods);
        }

        return $columns;
    }

    private function makeColumn(string $name, string $type, bool $nullable, array $modifiers): array
    {
        return ['name' => $name, 'type' => $type, 'nullable' => $nullable, 'modifiers' => $modifiers];
    }

    private function parseForeignKeys(string $content): array
    {
        $fks = [];

        // $table->foreign('col')->references('id')->on('table')
        preg_match_all(
            '/\$table\s*->\s*foreign\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)\s*->\s*references\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)\s*->\s*on\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/s',
            $content, $matches, PREG_SET_ORDER
        );
        foreach ($matches as $m) {
            $fks[] = ['column' => $m[1], 'references' => $m[2], 'on' => $m[3]];
        }

        // $table->foreignId('col')->constrained('table')
        preg_match_all(
            '/\$table\s*->\s*foreignId\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)(?:[^;]*?)->\s*constrained\s*\(\s*(?:[\'"]([^\'"]*)[\'"])?\s*\)/s',
            $content, $matches, PREG_SET_ORDER
        );
        foreach ($matches as $m) {
            $col   = $m[1];
            $table = !empty($m[2]) ? $m[2] : preg_replace('/_id$/', 's', $col);
            $fks[] = ['column' => $col, 'references' => 'id', 'on' => $table];
        }

        return $fks;
    }
}
