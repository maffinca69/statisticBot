<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'statistic.maffinca.com');

// Project repository
set('repository', 'git@github.com:maffinca69/statisticBot.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false);

// Shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server
add('writable_dirs', []);


// Hosts

host('193.109.78.189')
    ->user('deployer')
    ->identityFile('~/.ssh/deployerkey')
    ->set('deploy_path', '/var/www/{{application}}')
    ->set(
        'composer_options',
        '{{composer_action}} --ignore-platform-reqs --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader'
    );


// Tasks

// Main deploy task which consist of 4 other steps
task('deploy', [
    'release',
]);

task('release', [
    'deploy:prepare',
    'deploy:release',
    'deploy:shared',
    'deploy:writable',
]);

task('restart:fpm', function () {
    run('sudo /etc/init.d/php7.4-fpm restart');
});
after('deploy:release', 'restart:fpm');
