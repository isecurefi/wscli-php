<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\Unit\Config;

use IsecureFi\WsCli\Config\Settings;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SettingsTest extends TestCase
{
    private string $tmp;

    protected function setUp(): void
    {
        $this->tmp = tempnam(sys_get_temp_dir(), 'wscli-settings-');
        unlink($this->tmp);  // we want a clean path the Settings will create
    }

    protected function tearDown(): void
    {
        if (is_file($this->tmp)) {
            unlink($this->tmp);
        }
    }

    #[Test]
    public function it_returns_empty_state_when_file_missing(): void
    {
        $s = new Settings(path: $this->tmp, strictPerms: false);
        self::assertSame([], $s->all());
        self::assertNull($s->get('email'));
    }

    #[Test]
    public function set_and_save_round_trip(): void
    {
        $s = new Settings(path: $this->tmp, strictPerms: false);
        $s->set('email', 'a@b.c');
        $s->set('mode', 'data');
        $s->save();

        $reloaded = new Settings(path: $this->tmp, strictPerms: false);
        self::assertSame('a@b.c', $reloaded->get('email'));
        self::assertSame('data',  $reloaded->get('mode'));
    }

    #[Test]
    public function clear_drops_the_key(): void
    {
        $s = new Settings(path: $this->tmp, strictPerms: false);
        $s->set('idtoken', 'ey...');
        $s->clear('idtoken');
        self::assertNull($s->get('idtoken'));
    }

    #[Test]
    public function setting_null_or_empty_value_is_a_clear(): void
    {
        $s = new Settings(path: $this->tmp, strictPerms: false);
        $s->set('idtoken', 'ey...');
        $s->set('idtoken', null);
        self::assertNull($s->get('idtoken'));
        $s->set('idtoken', '');
        self::assertNull($s->get('idtoken'));
    }

    #[Test]
    public function merge_only_overrides_specified_keys(): void
    {
        $s = new Settings(path: $this->tmp, strictPerms: false);
        $s->merge(['email' => 'a@b.c', 'mode' => 'data', 'bank' => 'nordea']);
        $s->merge(['idtoken' => 'eyJ...']);
        self::assertSame('a@b.c', $s->get('email'));
        self::assertSame('eyJ...', $s->get('idtoken'));
    }

    #[Test]
    public function save_enforces_0600_permissions(): void
    {
        $s = new Settings(path: $this->tmp, strictPerms: false);
        $s->set('email', 'a@b.c');
        $s->save();
        self::assertSame(0600, fileperms($this->tmp) & 0777);
    }

    #[Test]
    public function strict_perms_refuses_to_read_a_world_readable_file(): void
    {
        file_put_contents($this->tmp, "settings:\n  email: a@b.c\n");
        chmod($this->tmp, 0644);

        $this->expectException(\RuntimeException::class);
        new Settings(path: $this->tmp, strictPerms: true);
    }

    #[Test]
    public function strict_perms_can_be_relaxed_for_tests(): void
    {
        file_put_contents($this->tmp, "settings:\n  email: a@b.c\n");
        chmod($this->tmp, 0644);
        $s = new Settings(path: $this->tmp, strictPerms: false);
        self::assertSame('a@b.c', $s->get('email'));
    }

    #[Test]
    public function default_path_lives_under_home_dot_wscli(): void
    {
        $original = getenv('HOME');
        putenv('HOME=/tmp/fake-home');
        try {
            self::assertSame('/tmp/fake-home/.wscli/settings.yaml', Settings::defaultPath());
        } finally {
            putenv('HOME=' . ($original === false ? '' : $original));
        }
    }
}
