<?php
$classLoader = require __DIR__ . '/../vendor/autoload.php';

// require __DIR__ . '/Assets/PhpFunctionOverrides.php';
// require __DIR__ . '/Assets/PhpFactoryFunctionOverrides.php';

\AdrianSuter\Autoload\Override\Override::apply($classLoader, [
    \Pluf\Http\Headers::class => [
        'getallheaders' => function () {
            if (array_key_exists('getallheaders_return', $GLOBALS)) {
                return $GLOBALS['getallheaders_return'];
            }

            return getallheaders();
        }
    ],
    \Pluf\Http\Message::class => [
        'header' => function (string $string, bool $replace = true, int $statusCode = null): void {
            \Pluf\Tests\Assets\HeaderStack::push([
                'header' => $string,
                'replace' => $replace,
                'status_code' => $statusCode
            ]);
        },
        'header_remove' => function ($name = null): void {
            \Pluf\Tests\Assets\HeaderStack::remove($name);
        }
    ],
    \Pluf\Http\NonBufferedBody::class => [
        'ob_get_level' => function (): int {
            if (isset($GLOBALS['ob_get_level_shift'])) {
                return ob_get_level() + $GLOBALS['ob_get_level_shift'];
            }

            return ob_get_level();
        }
    ],
    \Pluf\Http\UploadedFile::class => [
        'copy' => function (string $source, string $destination, $context = null): bool {
            if (isset($GLOBALS['copy_return'])) {
                return $GLOBALS['copy_return'];
            }

            if ($context === null) {
                return copy($source, $destination);
            }
            return copy($source, $destination, $context);
        },
        'is_uploaded_file' => function (string $filename): bool {
            if (isset($GLOBALS['is_uploaded_file_return'])) {
                return $GLOBALS['is_uploaded_file_return'];
            }

            return is_uploaded_file($filename);
        },
        'rename' => function (string $oldName, string $newName, $context = null): bool {
            if (isset($GLOBALS['rename_return'])) {
                return $GLOBALS['rename_return'];
            }

            if ($context === null) {
                return rename($oldName, $newName);
            }
            return rename($oldName, $newName, $context = null);
        }
    ],
    \Pluf\Http\StreamFactory::class => [
        'fopen' => function (string $filename, string $mode) {
            if (isset($GLOBALS['fopen_return'])) {
                return isset($GLOBALS['fopen_return']);
            }

            return fopen($filename, $mode);
        },
        'is_readable' => function (string $filename) {
            if ($filename === 'non-readable') {
                return false;
            }

            return is_readable($filename);
        }
    ]
]);
