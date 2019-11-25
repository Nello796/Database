<?php

/*
 * Basic validation for an email address.
 * This function cat take just email address as a argument.
 * If the eamil is valid, it returns true if no an error/errors.
 */


function check_email($email_address) {

        // Variables
        trim($email_address); // Delete empty spaces around the email.
	$allowed_SC = array("!", "#", "$", "%", "&", "'", "*", "+", "-", "/", "=", "?", "^", "`", "{", "|", "}", "~", '"', "@", ".", "_"); // Allowed before @
	$allowed_SC_domain = array("@", "."); // Allowed after in the domain.
	$email_address_SC = str_replace($allowed_SC, '', $email_address); // Replace allowed special character with nothing so the string can check if they are some not allowed.
	$check_at = substr($email_address, strpos($email_address, '@')); // Return the local name 'Until @'.
	$check_domain = str_replace($allowed_SC_domain, '', $check_at); // Replace @ and . with nothing so i can check if they are special character not allowed in the domain.
	$check_domain_suffix = substr($check_at, strpos($check_at, '.')); // Check the first . after @
	$check_domain_suffix_2 = explode('.', $check_domain_suffix); // Divide the suffix in two parts inside an array - [0] is the dot and [1] everithing after it
        $isValid = 0;
	$errors = NULL;

        // Array with all errors.
        $errors_list =  array(
			'<p>Please insert an email address.</p>',
        	'<p>You cannot have 2 or more @ in an email address.</p>',
        	'<p>You forgot to insert @ in your email address.</p>',
        	'<p>Please insert the domain "example: .com"</p>',
			'<p>You have unsupported characters.</p>',
			'<p>Special characters are not allowed in the domain.</p>',
			'<p>You cannot start with a . after @</p>',
			'<p>Please insert a correct domain suffix</p>'
        );

        // Check if the input is empty.
        if($email_address == '') {
                $errors = $errors . $errors_list[0];
                $isValid += 1;
        } else {

        	// Check if the email has more than one @.
        	if(substr_count($email_address, '@') > 1) {
               		$errors = $errors . $errors_list[1];
                	$isValid += 1;
        	}

        	// Check if the email doesn't have @.
        	if(substr_count($email_address, '@') == 0) {
                	$errors = $errors . $errors_list[2];
                	$isValid += 1;
        	}
        
        	// Check if there is an @.
        	if(substr_count($check_at, '.') == 0) {
                	$errors = $errors . $errors_list[3];
                	$isValid += 1;
		}

		// Check if the email has unsupported characters.
		if(!ctype_alnum($email_address_SC)) {
                	$errors = $errors . $errors_list[4];
        	        $isValid += 1;
		}
	
		// Check special character in the domain.
		if(!ctype_alnum($check_domain) && $check_domain != '') {
			$errors = $errors . $errors_list[5];
			$isValid += 1;
		}

		// Check . after @
		if(strpos($check_at, '.') <= 1) {
			$errors = $errors . $errors_list[6];
			$isValid += 1;
		}

		// Check domain suffix
		if(!ctype_alnum($check_domain_suffix_2[1])) {
			$errors = $errors . $errors_list[7];
			$isValid += 1;
		}	

	}

	// If the email has one or more errors return errors as a string and false, otherwise the email and true.
	if($isValid > 0) {

		return array($errors, false);
	} else {

		return array($email_address, true);
    }
}

?>
