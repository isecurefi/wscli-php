## WSCLI command line tool and SDK for PHP

## WSCLI

The `wscli` is [ISECure](https://www.isecure.fi) WS-Channel SaaS
command line client. It uses WSCLI SDK for PHP that is boosted version
of OpenAPI 2.0 generated client SDK. See details about the SDK below.

```shell
$ curl -LSs https://isecurefi.github.io/wscli-php/installer.php | php
$ ./wscli.phar --version
```

ISECure WS-Channel runs on AWS API Gateway and follows somewhat
RESTful API style. See
[OpenAPI 2.0 API specification](https://github.com/isecurefi/wsapi-v2)
and [online API documentation](https://isecure.fi/wsapi_v2/index.html)
for more information.

WS-Channel service supports banks in Finland and uses SEPA WebServices
interface towards banks. It supports certificate enrollment and file
transfers. Additionally, the services provides account management with
`admin` account and file transfers with `data` account (both use the
same email registration address and phone number). `admin` account
always requires SMS one-time password per login session
(2FA/MFA). This way `data` account credentials can be stored on
e.g. filesystem (with proper permissions) for automation and managemnt
credentials used separately (`admin` account password and phone).

_To-Be-Done_: PGP keys can be used to sign file uploads (with `data`
account) for authorization decisions, `n` of `m` signatures required
scheme, if needed. These PGP keys are imported as `authorization`
keys. Certificates and more precisely, their private keys, can be
imported and exported for the registered PGP keys imported as `export`
PGP keys.

Every account must have an API Key, e.g. integrator's API
Key. Certificates can be shared between accounts having the same API
Key - the account owner shares its certificates to other
accounts. This can be helpful if multiple accounts are required, but
only one set of bank certificates.

> **For clients WSCLI PHP SDK provides beefed up OpenAPI 2.0 generated
> SDK interface with WS-Channel service account registration, session
> login, and file transfers, and PGP based file upload
> authorizations. Clients do not need to worry about
> challenge-response fetching, RSA encryption or email/phone
> verification - all this happens automatically with WSCLI PHP SDK. In
> addition, the login session is preserved and checked in the settings
> file for expiration, which makes using the command line client
> easier.**

### Run wscli on Docker

You can run `wscli` through docker container.

Here is an example minimal `Dockerfile`.

```shell
FROM php:5.6-zts-jessie
RUN curl -LSs https://isecurefi.github.io/wscli-php/installer.php | php && \
    cp wscli.phar /usr/local/bin/wscli && \
    cp wscli.phar.pubkey /usr/local/bin/wscli.pubkey
```

And commands to use it. It assumes that you have `~/.wscli/settings.yaml`
configuration file properly setup. So, it runs the `wscli:latest` docker
container and the `wscli` command in it that we just installed when we
built the container.

```shell
docker build -t wscli:latest .
docker run -v ${HOME}/.wscli:/root/.wscli wscli wscli session login
```

### Install wscli tool

You can install `wscli` with the following commands. The tool is a
PHAR file.

```shell
$ curl -LSs https://isecurefi.github.io/wscli-php/installer.php | php
$ ./wscli.phar --version
```

Optionally setup as `/usr/local/bin/wscli`:

```shell
$ sudo cp wscli.phar /usr/local/bin/wscli
$ sudo cp wscli.phar.pubkey /usr/local/bin/wscli.pubkey
$ wscli --version
```

Tool can be updated and rollbacked if needed. We recommend checking
for updates frequently:

```
$ sudo wscli --update
$ sudo wscli --rollback
```

> _NOTE: The `wscli` itself is distributed as PHAR file. PHP was
> selected as the programming language to write beefed up SDK and
> command line client as most of our clients use PHP on their
> backends. Other languages are also supported on request (C#,
> Javascript, Python, ..)._

### Create configuration file

Create `~/.wscli/settings.yaml` file to keep default values for your
setup. wscli will also add session information into the file, so your
session is available for wscli commands readily.

```
settings:
    filetype: KTL
    filestatus: ALL
    bank: nordea
    environment: testing
    name: 'Dan Forsberg'
    phone: '+358404835507'
    email: dforsber@gmail.com
    company: 'ISECure Oy'
    apikey: <add your apikey or "0" for new regs and update once registered>
    password: <add 20 char long pw with big/small chars and spec chars and numbers>
    mode: admin
```

Note that the session information is not shown in the example above.

## WSCLI SDK for PHP

`wscli` uses WSCLI SDK for PHP that is based on OpenAPI 2.0 generated
client side SDK. It uses API to automatically fetch challenge,
utilizes required password RSA encryption and phone/email verification
logic during registration.

> **The SDK allows clients to focus on simple things, like calling
> `register()` and then `login()` with or without parameters (when
> settings are stored on configuration file).**

The SDK supports storing settings on a file, including session
credentials fetched during login. By default the file is
`~/.wscli/settings.yaml`. This allows running commands in a simple
form without too many arguments:

```
$ wscli session login
$ wscli files listFiles --bank=nordea --status=ALL --filetype=KTL
```

NOTE: _On the other parts than `account` and `session` the SDK is basically
on the same level OpenAPI 2.0 generated SDK. However, on the `files` API,
the SDK adds support for downloading set of files similar to the
listing files API. It just downloads file one by one using the
corresponding `downloadFile` API and by first calling `listFiles`._

### Download WSCLI SDK for PHP

See WSCLI SDK and WSCLI client releases on GitHub
[isecurefi/wscli-php](https://github.com/isecurefi/wscli-php) project.
The [API documentation](https://isecure.fi/wsapi_v2/index.html) and
OpenAPI 2.0 JSON API in
[isecurefi/wsapi-v2](https://github.com/isecurefi/wsapi-v2)
description are available online. See also
[wscli-php pages](https://isecurefi.github.io/wscli-php/).

- `wscli-php-sdk` includes beefed up OpenAPI 2.0 generated ISECure
  WS-Channel API client SDK to ease integration with PHP

- Command line tool `wscli` uses `wscli-php-sdk` and is distributed as
  single file PHAR.
