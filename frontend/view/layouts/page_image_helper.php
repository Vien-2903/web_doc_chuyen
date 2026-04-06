<?php
if (!function_exists('resolve_page_image_url')) {
    function resolve_page_image_url($rawPath, $default = '') {
        $rawPath = trim((string)($rawPath ?? ''));
        if ($rawPath === '') {
            return $default;
        }

        if (preg_match('#^https?://#i', $rawPath)) {
            return $rawPath;
        }

        if (strpos($rawPath, '/') === 0 && strpos($rawPath, '/web_doc_truyen/') !== 0) {
            return $rawPath;
        }

        $normalized = ltrim(str_replace('\\', '/', $rawPath), '/');
        if (strpos($normalized, 'web_doc_truyen/') === 0) {
            $normalized = substr($normalized, strlen('web_doc_truyen/'));
        }

        $legacyPrefixes = [
            'uploads/trang/',
            'frontend/public/uploads/trang/',
            'frontend/images/trang/',
            'images/trang/',
            'backend/uploads/trang/'
        ];

        foreach ($legacyPrefixes as $prefix) {
            if (strpos($normalized, $prefix) === 0) {
                $fileName = basename($normalized);
                if ($fileName !== '' && $fileName !== '.' && $fileName !== '..') {
                    return '/web_doc_truyen/backend/uploads/trang/' . $fileName;
                }
                return $default;
            }
        }

        if (strpos($normalized, 'backend/') === 0 || strpos($normalized, 'frontend/') === 0) {
            return '/web_doc_truyen/' . $normalized;
        }

        return '/web_doc_truyen/' . $normalized;
    }
}
