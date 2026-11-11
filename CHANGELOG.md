# Changelog

## 3.0.2 — 2026-11-11

- Fix the Symfony YAML and Filesystem advisories by updating to 7.4.23; require patched YAML for fresh CLI/SDK installations.
- Refresh compatible runtime/development dependencies and update Box to 4.7.1.
- Add repeatable Composer audit commands and record dependency-audit evidence.

## 3.0.1 — 2026-11-11

- Add inclusive `--startdate` and `--enddate` filters to `files:list`, with date validation and matching synchronous/asynchronous PHP SDK query parameters.
- Return a failing CLI exit code for non-`00` file-list responses while preserving the bank response.
- Preserve upstream authentication behavior and existing positional SDK arguments.
- Package versions: SDK 3.0.1, CLI 2.0.1, generated client 2.0.1. Align the CLI dependency with the current SDK major version.

## 3.0.0 — 2026-11-09

- Make the existing stronger-profile helper the standard password API and add an explicit legacy selector; preserve explicit legacy selection for grandfathered tenants and refuse automatic fallback.
- Update production V2 availability and migration guidance.
- The CLI and low-level generated/Challenge APIs remain unchanged; migrate password flows to `Auth\PasswordProfile`.
- Keep password-reset discovery bound to its operation and identity.

