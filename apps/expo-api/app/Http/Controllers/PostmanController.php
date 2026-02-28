<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostmanController extends Controller
{
    /**
     * Base path where Postman collection files live.
     * In production the files are copied into the app at build time (see Dockerfile).
     */
    protected function basePath(): string
    {
        // In production: postman/ folder is inside the app root
        $appPath = base_path('postman');
        if (is_dir($appPath)) {
            return $appPath;
        }

        // Dev fallback: monorepo root /postman/
        $monoPath = dirname(base_path(), 2) . '/postman';
        if (is_dir($monoPath)) {
            return $monoPath;
        }

        abort(404, 'Postman collections directory not found');
    }

    /**
     * Available collections mapping: slug → filename
     */
    protected function collections(): array
    {
        return [
            'public-api'    => 'Public-API.postman_collection.json',
            'admin'         => 'Admin.postman_collection.json',
            'investor'      => 'Investor.postman_collection.json',
            'merchant'      => 'Merchant.postman_collection.json',
            'sponsor'       => 'Sponsor.postman_collection.json',
            'supervisor'    => 'Supervisor.postman_collection.json',
            'super-admin'   => 'SuperAdmin.postman_collection.json',
            'dashboard'     => 'Dashboard.postman_collection.json',
            'auth-service'  => 'Auth-Service.postman_collection.json',
        ];
    }

    /**
     * GET /docs/postman/collections
     * List all available collections with their folders (sections).
     */
    public function index()
    {
        $result = [];
        foreach ($this->collections() as $slug => $filename) {
            $path = $this->basePath() . '/' . $filename;
            if (!file_exists($path)) continue;

            $data = json_decode(file_get_contents($path), true);
            $folders = [];
            foreach ($data['item'] ?? [] as $item) {
                if (isset($item['item'])) { // It's a folder
                    $folderSlug = Str::slug($item['name']);
                    $folders[] = [
                        'name'  => $item['name'],
                        'slug'  => $folderSlug,
                        'count' => $this->countRequests($item),
                        'download' => url("/docs/postman/collection/{$slug}/{$folderSlug}"),
                    ];
                }
            }

            $result[] = [
                'name'      => $data['info']['name'] ?? $slug,
                'slug'      => $slug,
                'folders'   => $folders,
                'total'     => array_sum(array_column($folders, 'count')),
                'download'  => url("/docs/postman/collection/{$slug}"),
            ];
        }

        return response()->json(['collections' => $result]);
    }

    /**
     * GET /docs/postman/collection/{slug}
     * Download full collection as .json file.
     */
    public function downloadCollection(string $slug)
    {
        $collections = $this->collections();
        if (!isset($collections[$slug])) {
            abort(404, 'Collection not found');
        }

        $path = $this->basePath() . '/' . $collections[$slug];
        if (!file_exists($path)) {
            abort(404, 'Collection file not found');
        }

        return response()->download($path, $collections[$slug], [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * GET /docs/postman/collection/{slug}/{folder}
     * Download a single folder (section) from a collection as a mini-collection.
     */
    public function downloadFolder(string $slug, string $folderSlug)
    {
        $collections = $this->collections();
        if (!isset($collections[$slug])) {
            abort(404, 'Collection not found');
        }

        $path = $this->basePath() . '/' . $collections[$slug];
        if (!file_exists($path)) {
            abort(404, 'Collection file not found');
        }

        $data = json_decode(file_get_contents($path), true);
        $folder = null;

        foreach ($data['item'] ?? [] as $item) {
            if (isset($item['item']) && Str::slug($item['name']) === $folderSlug) {
                $folder = $item;
                break;
            }
        }

        if (!$folder) {
            abort(404, 'Folder not found in collection');
        }

        // Build a mini-collection wrapping just this folder
        $miniCollection = [
            'info' => [
                'name'        => $data['info']['name'] . ' — ' . $folder['name'],
                '_postman_id' => Str::uuid()->toString(),
                'schema'      => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
            'item' => [$folder],
        ];

        // Carry over collection-level auth if present
        if (isset($data['auth'])) {
            $miniCollection['auth'] = $data['auth'];
        }
        // Carry over collection-level variables
        if (isset($data['variable'])) {
            $miniCollection['variable'] = $data['variable'];
        }
        // Carry over collection-level events (pre-request / test scripts)
        if (isset($data['event'])) {
            $miniCollection['event'] = $data['event'];
        }

        $filename = Str::slug($data['info']['name']) . '-' . $folderSlug . '.postman_collection.json';

        return response()->json($miniCollection)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }

    /**
     * GET /docs/postman/environment/{type}
     * Download environment file (local or production).
     */
    public function downloadEnvironment(string $type = 'production')
    {
        $files = [
            'local'      => 'Maham-Expo-Environment.postman_environment.json',
            'production' => 'Maham-Expo-Production.postman_environment.json',
        ];

        if (!isset($files[$type])) {
            abort(404, 'Environment type not found. Use: local or production');
        }

        $path = $this->basePath() . '/' . $files[$type];
        if (!file_exists($path)) {
            abort(404, 'Environment file not found');
        }

        return response()->download($path, $files[$type], [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * GET /docs/postman/all
     * Download ALL collections + both environments as a single ZIP.
     */
    public function downloadAll()
    {
        $zipName = 'Maham-Expo-Postman-Collections.zip';
        $zipPath = storage_path('app/' . $zipName);

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Could not create ZIP archive');
        }

        $base = $this->basePath();

        // Add all collections
        foreach ($this->collections() as $filename) {
            $file = $base . '/' . $filename;
            if (file_exists($file)) {
                $zip->addFile($file, 'collections/' . $filename);
            }
        }

        // Add environments
        foreach (['Maham-Expo-Environment.postman_environment.json', 'Maham-Expo-Production.postman_environment.json'] as $env) {
            $file = $base . '/' . $env;
            if (file_exists($file)) {
                $zip->addFile($file, 'environments/' . $env);
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Count total requests recursively inside a folder/item.
     */
    private function countRequests(array $item): int
    {
        $count = 0;
        if (isset($item['request'])) {
            return 1;
        }
        foreach ($item['item'] ?? [] as $child) {
            $count += $this->countRequests($child);
        }
        return $count;
    }
}
