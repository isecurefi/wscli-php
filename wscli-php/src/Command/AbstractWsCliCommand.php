<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command;

use IsecureFi\WsApi\Auth\Challenge;
use IsecureFi\WsApi\Client\Api\AccountApi;
use IsecureFi\WsApi\Client\Api\CertsApi;
use IsecureFi\WsApi\Client\Api\FilesApi;
use IsecureFi\WsApi\Client\Api\IntegratorApi;
use IsecureFi\WsApi\Client\Api\PgpApi;
use IsecureFi\WsApi\Client\Api\SessionApi;
use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Configuration;
use IsecureFi\WsCli\Config\EnvironmentResolver;
use IsecureFi\WsCli\Config\Settings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractWsCliCommand extends Command
{
    protected ?Settings $settings = null;
    protected Configuration $config;
    protected EnvironmentResolver $envResolver;
    protected Challenge $challenge;

    /** @var array<string, mixed> Merged options: CLI > config file. */
    protected array $opts = [];

    public function __construct(?string $name = null)
    {
        parent::__construct($name);
        $this->envResolver = new EnvironmentResolver();
    }

    protected function configure(): void
    {
        $this
            ->addOption('config',      'c', InputOption::VALUE_REQUIRED, 'Path to settings.yaml.')
            ->addOption('environment', null, InputOption::VALUE_REQUIRED, 'API environment (test|production).')
            ->addOption('host',        null, InputOption::VALUE_REQUIRED, 'Override the API base URL.')
            ->addOption('json',        null, InputOption::VALUE_NONE,     'Emit machine-readable JSON output.')
            ->addOption('no-save',     null, InputOption::VALUE_NONE,     'Do not persist session changes back to settings.yaml.');

        if ($this->wantsCredentials()) {
            $this
                ->addOption('email',   'e', InputOption::VALUE_REQUIRED, 'Account email.')
                ->addOption('mode',    'm', InputOption::VALUE_REQUIRED, 'Account mode: admin or data.')
                ->addOption('apikey',  'a', InputOption::VALUE_REQUIRED, 'Integrator API key.')
                ->addOption('idtoken', 'i', InputOption::VALUE_REQUIRED, 'Authorization IdToken from a prior login.');
        }
    }

    /**
     * Commands that need API credentials override this to true. Defaults to true;
     * the few commands that don't (rollback, help) override to false.
     */
    protected function wantsCredentials(): bool
    {
        return true;
    }

    /** Whether this command must have a valid IdToken+ApiKey to run. */
    protected function requiresAuthenticatedSession(): bool
    {
        return false;
    }

    /** Whether this command demands admin mode (post-MFA). */
    protected function requiresAdminMode(): bool
    {
        return false;
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        try {
            return parent::run($input, $output);
        } catch (\InvalidArgumentException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return self::INVALID;
        }
    }

    final protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $configPath = $input->getOption('config') ?: Settings::defaultPath();
        $this->settings = new Settings(
            path: $configPath,
            strictPerms: ! $this->isPermsRelaxed(),
        );
        $this->opts = $this->mergeOptions($input);

        $this->config = $this->envResolver->build(
            $this->opts['environment'] ?? null,
            $this->opts['host'] ?? null,
        );

        $this->challenge = new Challenge(
            new SessionApi(null, $this->config),
            new AccountApi(null, $this->config),
        );

        $this->validate($input);
    }

    /**
     * Hook for subclasses to validate options after the merge step. Throw on bad input.
     */
    protected function validate(InputInterface $input): void
    {
        if ($this->requiresAdminMode() && strtolower((string)($this->opts['mode'] ?? '')) !== 'admin') {
            throw new \InvalidArgumentException('This command requires --mode=admin.');
        }
        if ($this->requiresAuthenticatedSession()) {
            foreach (['idtoken', 'apikey'] as $key) {
                if (empty($this->opts[$key])) {
                    throw new \InvalidArgumentException(
                        "Missing --$key (or settings.yaml entry). Run `wscli session login` first.",
                    );
                }
            }
        }
    }

    /** @return array<string, mixed> */
    private function mergeOptions(InputInterface $input): array
    {
        $merged = $this->settings?->all() ?? [];
        foreach ($input->getOptions() as $name => $value) {
            if ($value === null || $value === false) {
                continue;
            }
            $merged[$name] = $value;
        }
        return $merged;
    }

    /** Relaxed file perms for tests or CI. */
    private function isPermsRelaxed(): bool
    {
        return getenv('WSCLI_RELAX_PERMS') === '1';
    }

    protected function sessionApi(): SessionApi
    {
        return new SessionApi(null, $this->config);
    }

    protected function accountApi(): AccountApi
    {
        return new AccountApi(null, $this->config);
    }

    protected function filesApi(): FilesApi
    {
        return new FilesApi(null, $this->config);
    }

    protected function certsApi(): CertsApi
    {
        return new CertsApi(null, $this->config);
    }

    protected function pgpApi(): PgpApi
    {
        return new PgpApi(null, $this->config);
    }

    protected function integratorApi(): IntegratorApi
    {
        return new IntegratorApi(null, $this->config);
    }

    /**
     * Render a response object, scalar, or array as either pretty text or JSON
     * depending on the --json flag. Returns Command::SUCCESS / FAILURE.
     */
    protected function emit(InputInterface $input, OutputInterface $output, mixed $value, int $code = self::SUCCESS): int
    {
        $payload = $this->toPayload($value);
        if ($input->getOption('json')) {
            $output->writeln(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return $code;
        }
        $this->writeHuman($output, $payload);
        return $code;
    }

    private function toPayload(mixed $value): mixed
    {
        if (is_object($value) && method_exists($value, 'jsonSerialize')) {
            return $value->jsonSerialize();
        }
        if (is_object($value)) {
            return get_object_vars($value);
        }
        return $value;
    }

    private function writeHuman(OutputInterface $output, mixed $payload): void
    {
        if (is_scalar($payload) || $payload === null) {
            $output->writeln((string)$payload);
            return;
        }
        $output->writeln(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    protected function handleApiException(ApiException $e, OutputInterface $output): int
    {
        $output->writeln(sprintf(
            '<error>API error %d: %s</error>',
            $e->getCode(),
            $e->getMessage(),
        ));
        $body = $e->getResponseBody();
        if ($body) {
            $output->writeln(is_string($body) ? $body : json_encode($body, JSON_PRETTY_PRINT));
        }
        return self::FAILURE;
    }

    /**
     * Persist session-related values back to settings.yaml if --no-save was not passed.
     * @param array<string, mixed> $values
     */
    protected function persist(InputInterface $input, array $values): void
    {
        if ($input->getOption('no-save')) {
            return;
        }
        if ($this->settings === null || $this->settings->path() === null) {
            return;
        }
        $this->settings->merge($values);
        $this->settings->save();
    }
}
