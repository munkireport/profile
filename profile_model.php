<?php

use CFPropertyList\CFPropertyList;

class Profile_model extends \Model
{
    public function __construct($serial = '')
    {
        parent::__construct('id', 'profile'); //primary key, tablename
        $this->rs['id'] = '';
        $this->rs['serial_number'] = $serial;
        $this->rs['profile_uuid'] = '';
        $this->rs['profile_name'] = '';
        $this->rs['profile_removal_allowed'] = ''; //Yes or No (not a boolean)
        $this->rs['payload_name'] = '';
        $this->rs['payload_display'] = '';
        $this->rs['payload_data'] = '';
        $this->rs['timestamp'] = 0; // Unix time when the report was uploaded
        $this->rs['profile_install_date'] = 0; // Unix time when the profile was installed
        $this->rs['profile_organization'] = '';
        $this->rs['profile_verification_state'] = '';
        $this->rs['user'] = '';
        $this->rs['profile_description'] = '';

        if ($serial) {
            $this->retrieve_record($serial);
        }

        $this->serial_number = $serial;
    }

    // ------------------------------------------------------------------------
    /**
     * Process data sent by postflight
     *
     * @param string data
     *
     **/
    public function process($data)
    {
       // If data is empty, throw error
        if (! $data) {
            throw new Exception("Error Processing Profile Module Request: No data found", 1);
        } else if (substr( $data, 0, 30 ) != '<?xml version="1.0" encoding="' ) { // Else if old style text, process with old text based handler

            // Translate profile strings to db fields
            $this->deleteWhere('serial_number=?', $this->serial_number);
            $translate = array(
                'ProfileUUID = ' => 'profile_uuid',
                'ProfileName = ' => 'profile_name',
                'ProfileRemovalDisallowed = ' => 'profile_removal_allowed',
                'PayloadName = ' => 'payload_name',
                'PayloadDisplayName = ' => 'payload_display',
                'PayloadData = ' => 'payload_data');
            foreach (explode("\n", $data) as $line) {
               // Translate standard entries
                foreach ($translate as $search => $field) {
                  //the separator is what triggers the save for each display
                  //making sure we have a valid s/n.
                    if ((strpos($line, '---------') === 0) && ($this->profile_uuid)) {
                        $this->id = 0;
                        $this->save(); //the actual save
                        $this->profile_uuid = null; //unset the display s/n to avoid writing twice if multiple separators are passed
                        break;
                    } elseif (strpos($line, $search) === 0) { //else if not separator and matches
                        $value = substr($line, strlen($search)); //get the current value
                        $this->$field = $value;
                        break;
                    }
                } //end foreach translate

             //timestamp added by the server
                $this->timestamp = time();
            } //end foreach explode lines

        } else { // Else process with new XML handler 

            // Process incoming profile.plist
            $parser = new CFPropertyList();
            $parser->parse($data, CFPropertyList::FORMAT_XML);
            $plist = $parser->toArray();

            // Delete existing data
            $this->deleteWhere('serial_number=?', $this->serial_number);

            // Process each profile/payload combo
            foreach ($plist as $profile){
                foreach ($this->rs as $key => $value) {
                    $this->rs[$key] = $value;
                    if(array_key_exists($key, $profile)) {
                        $this->rs[$key] = $profile[$key];
                    } else {
                        $this->rs[$key] = null;
                    }
                }

                // Save profile
                $this->id = '';
                $this->timestamp = time();
                $this->save();
            }
        }
    }
}
