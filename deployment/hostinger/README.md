# IProFixer Hostinger Production Pipeline

## Purpose

This pipeline deploys the Laravel application to the existing split Hostinger layout:

```text
/home/u434649687/domains/iprofixer.com/
├── iprofixer_app/   # Laravel application, production .env, persistent storage
└── public_html/     # public index.php, .htaccess, storage link, compiled assets
```

It deliberately does **not** use Hostinger's direct Git-to-web-root deployment. That feature cannot safely manage the split application/public layout.

## Safety model

- Manual GitHub Actions dispatch only.
- Optional GitHub production-environment approval.
- Exact `main` commit is validated, built, packaged and checksummed.
- Full backend tests and frontend build run before upload.
- `vendor/` and `public/build/` are generated in GitHub Actions, not on Hostinger.
- Production `.env`, `storage/`, uploads, public entrypoint and storage link remain persistent.
- Existing application code and public assets are backed up before change.
- Deployments are serialized with a server lock.
- Vite assets are switched atomically.
- `/health`, `/ready`, and `/login` are verified after deployment.
- Any failed command or smoke check triggers automatic rollback.
- The five newest rollback backups are retained.

## Required GitHub environment and secrets

Create a GitHub Environment named `production`. Enable required reviewers if the repository plan supports them.

Add these environment secrets:

| Secret | Production value |
|---|---|
| `HOSTINGER_HOST` | Hostinger SSH host/IP, currently `45.84.207.235` |
| `HOSTINGER_PORT` | Hostinger SSH port, currently `65002` |
| `HOSTINGER_USER` | Hostinger SSH user, currently `u434649687` |
| `HOSTINGER_BASE_PATH` | `/home/u434649687/domains/iprofixer.com` |
| `HOSTINGER_SSH_PRIVATE_KEY` | Dedicated private deployment key |
| `HOSTINGER_KNOWN_HOSTS` | Verified SSH known-hosts line |
| `PRODUCTION_APP_URL` | `https://iprofixer.com` |

## One-time SSH-key setup

Generate a deployment-only key on a trusted local computer:

```powershell
ssh-keygen -t ed25519 -C "iprofixer-github-deploy" -f "$HOME\.ssh\iprofixer_github_deploy"
```

Use no passphrase because GitHub Actions must use the key non-interactively. Restrict this key to the Hostinger account and rotate it if exposure is suspected.

- Add `iprofixer_github_deploy.pub` in Hostinger under **SSH Access → SSH keys**.
- Add the private key contents from `iprofixer_github_deploy` to `HOSTINGER_SSH_PRIVATE_KEY`.
- Obtain the server host key:

```powershell
ssh-keyscan -p 65002 45.84.207.235
```

Verify the fingerprint through Hostinger or another trusted channel before saving the complete line as `HOSTINGER_KNOWN_HOSTS`.

## First production deployment

1. Merge the deployment-pipeline pull request into `main` after green CI.
2. Open **GitHub → Actions → Deploy production**.
3. Select **Run workflow** on `main`.
4. Enter `DEPLOY` exactly.
5. Leave **Run pending database migrations** disabled unless the release explicitly includes approved migrations.
6. Approve the `production` environment if required.
7. Watch validation, packaging, upload, backup, deployment and live smoke checks.
8. Accept the release only after checking the public website, Arabic, RFQ submission and admin workspace.

## Normal future deployment

1. Merge an approved green PR into `main`.
2. Run **Deploy production** manually.
3. Enable migrations only when the release contract requires them.
4. Verify the workflow evidence and live smoke tests.

## Persistent production state

The deployer never overwrites:

- `iprofixer_app/.env`
- `iprofixer_app/storage/`
- uploaded/private media
- `public_html/index.php`
- `public_html/.htaccess`
- `public_html/storage`

No production credentials or customer data belong in Git or release artifacts.

## Rollback and evidence

Server backups are stored under:

```text
/home/u434649687/domains/iprofixer.com/.deploy/backups/
```

Each backup includes the previous application code, runtime dependencies, non-entrypoint public assets and Vite build. A failed deployment restores these automatically and returns Laravel from maintenance mode.

The GitHub workflow also stores the release checksum as a 30-day artifact.

## Production invariants

- GitHub `main` is the authoritative code source.
- Deploy only a green named commit.
- Do not point Hostinger direct Git deployment at `public_html` or `iprofixer_app`.
- Do not run Composer or Node builds on shared hosting.
- Do not enable production migrations casually.
- Do not bypass the workflow with File Manager folder replacement.
