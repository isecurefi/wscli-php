# WSCLI command-line tool and SDK for PHP

`wscli` is the [ISECure](https://www.isecure.fi) WS-Channel SaaS command-line
client. It is built on a modern OpenAPI Generator 7 PHP client wrapped by a
small auth helper (`IsecureFi\WsApi\Auth\Challenge`) and exposed through a
Symfony Console application. Requires **PHP 8.3+**.

The WS-Channel service is documented as
[OpenAPI 2.0](https://github.com/isecurefi/wsapi-v2) /
[online API docs](https://isecure.fi/wsapi_v2/index.html). It supports SEPA
WebServices file transfers with banks in Finland and handles certificate
enrollment (PKI) with automatic renewals.

The service has two account modes per email: **`admin`** (configuration, SMS
MFA on every login) and **`data`** (file transfers, no MFA — suitable for
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
| `Dockerfile` (repo root) | Multi-stage PHP 8.3 image that compiles and ships `wscli.phar` only. |
| `.dev/Dockerfile` | Developer container for running tests / phpstan / Box locally without depending on the host PHP install. |

---

## Run wscli with Docker

The repo root ships a multi-stage `Dockerfile` that builds the phar from
source and runs it on PHP 8.3:

```shell
docker build -t wscli:latest .
docker run -v "$HOME/.wscli:/root/.wscli" wscli session:login --json
```

---

## Install from a release phar

Download `wscli.phar` from the [GitHub releases](https://github.com/isecurefi/wscli-php/releases)
page, then:

```shell
$ chmod +x wscli.phar
$ ./wscli.phar --version
$ sudo mv wscli.phar /usr/local/bin/wscli
```

Build from source (requires PHP 8.3 + Composer + Box 4):

```shell
$ cd wscli-php-sdk && composer install
$ cd ../wscli-php   && composer install
$ vendor/bin/box compile
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
wscli session:login        --email --mode --password [--code --session] [--noninteractive]
wscli session:login-mfa    --email --code --session
wscli session:logout       --email --mode
```

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

The repo also includes `.dev/Dockerfile` — a PHP 8.3-cli image with
Composer + the required extensions so tests are reproducible regardless
of the host install:

```shell
docker build -t wscli-php-dev:8.3 .dev/
docker run --rm -v "$PWD":/app -w /app/wscli-php wscli-php-dev:8.3 \
    vendor/bin/phpunit
```

---

## Links

- Releases: <https://github.com/isecurefi/wscli-php/releases>
- API spec: <https://github.com/isecurefi/wsapi-v2>
- API docs: <https://isecure.fi/wsapi_v2/index.html>
