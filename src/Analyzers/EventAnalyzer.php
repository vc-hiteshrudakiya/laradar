<?php

namespace Vcian\Laradar\Analyzers;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class EventAnalyzer
{
    public function __construct(private string $path) {}

    public function analyze(): array
    {
        if (!is_dir($this->path)) {
            return ['items' => [], 'errors' => []];
        }

        $listenerMap = $this->buildListenerMap();
        $items       = $errors = [];

        foreach (File::allFiles($this->path) as $file) {
            if ($file->getExtension() !== 'php') continue;
            try {
                $item = $this->processFile($file, $listenerMap);
                if ($item) $items[] = $item;
            } catch (\Throwable $e) {
                $errors[] = ['file' => $file->getRelativePathname(), 'message' => $e->getMessage()];
            }
        }

        return ['items' => $items, 'errors' => $errors];
    }

    // Build event class → [listener class, ...] map from EventServiceProvider $listen array
    private function buildListenerMap(): array
    {
        $map               = [];
        $providerLocations = [
            app_path('Providers/EventServiceProvider.php'),
            app_path('Providers/AppServiceProvider.php'),
        ];

        foreach ($providerLocations as $providerPath) {
            if (!file_exists($providerPath)) continue;

            $content = file_get_contents($providerPath);

            // Match the $listen = [...] property block
            if (!preg_match('/\$listen\s*=\s*\[(.*?)\];/s', $content, $block)) continue;

            // Each entry: EventClass::class => [ListenerA::class, ListenerB::class]
            preg_match_all(
                '/([A-Za-z][\w\\\\]+)::class\s*=>\s*\[(.*?)\]/s',
                $block[1],
                $entries,
                PREG_SET_ORDER
            );

            foreach ($entries as $entry) {
                $eventClass = class_basename(str_replace('\\', '/', $entry[1]));
                preg_match_all('/([A-Za-z][\w\\\\]+)::class/', $entry[2], $lm);
                $listeners = array_map(
                    fn($l) => class_basename(str_replace('\\', '/', $l)),
                    $lm[1]
                );
                $map[$eventClass] = array_merge($map[$eventClass] ?? [], $listeners);
            }
        }

        return $map;
    }

    private function processFile(SplFileInfo $file, array $listenerMap = []): ?array
    {
        $content = file_get_contents($file->getRealPath());

        if (!preg_match('/^namespace\s+([^;]+);/m', $content, $ns)) return null;
        if (!preg_match('/class\s+(\w+)/', $content, $cn)) return null;

        $name      = $cn[1];
        $namespace = trim($ns[1]);

        // Constructor properties = event payload
        $properties = [];
        if (preg_match('/public\s+function\s+__construct\s*\(([^)]*)\)/s', $content, $ctor)) {
            preg_match_all('/(?:public|protected|private|readonly)?\s*(?:\?)?([A-Za-z][\w\\\\]*)\s+\$(\w+)/', $ctor[1], $pm, PREG_SET_ORDER);
            foreach ($pm as $p) {
                $properties[] = ['type' => $p[1], 'name' => $p[2]];
            }
        }

        // Broadcast channel name from broadcastOn() method
        $broadcastChannel = null;
        if (preg_match('/function\s+broadcastOn\s*\([^)]*\)[^{]*\{(.*?)\}/s', $content, $bcm)) {
            if (preg_match('/[\'"]([^\'"]+)[\'"]/', $bcm[1], $chn)) {
                $broadcastChannel = $chn[1];
            }
        }

        return [
            'name'              => $name,
            'namespace'         => $namespace,
            'path'              => $this->relativePath($file->getRealPath()),
            'should_broadcast'  => str_contains($content, 'ShouldBroadcast'),
            'broadcastNow'      => str_contains($content, 'ShouldBroadcastNow'),
            'broadcast_channel' => $broadcastChannel,
            'properties'        => $properties,
            'listeners'         => $listenerMap[$name] ?? [],
        ];
    }

    private function relativePath(string $absolutePath): string
    {
        return ltrim(str_replace(base_path(), '', $absolutePath), DIRECTORY_SEPARATOR);
    }
}
