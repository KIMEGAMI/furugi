# CI/CD

This project uses GitHub Actions for CI and production deployment.

## CI

The CI workflow runs on pull requests, pushes to `main`, and manual dispatch.

- Install PHP dependencies with Composer.
- Install Node.js dependencies with npm.
- Generate a temporary application key from `.env.example`.
- Run the Laravel test suite.
- Build Vite assets.
- Audit Composer and npm dependencies.

## CD

The CD workflow deploys only after the `CI` workflow succeeds on `main`, or when it is run manually.

Production deployment is intentionally configured with GitHub Secrets and Variables. Do not commit server credentials or production `.env` values.

Required repository or environment secrets:

- `DEPLOY_HOST`: SSH host name or IP address.
- `DEPLOY_USER`: SSH user name.
- `DEPLOY_SSH_PRIVATE_KEY`: Private key for the deployment user.
- `DEPLOY_SSH_KNOWN_HOSTS`: Expected SSH host key entry.

Optional repository or environment variables:

- `DEPLOY_PATH`: Application directory on the server. Defaults to `/var/www/furugi`.
- `DEPLOY_PORT`: SSH port. Defaults to `22`.

The server must already have the production `.env` file, database, web server, Composer, Node.js, npm, and repository checkout prepared as described in `DEPLOY.md`.

The SSH deployment user must be allowed to run the deployment commands as `www-data` and reload Apache. Prefer a restricted passwordless sudo rule for only these commands.
