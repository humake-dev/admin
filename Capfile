set :deploy_config_path, 'application/config/deploy.rb'
# default stage_config_path is 'config/deploy'
set :stage_config_path, 'application/config/deploy'

# Load DSL and set up stages
require "capistrano/setup"

# Include default deployment tasks
require "capistrano/deploy"

# Load the SCM plugin appropriate to your project:
#
# require "capistrano/scm/hg"
# install_plugin Capistrano::SCM::Hg
# or
# require "capistrano/scm/svn"
# install_plugin Capistrano::SCM::Svn
# or
require "capistrano/scm/git"
install_plugin Capistrano::SCM::Git

# Include tasks from other gems included in your Gemfile
#
# For documentation on these, see for example:
#
#   https://github.com/capistrano/rvm
#   https://github.com/capistrano/rbenv
#   https://github.com/capistrano/chruby
#   https://github.com/capistrano/bundler
#   https://github.com/capistrano/passenger
#   https://github.com/capistrano/rails
#
require "capistrano/rbenv"
require "capistrano/bundler"
# require "capistrano/rails/assets"
# require "capistrano/rails/migrations"
#require "capistrano/passenger"
#require "capistrano/composer"
require 'capistrano/php_fpm/sysv_upstart'

# Load custom tasks from `lib/capistrano/tasks` if you have any defined
Dir.glob("lib/capistrano/tasks/*.rake").each { |r| import r }
