# Deployment

The theme is deployed with [Deployer](https://deployer.org) using `deploy.php`.

> **The hosts in `deploy.php` are Bornfight's infrastructure.** Both `staging` and `production` point at `services.bfs.wtf` with user `bwp` and deploy under `~/bwp_projects-staging/<slug>` and `~/bwp_projects/<slug>`. If a project deploys anywhere else, change the `host(...)` blocks, `set('projects', ...)`/`set('projects-staging', ...)`, the user and the identity file before the first deploy.

## What `deploy.php` defines

| Setting | Value |
|---------|-------|
| `application` | the theme slug |
| `repository` | the project's git URL (set by the init script from `--repo`) |
| `staging` | branch `staging` |
| `production` | branch `master` |
| `deploy_path` | `~/{{projects-staging}}/{{application}}` and `~/{{projects}}/{{application}}` |

## The `deploy` task, in order

1. `deploy:info`, `deploy:prepare`, `deploy:lock`, `deploy:release`
2. `deploy:update_code`: clones the branch from `repository` on the server
3. `local:build`: runs **`npm run build` on your machine**
4. `deploy:upload_dist`: uploads your local `static/dist/` to the release
5. `deploy:upload_vendor`: uploads your local `vendor/` to the release
6. `deploy:symlink`, `deploy:unlock`, `cleanup`

Because `static/dist/` and `vendor/` are git-ignored, they are **not** in the clone. They reach the server from the machine that runs the deploy. Nothing is built or installed on the server.

## Before you deploy

- Run the deploy from a checkout where `composer install` and `npm install` have completed. `vendor/` is uploaded as it is on disk, so install what you want live.
- Your SSH key (`~/.ssh/id_rsa` by default) must have access to the server and to the git repository, and the branch you deploy must be pushed.
- Deployer (`dep`) must be installed locally.

```bash
dep deploy staging
dep deploy production
```

## After the first deploy

The server-side WordPress install, database, `wp-config.php` and plugins are not part of this pipeline; they must exist already. ACF field groups arrive as JSON with the theme: sync them in wp-admin (*Custom Fields → Sync*) on the target environment when `acf-json/` changed.

## Theme updates

Separately from Deployer, the theme can check for updates against Bornfight's update service (`services.bfs.wtf`) using its `Identifier`. New projects receive a fresh random identifier from the init script, so they don't update from the starter's feed. See [Architecture](architecture.md#theme-update-checker).
