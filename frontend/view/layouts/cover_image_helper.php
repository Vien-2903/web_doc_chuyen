<?php
if (!function_exists('resolve_cover_image_url')) {
    function resolve_cover_image_url($rawPath, $default = 'https://via.placeholder.com/180x240/2c3e50/ffffff?text=No+Image') {
        $rawPath = trim((string)($rawPath ?? ''));
        if ($rawPath === '') {
            return $default;
        }

        if (preg_match('#^https?://#i', $rawPath)) {
            return $rawPath;
        }

        $normalized = ltrim(str_replace('\\', '/', $rawPath), '/');
        if (strpos($normalized, 'web_doc_truyen/') === 0) {
            $normalized = substr($normalized, strlen('web_doc_truyen/'));
        }

        $legacyPrefixes = [
            'uploads/anhbia/',
            'frontend/public/uploads/anhbia/',
            'frontend/images/anhbia/',
            'images/anhbia/',
            'backend/uploads/anhbia/'
        ];

        foreach ($legacyPrefixes as $prefix) {
            if (strpos($normalized, $prefix) === 0) {
                $fileName = basename($normalized);
                if ($fileName === '' || $fileName === '.' || $fileName === '..') {
                    break;
                }

                $normalized = 'backend/uploads/anhbia/' . $fileName;
                break;
            }
        }

        if (strpos($normalized, 'backend/') !== 0 && strpos($normalized, 'frontend/') !== 0) {
            return '/web_doc_truyen/' . $normalized;
        }

        $projectRoot = dirname(__DIR__, 3);
        $relativeFsPath = str_replace('/', DIRECTORY_SEPARATOR, $normalized);
        $fullPath = $projectRoot . DIRECTORY_SEPARATOR . $relativeFsPath;

        if (is_file($fullPath)) {
            return '/web_doc_truyen/' . $normalized;
        }

        // If file does not exist yet, still return normalized URL for consistency.
        return '/web_doc_truyen/' . $normalized;
    }
}
