set :application, 'humake_admin'
set :repo_url, 'your_repository'
set :branch, 'main'
# Default branch is :master
# ask :branch, `git rev-parse --abbrev-ref HEAD`.chomp

# Default deploy_to directory is /var/www/my_app_name

# Default value for :format is :airbrussh.
# set :format, :airbrussh
set :composer_install_flags, -> { "--no-dev --no-interaction --optimize-autoloader --working-dir=#{shared_path}/application" }
# You can configure the Airbrussh format using :format_options.
# These are the defaults.
set :format_options, command_output: true, log_file: 'application/logs/capistrano.log', color: :auto, truncate: :auto

# Default value for :pty is false
set :pty, true
set :rbenv_type, :user
set :rbenv_ruby, "3.3.4"

# Default value for :linked_files is []
append :linked_files, 'application/config/config.php', 'application/config/database.php', 'application/config/constants.php', 'application/config/layout.php', 'application/config/humake-fitness.json', 'application/composer.lock', '.env'

# Default value for linked_dirs is []
append :linked_dirs, 'public/files', 'application/logs', 'application/vendor'


namespace :deploy do
  desc 'Make Minify'
  task :make_minify do
    on roles(:app), in: :sequence, wait: 1 do
      within release_path do        
          execute "cd #{release_path}/public/assets/stylesheets;uglifycss --output common.min.css bootstrap.min.css animate.min.css bootstrap-datepicker.css style.css jquery.fancybox-1.3.4.css font-face-product.css index.css"
          execute "cd #{release_path}/public/assets/javascripts;uglifyjs --output common.min.js jquery.min.js popper.min.js bootstrap.min.js jquery-ui-1.10.3.custom.min.js jquery.form.min.js jquery.fancybox.1.3.4.js jquery.pagination.js bootstrap-datepicker.min.js moment.js common.js"
      end
    end
  end

  desc 'Asset Sync'
  task :asset_sync do
    on roles(:app), in: :sequence, wait: 1 do
      within release_path do
        execute "cd #{release_path};bundle exec rake -f application/config/asset_sync.rake ci_asset_sync:asset_sync"
      end
    end
  end

  after :finishing, 'deploy:make_minify'
  after :finishing, 'deploy:asset_sync'
  after :finishing, 'php_fpm:reload'
end

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
set :keep_releases, 5
