<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Validates a file's actual MIME type via libmagic (finfo), not just the
 * extension or the browser-supplied Content-Type header.
 */
class SafeFileType implements ValidationRule
{
    /** @param string[] $allowedMimes Real MIME types accepted, e.g. ['image/jpeg','image/png'] */
    public function __construct(private array $allowedMimes) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile || !$value->isValid()) {
            $fail('The :attribute could not be read.');
            return;
        }

        $path = $value->getRealPath();

        if (!$path || !is_readable($path)) {
            $fail('The :attribute could not be read.');
            return;
        }

        if (!function_exists('finfo_open')) {
            // finfo extension not available — skip magic-byte check (log a warning).
            logger()->warning('SafeFileType: finfo extension not available; magic-byte check skipped.');
            return;
        }

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = $finfo ? finfo_file($finfo, $path) : null;
        if ($finfo) finfo_close($finfo);

        if (!$realMime || !in_array($realMime, $this->allowedMimes, true)) {
            $fail('The :attribute file type is not allowed (detected: ' . ($realMime ?? 'unknown') . ').');
        }
    }
}
