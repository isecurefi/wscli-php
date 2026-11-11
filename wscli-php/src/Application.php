<?php

declare(strict_types=1);

namespace IsecureFi\WsCli;

use IsecureFi\WsCli\Command;
use Symfony\Component\Console\Application as BaseApplication;

final class Application extends BaseApplication
{
    public const NAME    = 'wscli';
    public const VERSION = '2.0.2';

    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);

        $this->addCommands([
            // Session
            new Command\Session\InitLoginCommand(),
            new Command\Session\LoginCommand(),
            new Command\Session\LoginMfaCommand(),
            new Command\Session\LogoutCommand(),

            // Account
            new Command\Account\InitRegisterCommand(),
            new Command\Account\RegisterCommand(),
            new Command\Account\VerifyEmailCommand(),
            new Command\Account\VerifyPhoneCommand(),
            new Command\Account\InitPasswordResetCommand(),
            new Command\Account\PasswordResetCommand(),

            // Certs
            new Command\Certs\ListCertsCommand(),
            new Command\Certs\ConfigCertsCommand(),
            new Command\Certs\EnrollCertCommand(),
            new Command\Certs\ImportCertCommand(),
            new Command\Certs\ExportCertCommand(),
            new Command\Certs\ShareCertsCommand(),
            new Command\Certs\UnshareCertsCommand(),

            // Files
            new Command\Files\ListFilesCommand(),
            new Command\Files\UploadFileCommand(),
            new Command\Files\DownloadFileCommand(),
            new Command\Files\DeleteFileCommand(),
            new Command\Files\DownloadFilesCommand(),

            // Pgp
            new Command\Pgp\ListKeysCommand(),
            new Command\Pgp\UploadKeyCommand(),
            new Command\Pgp\DeleteKeyCommand(),

            // Integrator
            new Command\Integrator\ListAccountsCommand(),

            // Self-update
            new Command\SelfUpdateCommand(),
            new Command\RollbackCommand(),
        ]);
    }
}
