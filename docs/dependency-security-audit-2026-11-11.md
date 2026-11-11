# Dependency security audit — 2026-11-11

Scope: Composer advisory checks, compatible dependency refresh, CLI/SDK unit
tests, static analysis and PHAR packaging. This is not a penetration test, a
complete source-code audit, or an OS/container vulnerability scan.

## Findings and changes

- **High: Symfony YAML parsing denial of service**, CVE-2026-123792 /
  [GHSA-hwfg-qwvh-2wfx](https://github.com/symfony/symfony/security/advisories/GHSA-hwfg-qwvh-2wfx).
  CLI and SDK runtime locks selected 7.4.21. Updated to 7.4.23 and raised both
  direct dependency constraints to `^7.4.23` to protect fresh consumer installs.
- **High: Symfony Filesystem path validation bypass**,
  [GHSA-6prc-h956-hxjq](https://github.com/symfony/symfony/security/advisories/GHSA-6prc-h956-hxjq).
  CLI/SDK development locks selected 7.4.11; the isolated Box build-tool lock
  selected 7.4.21. All now select 7.4.23. Filesystem is not a CLI runtime dependency.
- Refreshed dependencies within the existing supported constraints, including
  Guzzle 7.16.1, phpseclib 3.0.58, Monolog 3.12.1, PHPUnit 11.5.57 and Symfony
  Console 7.4.23. Updated the explicit Box pin from 4.7.0 to 4.7.1.
- No advisory ignores, insecure TLS options or disabled Composer security checks
  were introduced. New direct major-version migrations remain separate work.

## Qualification

Composer 2.9.8 advisory checks returned zero advisories and zero abandoned
packages for all three updated lockfiles. A fresh isolated resolution of the
generated client's standalone Composer manifest also passed its advisory audit.
That temporary lock is evidence only: the generated library retains its existing
no-lockfile distribution, and runtime dependencies are pinned by its consumers.

All 24 SDK tests and 115 CLI tests passed on PHP 8.3.36. Tests run in a
network-disabled container without mounted cloud/customer credentials. Dependency
bootstrap and registry advisory checks used networking separately. OrbStack,
working directories, caches and build outputs stayed on the external Development
volume. Both full static checks and the PHAR build/version/help smoke checks passed; the CLI static analysis requires a 512 MB analysis memory
limit instead of the container's 128 MB PHP default.

Raw before/after audit JSON, update logs, standalone resolution and qualification
logs: `/Volumes/Development/wscli-security-evidence-20261111`.

The presence of vulnerable dependencies does not establish exploitation; this
investigation did not inspect customer bank traffic. Updating
lockfiles does not update an already-installed PHAR; rebuild and distribute it.
