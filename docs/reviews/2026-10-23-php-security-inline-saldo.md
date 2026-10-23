# PHP security and inline saldo: three inline reviews

Branch: `codex/realtime-saldo-php-security-20261023`.
No package, PHAR, container image, or API documentation was published.

## Review 1: PHP security and dependency boundaries

Verified against the [October 21 PHP advisory](https://www.php.net/archive/2026.php#2026-10-21-5):
CVE-2026-109618 and CVE-2026-109619 are fixed in 8.3.36, 8.4.27 and 8.5.12.
The existing client supported PHP 8.3+, so the update retains that minimum minor
branch, with patched ranges for each supported minor. Build, development and
runtime images use PHP 8.3.36 pinned to the official image digest
`sha256:74caa458ad8d6c5e401eb9f8841a62faeab9b7be42b54cd37986d7685f857618`.

A plain minimum such as `^8.3.36` would also admit vulnerable 8.4/8.5 releases.
All four Composer manifests instead use explicit minor ranges. Fifteen boundary
cases check their agreement with the CLI's pre-autoload runtime check. Both the
source executable and packaged PHAR reject the available unpatched PHP 8.1.34.
A PHAR does not bundle or upgrade the host interpreter; the Docker image does.

Composer audit also identified existing advisories in Guzzle, PSR-7, phpseclib,
Symfony YAML and SDK development tooling. Updated the affected locked packages
and required dependencies. CLI, SDK and isolated build-tool audits now report no
known advisories. No vulnerability suppression or platform-version override was
introduced. The advisory's web-request attack paths are not a claim that this
outbound CLI itself accepts web requests.

## Review 2: UploadFile protocol and compatibility

The generated client previously deserialized UploadFile into `Response`, losing
unknown `Content`. Added `UploadFileResp extends Response` with optional raw
Base64 content; both synchronous and asynchronous paths select it. Existing
acknowledgements retain their original JSON shape and remain instances of
`Response`. The shared response model and other operations are unchanged.

Eight SDK transport cases cover all four upload entry points with and without
Content, HTTP 201, exact serialization, and exactly one PUT. Six CLI cases cover
inline reports, acknowledgements and refusals in both output modes. The existing
injected HTTP client is now passed through the FilesApi factory so these tests
exercise the actual command without contacting a bank. No simulator-specific
logic, automatic decoding, or follow-up download was added to either client.

Live gpgtest qualification uses a retained synthetic tenant and signed camt.060
queries. Independent requests can have different report creation timestamps;
qualification therefore compares account and balance values, while unit tests
assert byte-for-byte preservation of each received Base64 payload.

## Review 3: Packaging, distribution and release readiness

The first clean container build exposed an unused Box Git substitution that
required unavailable Git metadata. Removed it. Security-updated phpseclib's
polyfill dependency also caused Composer to classify Box as a runtime dependency
because Box declares that it replaces the polyfill. Moved Box into `tools/box`
with its own lockfile instead of shipping the compiler or weakening constraints.
The application has 26 runtime packages; Box and PHPUnit are absent from the
built PHAR. The inline response model is present. Build context excludes local
credentials, private worktrees, vendors, logs and old PHARs.

Validation:

- 101 CLI tests and 14 SDK tests pass on PHP 8.3.36, 8.4.27 and 8.5.12.
- CLI and SDK PHPStan level 5 pass (CLI uses a 512 MiB analysis memory limit).
- All three Composer projects validate and pass platform checks and audits.
  Existing explicit package versions produce advisory validation warnings, so
  `composer validate --strict` is not claimed as passing.
- The Docker image builds, starts, reports PHP 8.3.36, and contains a working PHAR.
- Local image tag: `wscli:php-security-20261023` (not pushed).
- PHAR SHA-256: `07dcf15f87e7d4cbaef6dcc69941849f5edee068bf8dfea18e9d80eb91c2a222`.

Private live evidence and the prepared PHAR are stored in the repository's
`.private/php-security-20261023/` directory, outside this worktree. That directory
contains credentials and must not be published. API contract documentation is
prepared in the worktree only; the pending public schema must be used when
regenerating the client, retaining these patched PHP requirements and tests.

Live acceptance completed: PHP SDK synchronous/asynchronous UploadFile and the
packaged CLI returned XSD-valid camt.052 with matching tenant balances, HTTP 201
on both SDK paths, and unchanged downloadable file inventory. Guarded operator
cleanup suspended the retained synthetic tenant and confirmed its stored state.
After the 60-second cache TTL, the same authenticated packaged CLI query returned
`ResponseCode: 01`, the expected suspended-access message, and no Content.
Accounts and audit streams were retained. No environment deployment was required:
these clients used the previously qualified gpgtest simulator release `47125355`.
