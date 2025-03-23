source 'https://rubygems.org'

gem 'capistrano', '3.19.2'
gem 'capistrano-bundler'
gem 'capistrano-rbenv', '~> 2.0'
gem 'capistrano-php-fpm'
gem 'capistrano-composer'
gem 'rake'

group :test do
    gem 'selenium-webdriver'
    gem 'websocket-native'
    gem 'rspec'
end

group :production do
    gem 'asset_sync','~> 2.8'
    gem 'fog-azure-rm', git: 'https://github.com/sleepinglion/fog-azure-rm'
    gem 'dotenv-rails'
end
