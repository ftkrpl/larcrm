<?php

require __DIR__ . '/vendor/autoload.php';

$componentsToFind = ['Section', 'Group', 'TextInput', 'Grid', 'Accordion', 'Fieldset','Textarea','Placeholder','TextEntry','Select'];
$basePath = __DIR__ . '/vendor/filament';

echo "=== HASIL PENCARIAN COMPONENT FILAMENT ===\n\n";

foreach ($componentsToFind as $component) {
    echo "Mencari $component...\n";
    
    // Gunakan perintah shell 'grep' atau 'find' jika di Windows pakai 'dir'
    // Tapi kita pakai RecursiveDirectoryIterator agar universal
    $directory = new RecursiveDirectoryIterator($basePath);
    $iterator = new RecursiveIteratorIterator($directory);
    $found = false;

    foreach ($iterator as $file) {
        if ($file->getFilename() === $component . '.php') {
            $content = file_get_contents($file->getPathname());
            if (preg_match('/namespace\s+(.+);/', $content, $matches)) {
                echo "✅ KETEMU! Namespace: \\" . $matches[1] . "\\" . $component . "\n";
                echo "   Lokasi: " . $file->getPathname() . "\n\n";
                $found = true;
            }
        }
    }

    if (!$found) {
        echo "❌ TIDAK DITEMUKAN: $component tidak ada di folder vendor/filament\n\n";
    }
}