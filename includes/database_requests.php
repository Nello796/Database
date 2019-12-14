<?php
// External files
include "library.php";

// Variables
$database = file("../db/contacts.txt", FILE_SKIP_EMPTY_LINES);
$database_length = count($database);
$duplicate_result_number = array(TRUE, TRUE); 
$input_errors = array(
	"<p>Please insert your name.</p>",
	"<p>Please insert your last name.</p>",
	"<p>Please insert a correct phone number.</p>",
	"<p>Email address is alredy in the database.</p>",
	"<p>Phone number is alredy in the database.</p>"
);


// Check when a POST or GET request is send
if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {


    // *************************************************** //
    // *** If the request get the database *************** //
    if ($_REQUEST["data"]) {

        // Variables
        $database_to_sort = $_GET["database_to_sort"];
        $fname_sort = $_GET["fname_sort"];
        $lname_sort = $_GET["lname_sort"]; 
        $column_to_sort = 0; // [1] = Name / [2] = Last name
        $sort_type = 0;  // [1] = Increasing / [2] = Decreasing
        $database_to_send_key = 1;
        $sort_key_val = array();
        $results = array();
        $database_to_send = array();
        $key_database_value = "";

        // Transform each valuie in array
        for ($a = 1; $a < $database_length; $a++) {
		
            $database[$a] = explode("|", $database[$a]);
        }

        // Start if the sort button is pressed 
        if ($database_to_sort === "SORT") {
            
            // Check if name is going to be sorted set te value
            // $fname_sort = 0 - Don't sort : $fname_sort > 0 - Sort
            switch($fname_sort) {
                case "2":
                    $column_to_sort = 1;
                    $sort_type = 1;
		    break;

                case "1":
                    $column_to_sort = 1;
                    $sort_type = 2;
		    break;

		default: 
		    break;
            }

            // Check if last name is going to be sorted set te value
            // $lname_sort = 0 - Don't sort : $lname_sort > 0 - Sort
            switch($lname_sort) {
                case "2":
                    $column_to_sort = 2;
                    $sort_type = 1;
		    break;
                
                case "1":
                    $column_to_sort = 2;
                    $sort_type = 2;
		    break;
		
		default: 
		    break;
            }
    
            // ----- Create a new array whit keys = the value that we want to sort and values = contact from the database ----- //  
            // Create a new array and safe all the key's value from the database
            for ($b = 0; $b < $database_length; $b++) {
    
                $key_database_value = $database[$b][$column_to_sort];
                $key_database_value = strtolower($key_database_value);
                array_push($sort_key_val, $key_database_value);
            }

            // Save the contact corresponding to the key value
            // Example: if key is = 'Fabion', the value will be all fabion's datails from tadabase
            foreach ($sort_key_val as $key => $val) {
    
                // Skip the first row
                if ($key == 0) continue;
                $results[$val . $key] = $database[$key];
            }

            // ------------------------------------------------------------------ //
    
            // Sort from A-Z if it's 1 otherwise from Z-A if it's 2
            if ($sort_type == 1) {
    
                ksort($results);
            } elseif ($sort_type == 2) {
    
                krsort($results);
            }

            foreach ($results as $key => $val) { $database_to_send[$database_to_send_key] = $val; $database_to_send_key++;}
            echo $database_to_send = json_encode($database_to_send);
                
        } else {

            for ($a = 1; $a < $database_length; $a++) { $database_to_send[$a] = $database[$a]; }
            echo $database_to_send = json_encode($database_to_send);
        }

    }





    // *************************************************** //
    // *** If the request is to insert ******************* //
    if ($_POST["btn_insert"] == "Insert") {

        // Variables
        $first_name = trim($_POST["first_name"]);
        $last_name = trim($_POST["last_name"]); 
        $email = trim($_POST["email"]);
        $phone_number = trim($_POST["phone_number"]);
        $inputs_stauts = FALSE;
        $duplicate_phone_number = array(TRUE, TRUE);
        $duplicate_result_email = array();
        $validator_email = array();


        $inputs_stauts = check_main_inputs($first_name, $last_name, $phone_number); // Check main inputs
        $duplicate_phone_number = check_duplicate_inputs($phone_number, $database, $database_length, 4); // Check duplocate email
        // Check if the email is valid and it's not already in the database
        $duplicate_result_email = check_duplicate_inputs($email, $database, $database_length, 3);
        if ($duplicate_result_email[0]) {
            
            $validator_email = check_email($email);
        }

        //Write the variable's value in contancts.txt file
        if (!$inputs_stauts && $validator_email[0] && $duplicate_phone_number[0]) {

            // Search the the number of the registration on the first string saving it in a variable and add + 1
            $database_val = (int)substr($database[0], strpos($database[0], ":") + 1, strlen($database[0])) + 1;
            // Replace the new value with the old one
            $database[0] = preg_replace("/[0-9]+/", $database_val, $database[0]);
            // Create a string with all the datas
            $write_data = $database_val . "|" . $first_name . "|" . $last_name . "|" . $validator_email[1] . "|" . "$phone_number \n";
            // Add the string at the end of the array
            array_push($database, $write_data);
            // Rewrite the file
            file_put_contents("../db/contacts.txt", $database);
        }

        //Input errors
        if ($inputs_stauts) { echo $inputs_stauts; }
        if (!$duplicate_result_email[0]) { echo $input_errors[3]; }
        if (!$validator_email[0]) { echo $validator_email[1]; }
        if (!$duplicate_phone_number[0]) { echo $input_errors[4]; }
    }



    // *************************************************** //
    // *** If the request is to delete ******************* //
    if ($_GET["btn_delete"] == "Delete") {

        // Variables
        $delete_id = $_GET["delete_id"];

        // Transform each value in array
        for ($a = 1; $a < $database_length; $a++) {

            $database[$a] = explode("|", $database[$a]);
        }

        // Trasform the string with the id in an array
        $delete_id = explode(",", $delete_id);

        // Loop through all the taken from the checkboxes
        foreach ($delete_id as $row) {

            // Loop through all the database
            for ($a = 0; $a < $database_length; $a++) { 

                // If the id from the checkbox is equal to the data ID, delete the row
                // Otherwise delete the row
                if (trim($database[$a][0]) == $row) { unset($database[$a]); } // Delete the specific row
            }
        }

        // Transform each value in string
        for ($a = 1; $a < $database_length; $a++) { 

            $database[$a] = implode("|", $database[$a]); 
        }
        
        // Write the changes in the file deleting the old ones
        file_put_contents("../db/contacts.txt", implode($database));
    }



    // *************************************************** //
    // *** If the request is to update ******************* //
    if ($_REQUEST["update"]) {

        // Save the id from the button
        $id = $_GET["update"];
        // Start the loop going throught each row ot the database
        foreach ($database as $database_id) {
        
            // Skip the fist row
            if ($database_id < 1) continue;

            // Split the data in an array
            $data = explode("|", $database_id);
            // If the id is equeal to the id requested 
            if ($data[0] == $id) {

                // Print a another fieldset with all the data
                echo $id . "|" . $data[1] . "|" . $data[2] . "|" . $data[3] . "|" . $data[4];
            }
        }	
    }

    // *************************************************** //
    // *** If the request is to confirm the updates ****** //
    if ($_POST["action"] == "confirm_update") {

        // Variables
        $id_updated = trim($_POST["new_id"]);
		$name_updated = trim($_POST["new_name"]);
		$last_name_updated = trim($_POST["new_last_name"]);
		$email_updated = trim($_POST["new_email"]);
        $phone_number_updated = trim($_POST["new_phone_number"]);
        
        // Check inputs
        $check_main_inputs = check_main_inputs($name_updated, $last_name_updated, $phone_number_updated);
        $check_email = check_email($email_updated);

        // If the inputs are correct, end the script and print the errors
        if (is_string($check_main_inputs)) { echo $check_main_inputs; }
        if (!$check_email[0]) { echo $check_email[1]; }
        
        // Check if email and phone number are already in the database
        $duplicate_result_email = check_duplicate_inputs($email_updated, $database, $database_length, 3);
        $duplicate_result_number = check_duplicate_inputs($phone_number_updated, $database, $database_length, 4);

        // Check if the id is the same
		if ($duplicate_result_email[1] == $id_updated) { $duplicate_result_email = array(TRUE); } else { echo $input_errors[3]; }
		if ($duplicate_result_number[1] == $id_updated) { $duplicate_result_number = array(TRUE); } else { echo $input_errors[4]; }
	
		// If $email, phone number aren't in the database and all inputs are valid, make the update
		if ($duplicate_result_email[0] && $duplicate_result_number[0] && !is_string($check_main_inputs) && $check_email[0]) {
		// Check each row in the database till get the match id that we want to update
			for ($i = 1; $i <= $database_length; $i++) {

				// Split the of that id data in an array
				$data = explode("|", $database[$i]);
				//If the id is equeal to the id requested 
				if ($data[0] == $id_updated) {
		
                    // Replace the old data with the new ones
                    $data[1] = "|" . $name_updated . "|";
					$data[2] = $last_name_updated . "|";
					$data[3] = $email_updated . "|";
					$data[4] = $phone_number_updated . "\n";
					// Transform array into a string again
					$database[$i] = implode($data);
					// Rewrite the whole database
					file_put_contents("../db/contacts.txt", $database);
				}
			}
		}
    }
    

}
?>
