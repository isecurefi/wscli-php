<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\Unit\Command;

use IsecureFi\WsCli\Command\Certs\ConfigCertsCommand;
use IsecureFi\WsCli\Command\Certs\EnrollCertCommand;
use IsecureFi\WsCli\Command\Certs\ExportCertCommand;
use IsecureFi\WsCli\Command\Certs\ImportCertCommand;
use IsecureFi\WsCli\Command\Certs\ListCertsCommand;
use IsecureFi\WsCli\Command\Certs\ShareCertsCommand;
use IsecureFi\WsCli\Command\Certs\UnshareCertsCommand;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

final class CertsCommandsTest extends CommandTestCase
{
    private const AUTH = ['--idtoken' => 'ey.fake', '--apikey' => 'AB', '--mode' => 'admin'];

    #[Test]
    public function list_certs_demands_auth(): void
    {
        $t = $this->tester(new ListCertsCommand());
        $exit = $t->execute($this->withConfig());
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('Missing --idtoken', $t->getDisplay());
    }

    #[Test]
    public function config_certs_requires_admin_mode_and_a_valid_export_flag(): void
    {
        $t = $this->tester(new ConfigCertsCommand());
        $exit = $t->execute($this->withConfig([
            '--idtoken' => 'ey', '--apikey' => 'AB', '--mode' => 'data', '--export' => 'true',
        ]));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--mode=admin', $t->getDisplay());
    }

    #[Test]
    public function config_certs_rejects_garbage_export_value(): void
    {
        $t = $this->tester(new ConfigCertsCommand());
        $exit = $t->execute($this->withConfig(array_merge(self::AUTH, ['--export' => 'maybe'])));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--export must be true|false', $t->getDisplay());
    }

    #[Test]
    public function enroll_cert_requires_bank_pincode_and_wsuserid(): void
    {
        $t = $this->tester(new EnrollCertCommand());
        $exit = $t->execute($this->withConfig(array_merge(self::AUTH, ['--bank' => 'nordea'])));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--pincode', $t->getDisplay());
    }

    #[Test]
    public function import_cert_requires_all_pem_inputs(): void
    {
        $t = $this->tester(new ImportCertCommand());
        $exit = $t->execute($this->withConfig(array_merge(self::AUTH, [
            '--bank' => 'nordea',
            '--wsuserid' => 'WS',
        ])));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--certificate', $t->getDisplay());
    }

    #[Test]
    public function export_cert_requires_bank_and_pgpkeyid(): void
    {
        $t = $this->tester(new ExportCertCommand());
        $exit = $t->execute($this->withConfig(array_merge(self::AUTH, ['--bank' => 'nordea'])));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--pgpkeyid', $t->getDisplay());
    }

    #[Test]
    public function share_and_unshare_require_extemail(): void
    {
        foreach ([new ShareCertsCommand(), new UnshareCertsCommand()] as $cmd) {
            $t = $this->tester($cmd);
            $exit = $t->execute($this->withConfig(self::AUTH));
            self::assertSame(Command::INVALID, $exit, $cmd->getName() . ' must reject missing --extemail');
            self::assertStringContainsString('--extemail', $t->getDisplay());
        }
    }
}
