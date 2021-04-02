Profile module
==============

Reports on macOS mobile configuration profile information 

Collects information about installed profiles by querying the `/usr/bin/profiles` command

Table Schema
-----

* id - Unique ID
* serial_number - varchar(255) - Serial Number
* profile_uuid - varchar(255)  - UUID
* profile_name - varchar(255)  - Name
* profile_removal_allowed - varchar(255)  - Yes or No 
* payload_name - varchar(255) - Payload Name
* payload_display - varchar(255) - Payload Display Name
* payload_data - text - Payload Data
* timestamp - bigint - Unix timestamp when the report was uploaded
* profile_install_date - bigint - Unix timestamp when the profile was installed
* profile_organization - varchar(255) - Organization of the profile
* profile_verification_state - varchar(255) - Profile's verification state
* user - varchar(255) - User that the profile belongs to
* profile_description - text - Profile's description
