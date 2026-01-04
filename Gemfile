source 'https://rubygems.org'

gem 'capistrano', '3.19.2'
gem 'capistrano-bundler'
gem 'capistrano-rbenv', '~> 2.0'
gem 'capistrano-php-fpm'
gem 'capistrano-composer'
gem "nokogiri", ">= 1.18.9"
gem "rack", ">= 3.1.18"
gem "uri", ">= 1.0.4"
gem "rexml", ">= 3.4.2"
gem "rack-session", ">= 2.1.1"
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
