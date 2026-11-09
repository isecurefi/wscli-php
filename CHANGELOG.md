# Changelog

## 3.0.0 — 2026-11-09

- Make the existing stronger-profile helper the standard password API and add an explicit legacy selector; preserve explicit legacy selection for grandfathered tenants and refuse automatic fallback.
- Update production V2 availability and migration guidance.
- The CLI and low-level generated/Challenge APIs remain unchanged; migrate password flows to `Auth\PasswordProfile`.
- Keep password-reset discovery bound to its operation and identity.

