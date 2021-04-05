<?php
/**
 * profile module class
 *
 * @package munkireport
 * @author
 **/
class Profile_controller extends Module_controller
{
    /*** Protect methods with auth! ****/
    public function __construct()
    {
        // Store module path
        $this->module_path = dirname(__FILE__) .'/';
        $this->view_path = $this->module_path . 'views/';
    }

    /**
     * Default method
     *
     * @author
     **/
    public function index()
    {
        echo "You've loaded the profile module!";
    }

	/**
     * Retrieve data in JSON for widget
     *
     **/
    public function get_profile_groups()
    {
        $sql = "SELECT profile_name, COUNT(DISTINCT serial_number) AS count FROM profile 
                GROUP BY profile_name
                ORDER BY COUNT DESC";

        $out = array();
        $queryobj = new Profile_model;
        foreach ($queryobj->query($sql) as $obj) {
            if ("$obj->count" !== "0") {
                $obj->profile_name = $obj->profile_name ? $obj->profile_name : 'Unknown';
                $out[] = $obj;
            }
        }

        jsonView($out);
    } 

   	/**
     * Retrieve data for payload data popovers
     *
     **/
    public function get_payload_data($serial_number, $profile_uuid, $payload_name)
    {
        // Remove non-alphanumeric characters
        $serial_number = preg_replace("/[^A-Za-z0-9_\-.]]/", '', $serial_number);
        $profile_uuid = preg_replace("/[^A-Za-z0-9_\-.]]/", '', $profile_uuid);
        $payload_name = preg_replace("/[^A-Za-z0-9_\-.]]/", '', $payload_name);

        $sql = "SELECT payload_data
                        FROM profile 
                        WHERE serial_number = '$serial_number' AND profile_uuid = '$profile_uuid' AND payload_name = '$payload_name'
                        LIMIT 1;";

        $queryobj = new Profile_model;
        $returned_data = $queryobj->query($sql);

        // Check if we have data
        if (array_key_exists(0,$returned_data)){
            $json_string = $queryobj->query($sql)[0]->payload_data;
        } else{
            $json_string = "No Payload Data";
        }

        # Try to make it prettier
        $json_string = str_replace('\n', '<br />', $json_string);
        $json_string = str_replace(array('\\"', '"{', '}"','\''), '', $json_string);
        $json_string = str_replace('{}', 'No Payload Data', $json_string);
        $json_string = str_replace('null', 'No Payload Data', $json_string);
        echo '<div style="white-space: pre-wrap">'. $json_string.'</div>';        
    } 

	/**
     * Retrieve data in json format for client tab
     *
     **/
    public function get_data($serial_number = '')
    {
        // Remove non-serial number characters
        $serial_number = preg_replace("/[^A-Za-z0-9_\-]]/", '', $serial_number);

        $sql = "SELECT profile_name, profile_uuid, user, profile_method, payload_name, payload_display, serial_number, profile_removal_allowed, profile_removal_allowed, profile_install_date, profile_organization, profile_verification_state, profile_description
                        FROM profile 
                        WHERE serial_number = '$serial_number';";
        
        $queryobj = new Profile_model;
        jsonView($queryobj->query($sql));
    }
} // END class profile_controller
