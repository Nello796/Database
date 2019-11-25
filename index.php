<?php

/*
 * Basic validation for an email address.
 * This function cat take just email address as a argument.
 * If the eamil is valid, it returns true if no an error/errors.
 */

// External files
include "includes/email_validator.php";
include "includes/print_and_sort_database.php";

// Variables
$database = file("db/contacts.txt", FILE_SKIP_EMPTY_LINES);
$database_length = count($database);
$first_name = NULL;
$last_name = NULL;
$phone_number = NULL;
$input_errors = array(
	"Please insert your name.",
	"Please insert your last name.",
	"Please insert a correct phone number"
);


/********************
 ****** Insert ******
 ********************/
// When insert button is pressed
if (!empty($_POST["insert"]) == 'Insert') {


	// Check if the input is empty
	if(array_key_exists("first_name", $_POST)) {
	       	
		$first_name = (strlen(trim($_POST["first_name"])) >= 1) ? $_POST["first_name"] : false; 
	}

	// Check if the input is empty
	if(array_key_exists("last_name", $_POST)) {

		$last_name = (strlen(trim($_POST["last_name"])) >= 1) ? $_POST["last_name"] : false; 
	}

	// Check if the input is empty and contain just numbers
	if(array_key_exists("phone_number", $_POST)) {

		$phone_number = (strlen(trim($_POST["phone_number"])) >= 1 && is_numeric($_POST["phone_number"])) ? $_POST["phone_number"] : false;
	}

	// Check if the email is valid
	if(array_key_exists("email", $_POST)) { 

		$email = trim($_POST["email"]);
		$validator_email = check_email($email);
	}

	// Write the variable's value in contancts.txt file
	if($first_name && $last_name && $phone_number && $validator_email[1]) {

		// Search the the number of the registration on the first string saving it in a variable and add + 1
		$database_val = (int)substr($database[0], strpos($database[0], ":") + 1, strlen($database[0])) + 1;
		// Replace the new value with the old one
		$database[0] = preg_replace("/[0-9]+/", $database_val, $database[0]);
		// Create a string with all the datas
		$write_data = $database_val . "|" . $first_name . "|" . $last_name . "|" . $validator_email[0] . "|" . "$phone_number \n";
		// Add the string at the end of the array
		array_push($database, $write_data);
		// Rewrite the file
		file_put_contents("db/contacts.txt", $database);
		// Refresh the page
		header("Refresh:0");
	}
}


/********************
 **** Update DB  ****
 ********************/ 
if(array_key_exists("update", $_POST)) {
	
	// When the confirm update button is pressed
	if($_POST["update"] == "update") {

		// Save the new variables
		$id_updated = $_POST["new_id"];
		$name_updated = $_POST["new_name"];
		$last_name_updated = $_POST["new_last_name"];
		$email_updated = $_POST["new_email"];
		$phone_number_updated = $_POST["new_phone_number"];	
	
		// Check each row in the database till get the match id that we want to update
		for($i = 1; $i <= $database_length; $i++) {

			// Split the of that id data in an array
			$data = explode("|", $database[$i]);
			//If the id is equeal to the id requested 
			if($data[0] == $id_updated) {
	
				// Replace the old data with the new ones
				$data[1] = "|" . $name_updated . "|";
				$data[2] = $last_name_updated . "|";
				$data[3] = $email_updated . "|";
				$data[4] = $phone_number_updated . "\n";
				// Transform array into a string again
				$database[$i] = implode($data);
				// Rewrite the whole database
				file_put_contents("db/contacts.txt", $database);
				// Refresh the page
				header("Refresh:0");
			}
		}
	}
}

/********************
 ****** Delete ******
 ********************/
if(array_key_exists("delete", $_POST)) {

	// When delete button is pressed
	if(!empty($_POST["delete"]) == "Delete") {

		foreach($_POST['checkbox'] as $row) {

			// Delete the specific row
			unset($database[$row]);
		}

		// Write the changes in the file deleting the old ones
		file_put_contents("db/contacts.txt", implode($database));
		
		// Refresh the page
		header("Refresh:0");
	}
}

?>

<html>
<head>
	<link rel="stylesheet" href="css/style.css">
</head>
<body>

	<!--  Errors --> 
	<div>
		<?php
		if($first_name === FALSE) { echo "<p>" . $input_errors[0] . "</p>"; }
		if($last_name === False) { echo "<p>" . $input_errors[1] . "</p>"; }
		if($phone_number === FALSE) { echo "<p>" . $input_errors[2] . "</p>"; }
		if(!$validator_email[1]) { echo "<p>" . $validator_email[0] . "</p>"; }
		?>
	</div>	

	<!---------------------------------->
	<!------------- Form --------------->
	<!---------------------------------->
	<form action="index.php" method="POST">
		<div class="input_wrap">

			<!-------------------------------------->
			<!-- Inputs for the database ----------->
			<div>
				<fieldset class="database_inputs insert">
					<legend>Insert</legend>
					<input type="text" name="first_name" placeholder="First Name">
					<input type="text" name="last_name" placeholder="Last Name">
					<input type="text" name="email" placeholder="Email">
					<input type="tel" name="phone_number" placeholder="Phone Number">
					<input type="submit" name="insert" value="Insert">
				</fieldset>
			</div>
	
			<!--------------------------------------------->
			<!-- Inputs to update the database ------------>
			<div>
				<?php

				// Start the script if a update button is pressed 
				if(array_key_exists("id", $_GET)) {

					if($_GET["id"]) {

						// Save the id from the button
						$id = $_GET["id"];
						// Start the loop going throught each row ot the database
						foreach($database as $database_id) {
						
							// Skip the fist row
							if($database_id < 1) continue;

							// Split the data in an array
							$data = explode("|", $database_id);
							// If the id is equeal to the id requested 
							if($data[0] == $id) {

								// Print a another fieldset with all the data
								echo " 
								<fieldset class='database_inputs update'>
									<legend>Update</legend>
									<input type='text' name='new_id' value='" . $id . "' class='update_id'>
									<input type='text' name='new_name' value='" . $data[1] . "' placeholder='First Name'>
									<input type='text' name='new_last_name' value='" . $data[2] . "' placeholder='Last Name'>
									<input type='text' name='new_email' value='" . $data[3] . "' placeholder='Email'>
									<input type='tel' name='new_phone_number' value='" . $data[4] . "' placeholder='Phone Number'>
									<button type='submit' name='update' value='update'>Confirm update</button>
								</fieldset>
								";
							}
						}	
					}
				}
				
				?>

			</div>
		</div>

		<!------------------------------------>
		<!-- Results from database ----------->
		<table>	
			<thead>
				<tr>
					<th>Checkbox</th>
					<th>First name</th>
					<th>Last name</th>
					<th>Email</th>
					<th>Phone number</th>
					<th>Update</th>
				</tr>
			</thead>
			<tbody>
			
				<!-- Print and sort database -->
				<?php print_and_sort_database($database, $database_length); ?>

			</tbody>
			<tfoot>
			</tfoot>
		</table>
	</form>

</body>
</html>
