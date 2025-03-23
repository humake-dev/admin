require 'asset_sync'  
require 'dotenv/load'

namespace :ci_asset_sync do
    desc "Synchronize assets to Azure"
    task :asset_sync do
        AssetSync.configure do |config|
            config.fog_provider = ENV['FOG_PROVIDER']
            config.azure_storage_account_name= ENV['AZURE_STORAGE_ACCOUNT_NAME']
            config.azure_storage_access_key= ENV['AZURE_STORAGE_ACCESS_KEY']
            config.fog_directory = ENV['FOG_DIRECTORY']
            config.prefix = 'assets'
            config.public_path = Pathname('./public')
            # Clear the default overrides
            config.file_ext_to_mime_type_overrides.clear
                
            # Add/Edit overrides
            # Will call `#to_s` for inputs
            config.file_ext_to_mime_type_overrides.add(:js, :"application/x-javascript")
        end
        AssetSync.sync
    end
end