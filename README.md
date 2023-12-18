Profile module
==============

Reports on macOS mobile configuration profile information 

Collects information about installed profiles by querying the `/usr/bin/profiles` command

### Profile Payload Data
By default the `profile` module uploads the profile's payload data as a JSON file to the MunkiReport server. In some cases this can result is very large file uploads or a very large `profile` database table. 

To prevent the module from uploading the profile's payload, set the `profile_upload_payload` of the `MunkiReport` domain to `FALSE` using either a profile or the below command.

`defaults write /Library/Preferences/MunkiReport.plist profile_upload_payload -bool False`

Table Schema
-----

* id - Unique ID
* serial_number - varchar(255) - Serial Number
* profile_uuid - varchar(255)  - UUID
* profile_name - varchar(255)  - Name
* profile_removal_allowed - varchar(255)  - Yes or No 
* payload_name - varchar(255) - Payload Name
* payload_display - varchar(255) - Payload Display Name
* payload_data - mediumtext - Payload Data
* timestamp - bigint - Unix timestamp when the report was uploaded
* profile_install_date - bigint - Unix timestamp when the profile was installed
* profile_organization - varchar(255) - Organization of the profile
* profile_verification_state - varchar(255) - Profile's verification state
* user - varchar(255) - User that the profile belongs to
* profile_description - mediumtext - Profile's description
* profile_method - varchar(255) - If profile is emulated MCX or native mobile configuration profile