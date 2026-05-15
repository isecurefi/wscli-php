<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Config;

use Symfony\Component\Yaml\Yaml;

/**
 * YAML-backed settings file. Mirrors the historical ~/.wscli/settings.yaml
 * shape so existing users' files keep working.
 *
 * Stored under a top-level `settings:` key:
 *
 *   settings:
 *     email: dan.forsberg@isecure.fi
 *     mode: data
 *     environment: test
 *     apikey: AB12...
 *     idtoken: eyJ...
 *     bank: nordea
 *     filetype: KTL
 *     filestatus: ALL
 *
 * Sensitive files are required to have 0600 permissions when loaded from
 * the user's home directory; the test suite can opt out with $strictPerms=false.
 */
final class Settings
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(
        private readonly ?string $path = null,
        private readonly bool $strictPerms = true,
    ) {
        if ($this->path !== null && is_file($this->path)) {
            $this->load();
        }
    }

    public static function defaultPath(): string
    {
        $home = getenv('HOME') ?: (getenv('USERPROFILE') ?: '');
        return rtrim($home, '/\\') . '/.wscli/settings.yaml';
    }

    public function path(): ?string
    {
        return $this->path;
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return $this->data;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        if ($value === null || $value === '') {
            unset($this->data[$key]);
            return;
        }
        $this->data[$key] = $value;
    }

    /** @param array<string, mixed> $values */
    public function merge(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function clear(string $key): void
    {
        unset($this->data[$key]);
    }

    public function save(): void
    {
        if ($this->path === null) {
            throw new \LogicException('Cannot save settings without a path.');
        }
        $dir = dirname($this->path);
        if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
            throw new \RuntimeException("Cannot create settings directory: $dir");
        }
        $yaml = Yaml::dump(['settings' => $this->data], 4, 2);
        if (file_put_contents($this->path, $yaml) === false) {
            throw new \RuntimeException("Cannot write settings to: {$this->path}");
        }
        @chmod($this->path, 0600);
    }

    private function load(): void
    {
        if ($this->strictPerms) {
            $mode = fileperms($this->path) & 0777;
            if ($mode !== 0600) {
                throw new \RuntimeException(sprintf(
                    'Refusing to read settings %s: file permissions are %o, expected 0600.',
                    $this->path,
                    $mode,
                ));
            }
        }
        $parsed = Yaml::parseFile($this->path);
        $this->data = is_array($parsed['settings'] ?? null) ? $parsed['settings'] : [];
    }
}
