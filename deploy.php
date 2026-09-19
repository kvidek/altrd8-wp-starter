<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'altrd8-wp-starter');

set('release_name', function () {
    return (string) run('date +"%s"');
});

set('projects', 'bwp_projects');
set('projects-staging', 'bwp_projects-staging');

// Project repository
set('repository', 'git@github.com:kvidek/altrd8-wp-starter.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts

host('staging')
    ->hostname('services.bfs.wtf')
    ->stage('staging')
    ->set('branch', 'staging')
    ->user('bwp')
    ->port(22)
    ->identityFile('~/.ssh/id_rsa')
    ->forwardAgent(true)
    ->multiplexing(true)
    ->set('deploy_path', '~/{{projects-staging}}/{{application}}');

host('production')
    ->hostname('services.bfs.wtf')
    ->stage('production')
    ->set('branch', 'master')
    ->user('bwp')
    ->port(22)
    ->identityFile('~/.ssh/id_rsa')
    ->forwardAgent(true)
    ->multiplexing(true)
    ->set('deploy_path', '~/{{projects}}/{{application}}');

/**
 * Upload static files
 */
task('deploy:upload_dist', function () {
    writeln("<info>Uploading ./static/dist files to server</info>");
    upload('static/dist', '{{deploy_path}}/releases/{{release_name}}/themes/{{application}}/static');
});

/**
 * Upload vendor files
 */
task('deploy:upload_vendor', function () {
    writeln("<info>Uploading ./vendor files to server</info>");
    upload('vendor', '{{deploy_path}}/releases/{{release_name}}/themes/{{application}}');
});

/**
 * Local build
 */
task('local:build', function () {
    writeln("<info>Building static files</info>");
    runLocally('npm run build');
});

/**
 * Main task
 */
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'local:build',
    'deploy:upload_dist',
    'deploy:upload_vendor',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your project');


// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
after('deploy', 'success');
