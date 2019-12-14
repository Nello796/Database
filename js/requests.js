// ************************************* //
// *** Variables *********************** //
// ************************************* //

// Buttons 
$btn_sort_fname = $("#btn_sort_fname");
$btn_sort_lname = $("#btn_sort_lname");
$btn_insert = $("#btn_insert");
$btn_confirm_update = $("#btn_confirm_update");
$btn_delete = $("#btn_delete");

// Inputs insert
$first_name = $("#first_name");
$last_name = $("#last_name");
$email = $("#email");
$phone_number = $("#phone_number");

// Inputs update
$id_update = $("#id_update");
$first_name_update = $("#first_name_update");
$last_name_update = $("#last_name_update");
$email_update = $("#email_update");
$phone_number_update = $("#phone_number_update");

// General
$list_database = $("#list_database");
$list_errors = $("#errors");
$list_errors_insert = $("#errors-insert");
$list_errors_update = $("#errors-update");
$insert_error_footer = $("#insert-error-footer");
$update_error_footer = $("#update-error-footer");
$input_insert_success = $("#input-insert-success");
$input_update_success = $("#input-update-success");
let checkbox_checked = [];
let database_to_sort = "NSORT";
let fname_sort = ""; // Set type of sort on 0
let lname_sort = ""; // Set type of sort on 0
let data_id = "";
let data_contact = "";
const sort_up = "fas fa-sort-alpha-down-alt ml-3";
const sort_down = "fas fa-sort-alpha-up ml-3";

$input_insert_success.hide();
$input_update_success.hide();


// ************************************* //
// *** Print and sort database ********* //
// ************************************* //


// Set request to print the database
function print_database(database_to_sort, fname_sort, lname_sort) {

    $.ajax({
        url: "includes/database_requests.php" 
                                            + "?data=db" 
                                            + "&database_to_sort=" + database_to_sort
                                            + "&fname_sort=" + fname_sort
                                            + "&lname_sort=" + lname_sort,
        dataType: "JSON",
        method: "GET",
        success: function($data) {

            $list_database.empty();

            $.each($data, function(key, value) { 

                $list_database.append("<tr>");
                $list_database.append("<td>" 
			+ "<div class='custom-control custom-checkbox d-flex justify-content-center align-items-center'>" 
			+ "<input type='checkbox' class='checkbox custom-control-input' id='checkboxes" + value[0] + "' name='checkbox[]' value='" + value[0] + "'>"
			+ "<label class='custom-control-label mb-4' for='checkboxes" + value[0] + "'></label>"
			+ "</div>" 
			+ "</td>");

                $.each(value, function(key_a, value_a) {

                    // Skip the first key "ID"
                    if (key_a == 0) return true;
                    $list_database.append("<td class='align-middle text-center'>" + value_a + "</td>");
                });

                $list_database.append("<td><i id='" + value[0] + "' class='fas fa-user-edit btn_update align-middle text-center w-100' value='Update' data-toggle='modal' data-target='#update-contact'></i></td>");
                $list_database.append("</tr>");
            });	

        },
        error: function(jqXHR) {

            $list_database.empty();
            $list_errors.append("<p>Sorry, there was a problem with the server.</p>");
            $list_errors.append("<p>Error: " + jqXHR.statusText + " (" + jqXHR.status + ")</p>");
        },
        timeout: 5000
    });
}

// Start the function when button sort name is clicked
$btn_sort_fname.click(function() {

	fname_sort = "";
	lname_sort = "";

	if($btn_sort_fname.attr("name") === "up") {

            database_to_sort = "SORT";
            fname_sort = "2";
            $btn_sort_fname.attr("name", "down");
	    $btn_sort_fname.attr("class", sort_up);
            print_database(database_to_sort, fname_sort, lname_sort);
	} else if ($btn_sort_fname.attr("name")) {

            database_to_sort = "SORT";
            fname_sort = "1";
            $btn_sort_fname.attr("name", "up");
	    $btn_sort_fname.attr("class", sort_down);
            print_database(database_to_sort, fname_sort, lname_sort);
	}
})

// Start the function when button sort last name is clicked
$btn_sort_lname.click(function() {

	fname_sort = "";
	lname_sort = "";

	if ($btn_sort_lname.attr("name") === "up") {
		
		database_to_sort = "SORT";
		lname_sort = "2";
		$btn_sort_lname.attr("name", "down");
		$btn_sort_lname.attr("class", sort_up);
		print_database(database_to_sort, fname_sort, lname_sort);
	} else if ($btn_sort_lname.attr("name") === "down") {
		
		database_to_sort = "SORT";
		lname_sort = "1";
		$btn_sort_lname.attr("name", "up");
		$btn_sort_lname.attr("class", sort_down);
		print_database(database_to_sort, fname_sort, lname_sort);
	}
})

// Print database
print_database();


// ************************************* //
// *** Insert on database ************** //
// ************************************* //

$btn_insert.click(function() {

    $.ajax({
        url: "includes/database_requests.php",
        dataType: "text",
        method: "POST",
        data: {
            "btn_insert": "Insert",
            "first_name": $first_name.val(),
            "last_name": $last_name.val(),
            "email": $email.val(),
            "phone_number": $phone_number.val()
        },
        success: function ($data) {

            $list_errors_insert.empty();
	    // End callback in case there is any errors and print them
            if ($data.length > 0) { 
		    $list_errors_insert.append($data);
		    $insert_error_footer.removeClass("d-none");
		    return false; 
	    } 

            $first_name.val("");
            $last_name.val("");
            $email.val("");
            $phone_number.val("");
	    $insert_error_footer.addClass("d-none");
	    $input_insert_success.show("fast");
	    setTimeout(function() {

		    $("#insert-contact").modal("hide");
		    $input_insert_success.hide();
	    }, 1500);

            // Print database
            print_database();

        },
        error: function(jqXHR) {

            $list_database.empty();
            $list_errors.append("<p>Sorry, there was a problem with the server.</p>");
            $list_errors.append("<p>Error: " + jqXHR.statusText + " (" + jqXHR.status + ")</p>");
        },
        timeout: 5000
    });
})


// ************************************* //
// *** Delete from database ************ //
// ************************************* //

$btn_delete.click(function() {

    $.each($("input[name='checkbox[]']:checked"), function() {

        checkbox_checked.push($(this).val());
    });

    $.ajax({
        url: "includes/database_requests.php" 
                                            + "?btn_delete=Delete"
                                            + "&delete_id=" + checkbox_checked,
        dataType: "text",
        method: "GET",
        success: function ($data) {

            print_database();
        },
        error: function(jqXHR) {

            $list_database.empty();
            $list_errors.append("<p>Sorry, there was a problem with the server.</p>");
            $list_errors.append("<p>Error: " + jqXHR.statusText + " (" + jqXHR.status + ")</p>");
        },
        timeout: 5000
    });
})


// ************************************* //
// *** Update data from database ******* //
// ************************************* //

$list_database.on("click", ".btn_update", function() {

    data_id = this.id;

    $.ajax({
        url: "includes/database_requests.php?update=" + data_id,
        dataType: "text",
        method: "GET",
        success: function ($data) {

	    // Reset input values
            $id_update.val("");
            $first_name_update.val("");
	    $last_name_update.val("");
            $email_update.val("");
	    $phone_number_update.val("");

	    // Insert new inputs' value to be updated
            data_contact = $data.split("|");
            $id_update.val(data_contact[0]);
            $first_name_update.val(data_contact[1]);
            $last_name_update.val(data_contact[2]);
            $email_update.val(data_contact[3]);
            $phone_number_update.val(data_contact[4]);

	    $btn_confirm_update.attr("disabled", false);

        },
        error: function(jqXHR) {

            $list_database.empty();
            $list_errors.append("<p>Sorry, there was a problem with the server.</p>");
            $list_errors.append("<p>Error: " + jqXHR.statusText + " (" + jqXHR.status + ")</p>");
        },
        timeout: 5000
    });
})

// ************************************* //
// *** Confirm updates ***************** //
// ************************************* //

$btn_confirm_update.click(function(){

    $.ajax({
        url: "includes/database_requests.php",
        dataType: "text",
        method: "POST",
        data: {
            "action": "confirm_update",
            "new_id": $id_update.val(),
            "new_name": $first_name_update.val(),
            "new_last_name": $last_name_update.val(),
            "new_email": $email_update.val(),
            "new_phone_number": $phone_number_update.val()
        },
        success: function ($data) {

            if ($data.length > 0) {

                $list_errors_update.empty();
                $list_errors_update.append($data);
		$update_error_footer.removeClass("d-none");
            } else {

                $id_update.val("");
                $first_name_update.val("");
                $last_name_update.val("");
                $email_update.val("");
                $phone_number_update.val("");
                $btn_confirm_update.attr("disabled", true);
                $list_database.empty();
                print_database();
		$update_error_footer.addClass("d-none");
 		$input_update_success.show("fast");
		setTimeout(function() {

			$("#update-contact").modal("hide");
			$input_update_success.hide();
		}, 1500);
            }
        },
        error: function(jqXHR) {

            $list_database.empty();
            $list_errors.append("<p>Sorry, there was a problem with the server.</p>");
            $list_errors.append("<p>Error: " + jqXHR.statusText + " (" + jqXHR.status + ")</p>");
        },
        timeout: 5000
    });
})
