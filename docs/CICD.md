# CI/CD Guide

## Overview

This project uses **GitHub Actions** for Continuous Integration and Continuous Deployment.
There are four workflow files in `.github/workflows/`:

| File | Trigger | Purpose |
|------|---------|---------|
| `ci.yml` | Every push + PRs | Lint, test, build frontend |
| `security.yml` | Weekly + lockfile changes | Vulnerability & secret scanning |
| `deploy-staging.yml` | CI passes on `develop` branch | Auto-deploy to staging |
| `deploy-production.yml` | Push a `v*` tag | Manual-approved deploy to production |

---

## Concepts to Learn

### Continuous Integration (CI)
Automatic verification every time you push code.
Goal: catch broken code **before** it reaches a shared branch.

### Continuous Deployment (CD)
Automatic (or semi-automatic) delivery of code to a server.
Goal: make releases fast, reliable, and repeatable.

### Why split them?
CI should be fully automatic and fast.
CD should have appropriate gates (tests must pass, human approval for prod).

---

## Workflow 1: `ci.yml`

### When it runs
- Every push to any branch
- Every pull request targeting `master`, `main`, or `develop`
- Cancels redundant runs if you push again quickly (`concurrency` setting)

### Jobs

```
push / PR
    │
    ├── lint          (Pint code style check)
    │       ↓ must pass
    ├── test          (PHPUnit × PHP 8.2, 8.3, 8.4)
    │
    └── build-frontend  (Vite build, runs in parallel with test)
```

### What each job does

#### `lint`
- Sets up PHP 8.4
- Installs Composer packages (cached)
- Runs `./vendor/bin/pint --test`
- Fails if any file needs reformatting
- **You learn**: automated code style enforcement

#### `test`
- Runs on a PHP **matrix**: 8.2, 8.3, 8.4
- Installs Composer packages (cached per PHP version)
- Copies `.env.example` → `.env`, generates key
- Runs `php artisan test --coverage`
- Uploads `coverage.xml` as an artifact (PHP 8.4 only)
- **You learn**: matrix builds, env configuration in CI, artifacts

#### `build-frontend`
- Sets up Node 20
- Runs `npm ci` (strict install from lockfile)
- Runs `npm run build` (Vite)
- Uploads `public/build` as an artifact
- **You learn**: Node caching, asset pipeline in CI

### Artifacts produced
- `coverage-report` — PHPUnit XML coverage report (7 days)
- `frontend-assets` — Compiled CSS/JS from Vite (7 days)

---

## Workflow 2: `security.yml`

### When it runs
- **Every Monday at 08:00 UTC** (scheduled)
- When `composer.lock` or `package-lock.json` changes
- Manually from the GitHub Actions tab (`workflow_dispatch`)

### Jobs

#### `php-audit`
- Runs `composer audit --abandoned`
- Fails on any HIGH or CRITICAL CVE
- **You learn**: supply chain security for PHP packages

#### `npm-audit`
- Runs `npm audit --omit=dev --audit-level=high`
- Skips dev dependencies (noise reduction)
- Fails only on HIGH or CRITICAL
- **You learn**: supply chain security for JavaScript packages

#### `secret-scan`
- Runs **Gitleaks** on the full git history (`fetch-depth: 0`)
- Detects accidentally committed API keys, passwords, tokens
- **You learn**: secret scanning, why `.env` must never be committed

### Tip
You can trigger this manually any time:
`GitHub → Actions tab → Security → Run workflow`

---

## Workflow 3: `deploy-staging.yml`

### When it runs
- When CI workflow **completes successfully** on the `develop` or `staging` branch
- Manually via `workflow_dispatch` with a ref input

### Secrets required
Set these in: `GitHub → Settings → Environments → staging`

| Secret | Example value | Description |
|--------|--------------|-------------|
| `STAGING_SSH_KEY` | `-----BEGIN OPENSSH PRIVATE KEY-----...` | Private SSH key for server access |
| `STAGING_HOST` | `123.45.67.89` | Server IP or hostname |
| `STAGING_USER` | `deploy` | SSH username |
| `STAGING_PATH` | `/var/www/staging` | Deploy directory on server |

### Variables (not secrets)
Set these in: `GitHub → Settings → Environments → staging → Variables`

| Variable | Example | Description |
|----------|---------|-------------|
| `STAGING_URL` | `https://staging.yourdomain.com` | Used for smoke tests and deployment URL display |

### Flow

```
CI passes on develop branch
        │
        ▼
    guard job     ← abort if CI failed
        │
        ▼
    deploy job
      1. Checkout code
      2. composer install --no-dev --optimize-autoloader
      3. npm ci + npm run build
      4. Write version.json (SHA + timestamp)
      5. rsync files to server (skips .git, .env, node_modules)
      6. SSH: php artisan down
      7. SSH: php artisan migrate --force
      8. SSH: php artisan config:cache / route:cache / view:cache
      9. SSH: php artisan queue:restart
     10. SSH: php artisan up
        │
        ▼
  smoke-test job
      • GET /api/health → expect 200 + {"status":"ok"}
      • GET /api/version → expect 200
        │
        ▼
   notify job     ← reports success or failure
```

### What you learn
- rsync-based file transfer
- SSH commands from CI
- Zero-downtime pattern (down → migrate → cache → up)
- Smoke tests after deploy
- GitHub Environments
- Deployment URL displayed in GitHub UI

---

## Workflow 4: `deploy-production.yml`

### When it runs
- When you push a **semver tag** like `v0.2.0` or `v1.0.0`
- Manually via `workflow_dispatch` with a tag input

### Differences from staging deploy
1. **Manual approval** required before deploy starts
2. **Automatic rollback** if smoke tests fail
3. **Creates a GitHub Release** after success
4. Uses separate `production` secrets

### Setting up approval gate
1. Go to `GitHub → Settings → Environments`
2. Create environment named `production`
3. Enable **Required reviewers**
4. Add yourself (or teammates) as reviewers
5. Every production deploy will pause and send you an email

### Secrets required
Set in: `GitHub → Settings → Environments → production`

| Secret | Description |
|--------|-------------|
| `PRODUCTION_SSH_KEY` | SSH private key for production server |
| `PRODUCTION_HOST` | Production server IP or hostname |
| `PRODUCTION_USER` | SSH username |
| `PRODUCTION_PATH` | Deploy path on production server |

### Variables
| Variable | Description |
|----------|-------------|
| `PRODUCTION_URL` | Used for smoke tests and release display |

### Flow

```
git tag v0.2.0 && git push origin v0.2.0
        │
        ▼
  approval job    ← PAUSES: sends email to reviewers
        │         ← reviewer clicks "Approve" in GitHub
        ▼
  deploy job      ← same as staging deploy
        │
        ▼
  smoke-test job
        │
        ├── pass → release job (creates GitHub Release)
        │
        └── fail → rollback job (reverts to previous commit)
        │
        ▼
  notify job
```

### How to create a release tag
```bash
# When you're ready to release
git tag v0.2.0
git push origin v0.2.0

# GitHub Actions picks this up automatically
```

### What you learn
- Protected environments with required approvals
- Semver release tagging
- Automatic rollback strategy
- GitHub Releases from CI
- Deployment history via GitHub UI

---

## Server Setup Checklist

Before staging/production workflows will work, the server needs:

```bash
# Minimum required on the server
php 8.2+              # apt install php8.4-fpm / brew install php
composer              # getcomposer.org
mysql or postgresql   # your DB
redis (optional)      # for queue/cache
node (optional)       # not needed if you rsync built assets

# SSH access
# Create a deploy user with SSH key auth:
adduser deploy
su - deploy
mkdir -p ~/.ssh
echo "YOUR_PUBLIC_KEY" >> ~/.ssh/authorized_keys
chmod 700 ~/.ssh && chmod 600 ~/.ssh/authorized_keys

# App directory
mkdir -p /var/www/staging
chown deploy:deploy /var/www/staging

# .env file on server (NOT deployed by CI — set manually once)
cp /var/www/staging/.env.example /var/www/staging/.env
# edit with real production values
php artisan key:generate
```

### Generate SSH key pair for CI
```bash
# Generate a dedicated deploy key
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/deploy_key -N ""

# Public key → add to server's authorized_keys
cat ~/.ssh/deploy_key.pub
# paste into: /home/deploy/.ssh/authorized_keys on the server

# Private key → add to GitHub secret
cat ~/.ssh/deploy_key
# paste into: GitHub → Settings → Environments → staging → STAGING_SSH_KEY
```

---

## Caching Strategy

### Why cache matters
Installing 80+ Composer packages on every CI run takes ~30-40 seconds without cache.
With cache it drops to ~5 seconds on a cache hit.

### How it works in `ci.yml`
```yaml
- uses: actions/cache@v4
  with:
    path: vendor
    key: composer-ubuntu-latest-8.4-<hash of composer.lock>
    restore-keys: composer-ubuntu-latest-8.4-
```

- `key` = exact match (cache hit → skip install entirely)
- `restore-keys` = prefix match (partial hit → install only new packages)
- Cache is invalidated automatically when `composer.lock` changes

Same pattern used for npm with `cache: "npm"` on the Node setup action.

---

## Secrets vs Variables

| Type | Visible in logs? | Encrypted? | Use for |
|------|-----------------|-----------|---------|
| **Secret** | Never | Yes | SSH keys, passwords, API tokens |
| **Variable** | Yes | No | URLs, config names, non-sensitive settings |

### Where to set them

| Scope | Location | Visible to |
|-------|----------|-----------|
| Repository | Settings → Secrets | All workflows |
| Environment | Settings → Environments → (name) → Secrets | Workflows using that environment |

---

## Branch Strategy for CI/CD

Recommended flow matching your current naming style:

```
feature/your-feature    → push triggers CI (lint + test + build)
        │
        ▼  PR
develop / Mail_Service  → CI passes → staging auto-deploy
        │
        ▼  PR
master                  → CI passes
        │
        ▼  git tag v0.x.x
production              → approval → deploy → smoke test → release
```

---

## Commit Messages for Workflow Files

Following your `Scope: Area Action` convention:

```
Mail: CI add GitHub Actions lint test build
Mail: CI add security audit workflow
Mail: CI add staging deploy with smoke tests
Mail: CI add production deploy with approval and rollback
```

---

## Local Simulation (Before Pushing)

Before pushing, simulate CI locally:

```bash
# Lint
./vendor/bin/pint --test

# Tests
php artisan test

# Tests with coverage
php artisan test --coverage

# Parallel tests
php artisan test --parallel

# Frontend build
npm run build

# Security audit
composer audit
npm audit --omit=dev --audit-level=high
```

If all pass locally, CI should pass too.

---

## Troubleshooting

### Lint fails on CI but not locally
Your local PHP Pint ran and reformatted the file.
CI uses `--test` (dry-run, no changes).

**Fix**:
```bash
./vendor/bin/pint        # reformat locally
git add . && git commit -m "Mail: CI fix Pint formatting"
git push
```

### Tests fail: "no such table"
The in-memory SQLite database didn't run migrations.
The `LazilyRefreshDatabase` trait in `TestCase.php` handles this.
If still failing, check that `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:` are set in the job env.

### SSH deploy fails: Permission denied
The public key is not in `authorized_keys` on the server, or the private key secret is wrong.

**Check**:
```bash
# Test SSH manually from your machine first
ssh -i ~/.ssh/deploy_key deploy@your-server-ip
```

### rsync fails: unknown host
`ssh-keyscan` step adds the host fingerprint.
If the host key changes (server rebuilt), the scan will update it automatically.

### Smoke test fails but app works
The smoke test waits 10-15 seconds.
If your server is slow to start (cold queue worker, etc.), increase `sleep`:

```yaml
- name: Wait for server to warm up
  run: sleep 30    # increase as needed
```

### "Required reviewers" not triggering
Make sure the workflow job uses `environment: production`.
The approval gate only works when a job targets a protected environment.

---

## Next Steps

### Short term
- [ ] Push to GitHub and see CI run for the first time
- [ ] Fix any lint or test failures
- [ ] Create a `develop` branch and watch staging auto-deploy (once server is set up)

### Medium term
- [ ] Set up a staging VPS (DigitalOcean, Hetzner, etc.)
- [ ] Add SSH secrets to GitHub Environments
- [ ] Test full staging deploy

### Long term
- [ ] Add Larastan/PHPStan job to `ci.yml`
- [ ] Add `coverage-minimum` enforcement (fail if coverage drops below 70%)
- [ ] Add Slack/Discord notifications to deploy workflows
- [ ] Add a scheduled `backup.yml` workflow for database backups
- [ ] Consider Docker-based deploy for more isolation

---

## File Map

```
.github/
└── workflows/
    ├── ci.yml                  ← Lint + Test + Build (every push)
    ├── security.yml            ← Vulnerability + secret scan (weekly)
    ├── deploy-staging.yml      ← Auto-deploy to staging (develop branch)
    └── deploy-production.yml   ← Manual-approved deploy to production (tags)

docs/
└── CICD.md                    ← This file
```

---

Last updated: March 17, 2026

