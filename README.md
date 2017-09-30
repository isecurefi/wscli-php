[![Dependency Status](https://www.versioneye.com/user/projects/59c778be2de28c79bdf1f43e/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/59c778be2de28c79bdf1f43e)

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

*To-Be-Done*: PGP keys can be used to sign file uploads (with `data`
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

> *NOTE: The `wscli` itself is distributed as PHAR file. PHP was
> selected as the programming language to write beefed up SDK and
> command line client as most of our clients use PHP on their
> backends. Other languages are also supported on request (C#,
> Javascript, Python, ..).*

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

NOTE: *On the other parts than `account` and `session` the SDK is basically
on the same level OpenAPI 2.0 generated SDK. However, on the `files` API,
the SDK adds support for downloading set of files similar to the
listing files API. It just downloads file one by one using the
corresponding `downloadFile` API and by first calling `listFiles`.*

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

