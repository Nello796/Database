<?php
function check_email($email_address) {

    /*
     * Basic validation for an email address
     * This function can take just email address as a argument
     * If the eamil is valid, it returns true if no an error/errors
     */

	// Variables ------- //
	trim($email_address); // Delete empty spaces around the email
	$allowed_special_character_local = array("!", "#", "$", "%", "&", "'", "*", "+", "-", "/", "=", "?", "^", "`", "{", "|", "}", "~", '"', "@", ".", "_"); // Allowed special character before @
	$allowed_special_character_domain = array("@", ".", "-"); // Allowed special character after @
	$isValid = 0; 
	$errors = NULL;

	// Local-Part
	$email_local = substr($email_address, 0, strpos($email_address, "@")); // Return the local name 'Before @'
	$email_local_alphanumeric = str_replace($allowed_special_character_local, "", $email_address); // Replace allowed special character in the string with nothing

	// Domain
	$email_domain = substr($email_address, strpos($email_address, "@")); // Return the domain name 'After @'
	$email_domain_alphanumeric = str_replace($allowed_special_character_domain, "", $email_domain); // Replace allowed special character in the string with nothing

	// Domain suffix & Subdomain
	$email_domain_suffix = substr($email_domain, strpos($email_domain, ".") + 1); // Return domain suffix 'After .'
	$email_subdomain = substr($email_domain_suffix, strpos($email_domain_suffix, ".") + 1); // Return subdomain 'Domain: @example.subdomain.suffix'

	// Array with all errors
	$errors_list =  array(
		"<p>Please insert an email address.</p>",
		"<p>You have unsupported characters.</p>",
		"<p>You cannot have 2 or more @ in an email address.</p>",
		"<p>Please insert the domain 'example: @domain.com'</p>",
		"<p>Special characters are not allowed in the domain.</p>",
		"<p>You cannot start with a . after @</p>",
		"<p>Please insert a correct domain</p>",
		"<p>Sorry, just one Subdomain is allowed</p>"
	);

	// Check if the input is empty
	if ($email_address == '') {

		$errors = $errors . $errors_list[0];
		$isValid += 1;
	} else {

		// Check if the local email has unsupported characters
		if (!ctype_alnum($email_local_alphanumeric)) {

			$errors = $errors . $errors_list[1];
			$isValid += 1;
		}

		// Check if the email has more than one @.
		if (substr_count($email_address, '@') > 1) {

			$errors = $errors . $errors_list[2];
			$isValid += 1;
		}

		// Check if the email doesn't have a domain "@domain.com"
		// 1) No '@' 
		// 2) No '.' 
		// 3) Domain < 4 character
		if (substr_count($email_address, '@') === 0 || substr_count($email_domain, '.') === 0 || strlen($email_domain) < 4 ) {

			$errors = $errors . $errors_list[3];
			$isValid += 1;
		}

		// Check special character in the domain
		if (!ctype_alnum($email_domain_alphanumeric) && $email_domain == "") {

			echo $email_domain . "\n";
			echo $email_domain_alphanumeric . "\n";
			$errors = $errors . $errors_list[4];
			$isValid += 1;
		}

		// Check if the domain starts with a .
		if (strpos($email_domain, '.') === 1) {

			$errors = $errors . $errors_list[5];
			$isValid += 1;
		}

		// Check if the domain suffix and subdomain are not empty
		if ($email_domain_suffix === "" || $email_subdomain === "") {

			$errors = $errors . $errors_list[6];
			$isValid += 1;
		}

		// Check if the domain suffix and subdomain start with a .
		if (strpos($email_domain_suffix, ".") === 0 || strpos($email_subdomain, ".") === 0) {

			$errors = $errors . $errors_list[6];
			$isValid += 1;
		}

		// Check if there is more than one subdomain
		if (substr_count($email_domain_suffix, ".") > 1) {

			$errors = $errors . $errors_list[7];
			$isValid += 1;
		}
	}

	// If the email has one or more errors return a string with relative errors and false, otherwise the email in lowercase and true.
	if ($isValid > 0) {

		return array(FALSE, $errors);
	} else {
		
		return array(TRUE, strtolower($email_address));
	}
}


function check_duplicate_inputs($input, $database, $database_length, $row) {
    
    /*
     * Check duplicate from imputs to database 
     * This function take database, database length, the input's value as a arguments and the value to check in the database "ROW/Array"
     * If the input is already present in the database it will return FALSE and the ID otherwise TRUE and the ID
     */

	 
    // Transform each valuie in array
    for ($a = 1; $a < $database_length; $a++) {

        $data[$a] = explode("|", $database[$a]);
    }
 
    // Check in each row if the input is already in the database 
    foreach ($data as $key => $val) {
        
        // If it's present return the ID and false
        if (trim($val[$row]) == trim($input)) {

            return  array(FALSE, $val[0]);
        }
    }

    // If it's NOT present return the ID and TRUE
    return array(TRUE, $val[0]);
}



function check_main_inputs($first_name, $last_name, $phone_number) {
    
    /*
     * Check each input
     * This function takes $forst_name $last_name and $phone_number as a parameter
     * If the inputs are valid return TRUE otherwise a string with relative errors
     */
	
	// Varaibles
	trim($first_name);
	trim($last_name);
	trim($phone_number);
	$inputs_check = TRUE;
	$errors_message = "";
	$input_errors = array(
		"<p>Please insert your name.</p>",
		"<p>Please insert a correct name.</p>",
		"<p>Please insert your last name.</p>",
		"<p>Please insert a correct last name.</p>",
		"<p>Please insert your phone number.</p>",
		"<p>Please insert a correct phone number.</p>"
	);

	// Check if name is not empty and contains just letters
	if (!strlen($first_name) > 0) { 

		$errors_message = $errors_message . $input_errors[0];
		$inputs_check = FALSE; 
	} else if (!ctype_alpha($first_name)) {
		
		$errors_message = $errors_message . $input_errors[1]; 
		$inputs_check = FALSE;
	}

	// Check if last name is not empty and contains jsut letters
	if (!strlen($last_name) > 0) {

		$errors_message = $errors_message . $input_errors[2]; 
		$inputs_check = FALSE; 
	} else if (!ctype_alpha($last_name)) { 
		
		$errors_message = $errors_message . $input_errors[3]; 
		$inputs_check = FALSE; 
	}

	// Check if phone number is not empty and contains just letters
	if (!strlen($phone_number) > 0) { 
		$errors_message = $errors_message . $input_errors[4]; 
		$inputs_check = FALSE; 
	} else if (!ctype_digit($phone_number)) {

		$errors_message = $errors_message . $input_errors[5];
		$inputs_check = FALSE; 
	}

	// Return true if each input is correct, otherwise a string with relative errors
	return (!$inputs_check) ? $errors_message : FALSE;
}
?>