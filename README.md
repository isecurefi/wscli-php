# WSCLI and WSCLI SDK for PHP

## WSCLI

The `wscli` is [ISECure](https://www.isecure.fi) WS-Channel SaaS
command line client. It uses WSCLI SDK for PHP that is boosted version
of Swagger generated client SDK. See details about the SDK below.

> *NOTE: The `wscli` itself is distributed as PHAR file. PHP was selected
> as the first programming language to write beefed up SDK and command
> line client as most of our clients use PHP on their backends. Other
> languages are also supported on request (C#, Javascript, Python, ..).*

ISECure WS-Channel runs on AWS API Gateway and follows somewhat
RESTful API style. See
[Swagger API specification](https://isecure.fi/wsapi_v2.json) and
[online API documentation](https://isecure.fi/wsapi_v2/index.html) for
more information.

WS-Channel service supports banks in Finland and uses SEPA WebServices
interface towards banks. It supports certificate enrollment and file
transfers. **For clients it provides beefed up interface with WS-Channel
service account management, certificate enrollment, file transfers and
PGP based file upload authorizations. It also supports sharing
WS-Channel certificates between multiple accounts under the same
(integrator) API Key.** Read more from the
[online API documentation](https://isecure.fi/wsapi_v2/index.html).

Account includes both admin and data accounts. Admin account requires
SMS one-time password during login (MFA) and allows configuring the
account, e.g. managing PGP keys and linked accounts for certificate
sharing, importing and exporting certificates for PGP keys.

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
$ wscli --update
```

Note that `wscli` can be updated to the latest version with `wscli
--update` command. Rollback is also supported with `wscli --rollback`
command.

## WSCLI SDK for PHP

`wscli` uses WSCLI SDK for PHP that is based on Swagger generated
client side SDK. It uses API to automatically fetch challenge,
utilizes required password RSA encryption and phone/email verification
logic during registration.

> **The SDK allows clients to focus on simple things, like calling
> `register()` and then `login()` with or without parameters (when
> settings are stored on configuration file).**

The SDK supports storing settings on a file, including session
credentials fetched during login. By default the file is
`~/.wscli/settings.yaml`. This allows running commands like

```
$ wscli login
$ wscli files listFiles --status=ALL --filetype=KTL
```

On the other parts than `account` and `session` the SDK is basically
on the same level Swagger generated SDK. However, on the `files` API,
the SDK adds support for downloading set of files similar to the
listing files API. It just downloads file one by one using the
corresponding `downloadFile` API and by first calling `listFiles`.

### Download WSCLI SDK for PHP

See WSCLI SDK and WSCLI client releases on GitHub
[isecurefi/wscli-php](https://github.com/isecurefi/wscli-php) project.
