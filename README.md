# WSCLI command-line tool and SDK for PHP

`wscli` is the [ISECure](https://www.isecure.fi) WS-Channel SaaS command-line
client. It is built on a modern OpenAPI Generator 7 PHP client wrapped by a
small auth helper (`IsecureFi\WsApi\Auth\Challenge`) and exposed through a
Symfony Console application. Requires **PHP 8.3.36+, 8.4.27+, or 8.5.12+** within those minor branches.

The WS-Channel service is documented as
[OpenAPI 2.0](https://github.com/isecurefi/wsapi-v2) /
[online API docs](https://isecure.fi/wsapi_v2/index.html). It supports SEPA
WebServices file transfers with banks in Finland and handles certificate
enrollment (PKI) with automatic renewals.

The service has two account modes per email: **`admin`** (configuration, SMS or
authenticator/TOTP MFA on every login) and **`data`** (file transfers, no MFA — suitable for
automation). Both modes share the same email and phone. The intended pattern
is to keep `data` credentials on a server filesystem (mode 0600) and use
`admin` separately when configuring the account.

Every account belongs to one API Key. Bank certificates can be shared between
accounts that share the same API Key, so a single set of bank certs can serve
multiple `data` accounts via one `admin` owner.

> **The SDK takes care of the protocol plumbing — challenge fetch, RSA-OAEP
> password encryption, phone/email verification, session token persistence.
> Clients call `account:register`, then `session:login`, and the rest is the
> usual REST surface (list files, upload, enroll, etc.).**

---

## Repository layout

| Path | Purpose |
|---|---|
| `wscli-php-sdk/` | The SDK: `IsecureFi\WsApi\Auth\Challenge` (RSA challenge/encrypt helper) + path-mapped OpenAPI Generator client. |
| `wscli-php-sdk/openapi-php-sdk/` | The regenerated client (namespace `IsecureFi\WsApi\Client`). Rebuild with `npx @openapitools/openapi-generator-cli generate -i wsapi_v2.json -g php -o openapi-php-sdk -c openapi-generator-config.json`. |
| `wscli-php/` | The CLI: Symfony Console application (`IsecureFi\WsCli\Application`) with 27 commands. |
| `wscli-php/box.json` | Box 4 phar build config. |
| `Dockerfile` (repo root) | Multi-stage PHP 8.3.36 image that compiles and ships `wscli.phar` only. |
| `.dev/Dockerfile` | Developer container for running tests / phpstan / Box locally without depending on the host PHP install. |

---

## Run wscli with Docker

The repo root ships a multi-stage `Dockerfile` that builds the phar from
source and runs it on PHP 8.3.36:

```shell
docker build -t wscli:latest .
docker run -v "$HOME/.wscli:/root/.wscli" wscli session:login --json
```

---

The runtime is pinned by version and image digest in both Dockerfiles. The
October 21, 2026 [PHP security release](https://www.php.net/archive/2026.php#2026-10-21-5)
fixes CVE-2026-109618 and CVE-2026-109619. Composer requirements reject older
patches in each supported branch. A PHAR contains application code, **not PHP**;
upgrade the interpreter on every host running it as well. The CLI checks its
runtime before loading dependencies, including when invoked from a PHAR.

## Install from a release phar

Download `wscli.phar` from the [GitHub releases](https://github.com/isecurefi/wscli-php/releases)
page, then:

```shell
$ chmod +x wscli.phar
$ ./wscli.phar --version
$ sudo mv wscli.phar /usr/local/bin/wscli
```

Build from source (requires a patched PHP runtime + Composer + Box 4):

```shell
$ composer install --working-dir=tools/box
$ cd wscli-php-sdk && composer install
$ cd ../wscli-php   && composer install
$ composer phar
$ ./wscli.phar --version
```

In-place phar updates (only meaningful when running from a phar):

```shell
$ wscli self-update
$ wscli rollback
```

> _Note: the Box 4 phar is not currently OpenSSL-signed. The legacy
> `wscli.phar.pubkey` is retained for compatibility with older installs;
> the new phar's signature is the embedded SHA-512 hash Box produces by
> default. Wire `algorithm: OPENSSL` + an external private key into
> `box.json` if you want a signed-release pipeline._

---

## Configure `~/.wscli/settings.yaml`

`wscli` reads defaults from `~/.wscli/settings.yaml` and persists session
material there after login. The file must be `0600`.

```yaml
settings:
    email: user@example.com
    mode: data
    environment: test           # or "production"
    bank: nordea
    filetype: KTL
    filestatus: ALL
    name: 'Your Name'
    phone: '+358401234567'
    company: 'Your Company Oy'
    apikey: <existing apikey or "0" for new registrations>
    password: <>= 20 chars, mixed case, digits, specials>
```

After `wscli session:login`, additional fields are appended:
`idtoken`, `apikey`, `expiresin`, and (when email is not yet verified)
`accesstoken`.

Use `-c /path/to/file.yaml` on any command to override the default path.

---

## Command reference

All commands group by API tag with a `tag:operation` naming scheme.

### Session
```
wscli session:init-login   --email --mode
wscli session:login        --email --mode --password [--code --session] [--mfa-type=totp|sms] [--noninteractive]
wscli session:login-mfa    --email --mode=admin --code --session [--challenge-name]
wscli session:logout       --email --mode
```

`session:login` prompts for the factor requested by the server and preserves its
challenge type when submitting the code. Authenticator accounts display
`Authenticator code:`; SMS accounts display `SMS code:`. Code entry is hidden.
The stand-alone `session:login-mfa` command accepts
`--challenge-name=SOFTWARE_TOKEN_MFA` for TOTP and defaults to `SMS_MFA` for
existing scripts. When the server offers a choice of factors, the CLI displays
a menu with authenticator first when available. Use `--mfa-type=totp` or
`--mfa-type=sms` to select an offered method without the menu; noninteractive
selection requires this option. The choice applies to this login and does not
change the account’s preferred factor. Failed or incomplete MFA returns
a nonzero exit code and leaves the saved session unchanged.

### Account
```
wscli account:init-register        --email --mode
wscli account:register             --email --mode --password --name --company --phone --apikey
wscli account:verify-phone         --email --mode --phone --code
wscli account:verify-email         --email --mode --code --accesstoken
wscli account:init-password-reset  --email --mode
wscli account:password-reset       --email --mode --password --code
```

### Certs (admin mode for mutating ops)
```
wscli certs:list
wscli certs:config    --export=true|false
wscli certs:enroll    --bank --pincode --wsuserid [--company]
wscli certs:import    --bank --wsuserid --certificate --privatekey [--enccertificate --encprivatekey]
wscli certs:export    --bank --pgpkeyid
wscli certs:share     --extemail
wscli certs:unshare   --extemail
```

### Files
```
wscli files:list      --bank [--filetype --filestatus]
wscli files:upload    --bank --filetype --filename (--filepath FILE | --filecontents BASE64) [--signatures]
wscli files:download  --bank --filetype --filereference [--output-file PATH]
wscli files:delete    --bank --filetype --filereference
wscli files:sync      --bank --syncdir DIR [--filetype --filestatus --filereference]
```

### PGP (admin mode for upload/delete)
```
wscli pgp:list
wscli pgp:upload  (--pgpkeycontents BODY | --pgpkeyfile FILE) [--pgpkeypurpose authorization|export]
wscli pgp:delete  --pgpkeyid
```

### Integrator
```
wscli integrator:list-accounts
```

### Global options (every command)
```
-c, --config FILE      ~/.wscli/settings.yaml override
    --environment ENV  test|production (default: test)
    --host URL         API base URL override (defaults from --environment)
    --json             Machine-readable JSON output
    --no-save          Do not persist session changes back to settings.yaml
-e, --email
-m, --mode             admin|data
-a, --apikey
-i, --idtoken
```

Run `wscli list` (or `wscli help <command>`) for the full surface.

---

## Examples

```shell
# Day-to-day data-mode flow
$ wscli session:login
$ wscli files:list --bank=nordea --filestatus=ALL --filetype=KTL
$ wscli files:download --bank=nordea --filetype=KTL \
        --filereference=ABC123 --output-file=./incoming.xml

# One-shot bulk sync
$ wscli files:sync --bank=nordea --filetype=KTL --syncdir=./inbox
```

---

## WSCLI SDK for PHP

The SDK is two layers:

- **`IsecureFi\WsApi\Client`** — the regenerated client (OpenAPI Generator 7,
  Guzzle 7). One `Api` class per tag (`SessionApi`, `AccountApi`, `CertsApi`,
  `FilesApi`, `PgpApi`, `IntegratorApi`), strongly-typed Model classes for
  every request/response shape.
- **`IsecureFi\WsApi\Auth\Challenge`** — the protocol helper. Fetches the
  per-session challenge from `initLogin` / `initRegister` and RSA-encrypts
  the password under the environment-specific service public key
  (phpseclib3, OAEP-SHA1, pure-PHP engine).

```php
use IsecureFi\WsApi\Auth\Challenge;
use IsecureFi\WsApi\Client\Api\SessionApi;
use IsecureFi\WsApi\Client\Configuration;
use IsecureFi\WsApi\Client\Model\LoginReq;

$cfg = (new Configuration())->setHost('https://ws-api.test.isecure.fi/v2');
$session = new SessionApi(null, $cfg);
$auth = new Challenge($session);

$challenge = $auth->fetch($email, 'data');
$encrypted = $auth->encryptPassword($password, $challenge, 'test');
$resp = $session->login($email, 'data',
    new LoginReq(['chResp' => $challenge, 'encrypted' => $encrypted]));

$idToken = $resp->getIdToken();
$apiKey  = $resp->getApiKey();
```

The CLI itself is a reference implementation of this two-layer model — see
`wscli-php/src/Command/` for one example per API operation.

---

## Tests

```shell
cd wscli-php-sdk && vendor/bin/phpunit            # 28 tests (6 unit + 22 e2e)
cd wscli-php     && vendor/bin/phpunit            # 76 tests (58 unit + 18 e2e)
```

Unit tests have no API dependencies. The e2e tests target
`ws-api.test.isecure.fi` and skip when `WSAPI_TEST_EMAIL` /
`WSAPI_TEST_PASSWORD` are not set. See `tests/E2E/E2ETestCase.php`
(SDK) and `tests/E2E/CliE2ETestCase.php` (CLI) for the full env-var
gate list — every mutating test is opt-in via a `WSAPI_TEST_ALLOW_*`
flag, and uploaded artifacts (files, PGP keys, cert shares) are
cleaned up in `tearDown`.

The repo also includes `.dev/Dockerfile` — a PHP 8.3.36-cli image with
Composer + the required extensions so tests are reproducible regardless
of the host install:

```shell
docker build -t wscli-php-dev:8.3.36 .dev/
docker run --rm -v "$PWD":/app -w /app/wscli-php wscli-php-dev:8.3.36 \
    vendor/bin/phpunit
```

---

## Links

- Releases: <https://github.com/isecurefi/wscli-php/releases>
- API spec: <https://github.com/isecurefi/wsapi-v2>
- API docs: <https://isecure.fi/wsapi_v2/index.html>

Box is installed separately under `tools/box` so its build-time polyfill
replacements cannot pull the compiler into the runtime dependency graph.

## Inline UploadFile responses

Some banks return a report directly from UploadFile. The SDK returns an
`UploadFileResp` (a subclass of `Response`) whose `getContent(): ?string` retains
the optional Base64 `Content`. Ordinary upload acknowledgements return `null`.
The synchronous and asynchronous methods both preserve it without decoding or
issuing a download request. The CLI includes `Content` in its normal JSON output:

```shell
wscli files:upload --bank osuuspankki --filetype camt.060.001.02 \
  --filename balance-query.xml --filepath balance-query.xml --json > upload-response.json
```

Read `ResponseCode` before using a response. For a successful query, decode
`Content` with strict Base64 decoding to obtain the bank's report. The bank's
query format and signing requirements still apply.

The checked-in `UploadFileResp` extension and UploadFile return types prepare
support for the pending API contract. When regenerating the OpenAPI client, use
the updated UploadFile schema and retain the patched PHP constraints; the
regression tests must continue to pass. No API specification has been published
as part of this change.


## Default password profile and legacy migration

Qualification (2026-11-08): this implementation passed local checks and
bounded hosted registration/login tests on ordinary gpgtest, including Admin MFA
and 283-byte UTF-8 passwords. RSA-3072 and RSA-4096 login were exercised.
Version 3.0.0 uses the stronger profile by default for password operations.
It is available on ordinary gpgtest and production V2. Select `PasswordProfile::LEGACY` with an explicit legacy environment
only for a grandfathered tenant that needs legacy authentication. There is no
endpoint inference or automatic fallback.

Additional qualification (2026-11-09): this SDK passed Data/Admin TOTP login,
reset initialization and invalid reset-code rejection against gpgtest Login 28.
The reset reached Cognito and was refused with CodeMismatchException. Login also
passed after an operator-managed synthetic password change. That change is not
a public password-reset test: successful reset with a delivered code remains
unqualified. Operator-confirmed email/phone attributes do not prove delivery.
The PHP qualification uses the profile helper and existing HTTP MFA continuation;
it does not establish an automatic MFA state machine.

This source adds `password-rsa-oaep-sha256-v1` on existing v2 routes. Availability
is environment-specific: the SDK requires matching server discovery and never
falls back if an older server ignores the selector. New tenants require the
stronger profile; grandfathered tenants can use either profile, including new accounts.

Use `IsecureFi\WsApi\Auth\PasswordProfile($configuration, $guzzleClient)` as
the standard password-authentication helper. Its omitted profile defaults to the stronger envelope. Its `register(email, mode, password, tenant, company, name,
phone)` and `login(email, mode, password)` return the API response without changing
MFA requirements. Use `initPasswordReset(email, mode)` followed by
`passwordReset(email, mode, code, newPassword)` on the same helper. Continue login
MFA with the existing SMS/TOTP helpers. The existing `Challenge` helper remains
legacy; it never auto-upgrades or falls back.

The profile uses direct RSA-3072/4096 OAEP with SHA-256/MGF1-SHA-256 and an empty
label. The binary envelope binds the exact challenge and preserves 1–283 UTF-8
password bytes without normalization. Discovery is not authorization. Admin MFA
still applies. Do not log password envelopes, tokens, codes or raw auth bodies.
Reset uses its own discovery; never substitute an InitLogin challenge or
automatically retry code delivery/password mutation. This feature does not establish
NIST/FIPS certification or successful recovery-code delivery.

### Install the 3.0.0 SDK release

Download the `wscli-php-sdk-3.0.0.tar.gz` GitHub release asset, extract it,
and run `composer install` in `wscli-php-sdk/`. The archive includes the
generated client used by its local Composer dependency. The CLI and generated low-level
client are unchanged; migrate password operations to the standard helper.
For explicit legacy behavior construct `PasswordProfile($config, $http,
PasswordProfile::LEGACY, Challenge::ENV_PRODUCTION)` (or `ENV_TEST`).
The low-level `Challenge` helper is an explicit legacy API, not an automatic
fallback or stronger-profile discovery client.

## Recovering previously downloaded bank files

Use a bounded date range when listing historical files; an unrestricted `ALL`
listing can exceed the service's response limits:

```shell
wscli files:list --environment=production --bank=osuuspankki \
  --filetype=pain.002.001.02 --filestatus=ALL \
  --startdate=2026-11-08 --enddate=2026-11-10 --json
```

Dates use `YYYY-MM-DD` and are inclusive. Either bound can be supplied alone;
bank support and the meaning of the file date depend on the bank. For OP, the
filter selects when the file was saved in the bank channel, not the payment's
execution date. Include later dates if feedback arrived later. Use returned
file references with `files:download` to retrieve individual files.

`files:list` preserves the bank's response code/text and exits unsuccessfully
when `ResponseCode` is not `00`; an error must not be treated as an empty list.

## Dependency security checks

Run Composer's advisory audit for every locked dependency set (including development tools):

```shell
composer audit --locked --working-dir=wscli-php
composer audit --locked --working-dir=wscli-php-sdk
composer audit --locked --working-dir=tools/box
```

Each project also exposes `composer run audit`. The generated client's runtime
dependencies are included in both consumer lockfiles. Its standalone development
dependencies can be resolved and audited separately with Composer.

After updating dependencies, run both unit suites and rebuild the PHAR: existing
PHAR installations contain bundled dependencies and need the rebuilt artifact.
See [the 2026-11-11 dependency audit](docs/dependency-security-audit-2026-11-11.md)
for findings, scope and qualification.
