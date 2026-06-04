# Dockerised test environment

A reproducible container that mirrors the GitHub Actions CI
(`.github/workflows/testing.yml`, `code-quality.yml`): PHP **8.1** floor with the
extensions `dom fileinfo filter libxml xmlreader zip gd`. Use it to get the same
results locally that CI produces — independent of whatever PHP version is on your host.

## Requirements

- Docker Engine + Compose v2 (`docker compose`)

> This bundle is a library: `composer.lock` is gitignored and dependencies are
> resolved per PHP version (as CI does). `make install` runs `composer update`,
> and the container keeps its own `vendor/` in a named volume, isolated from the host.

## Quick start

```bash
make install        # build the image and resolve/install dependencies
make test           # run PHPUnit
make phpstan        # run PHPStan
make cs-check       # check code style (dry-run)
make cs-fix         # apply code style fixes
make ci             # install + test + phpstan + cs-check (the full CI run)
make shell          # interactive shell in the container
```

Run `make` (or `make help`) to list all targets.

## Choosing a PHP version

The CI test matrix is PHP 8.1–8.4. Override `PHP_VERSION` on any target:

```bash
make test PHP_VERSION=8.4
```

## Reproducing the full CI matrix

```bash
make matrix         # PHPUnit across PHP 8.1-8.4 x {lowest, highest} dependencies
```

`matrix` copies the project into a throwaway path inside the container and runs
`composer update` there, so your host `vendor/` and `composer.lock` are left untouched.

## How it works

- `docker/Dockerfile` — `php:${PHP_VERSION}-cli-bookworm` + `gd`/`zip` extensions + Composer.
- `compose.yaml` — bind-mounts the repo at `/app`, runs as your host UID/GID
  (set automatically by the Makefile), and keeps `vendor/` in a container-private
  named volume so it never clobbers (or is clobbered by) the host's `vendor/`.
- `HOME`/Composer cache live under `/tmp`, so the container works as any UID.

The container resolves and installs its own dependencies (`make install` →
`composer update`) into the named volume, so your host `vendor/` is left untouched.
The gitignored `composer.lock` lives in the bind-mounted repo and is regenerated for the
container's PHP version (harmless — it is an ephemeral artifact for a library). To rebuild
the container's dependencies (e.g. after changing `composer.json`), run `make install` again.
