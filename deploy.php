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
    ->set('deploy_path', '/var/www/{{application}}')
    ->set(
        'composer_options',
        '{{composer_action}} --ignore-platform-reqs --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader'
    );


// Tasks

task('artisan:config:cache', function() {})->setPrivate();
task('artisan:down', function() {})->setPrivate();
task('artisan:event:cache', function() {})->setPrivate();
task('artisan:event:clear', function() {})->setPrivate();
task('artisan:horizon:terminate', function() {})->setPrivate();
task('artisan:optimize', function() {})->setPrivate();
task('artisan:optimize:clear', function() {})->setPrivate();
task('artisan:route:cache', function() {})->setPrivate();
task('artisan:storage:link', function() {})->setPrivate();
task('artisan:up', function() {})->setPrivate();
task('artisan:view:cache', function() {})->setPrivate();
task('artisan:view:clear', function() {})->setPrivate();
task('restart:fpm', function () {
    run('sudo /etc/init.d/php7.4-fpm restart');
});

// Tasks
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'deploy:symlink',
    'deploy:unlock',
    'restart:fpm',
]);

after('deploy:failed', 'deploy:unlock');

