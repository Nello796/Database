<?php

/***************************************
 ******* Print and sort database *******
 ***************************************/
function print_and_sort_database($database, $database_length) {


    // Variables
    $database;
    $database_length;
    $sort_name = "A-Z";
    $sort_last_name = "A-Z";
    $sort_type = 0; // [1] = Increasing / [2] = Decreasing
    $column_to_sort = 0; // [1] = Name / [2] = Last name
    $checkbox_val = 1;
    $key_database_value = NULL;
    $sort_key_val = array();
    $results = array();


    // Transform each key in an array
    for($a = 1; $a < $database_length; $a++) {

        $database[$a] = explode("|", $database[$a]);
    }


    // Start if the sort button is pressed 
    if(array_key_exists("sort", $_GET)) {

        // Get the value of sort
        $sort_val = $_GET["sort"];
        
        // Check which the value is and apply the script 
        switch($sort_val) {

            case "name-A-Z":
                $sort_name = "Z-A";
                $column_to_sort = 1;
                $sort_type = 1;
                break;

            case "name-Z-A":
                $sort_name = "A-Z";
                $column_to_sort = 1;
                $sort_type = 2;
                break;

            case "last_name-A-Z":
                $sort_last_name = "Z-A";
                $column_to_sort = 2;
                $sort_type = 1;
                break;

            case "last_name-Z-A":
                $sort_last_name = "A-Z";
                $column_to_sort = 2;
                $sort_type = 2;
                break;

            default:
                echo "Error";
        }

        // ----- Create a new array wiht keys = the value that we want to sort and values = contact from the database ----- //  
        // Create a new array and safe all the key's value from the database
        for($b = 0; $b < $database_length; $b++) {

            $key_database_value = $database[$b][$column_to_sort];
            $key_database_value =strtolower($key_database_value);
            array_push($sort_key_val, $key_database_value);
        }

        // Save the contact corresponding to the key value
        // Example: if key is = 'Fabion', the value will be all fabion's datails from tadabase
        foreach($sort_key_val as $key => $val) {

            if ($key == 0) continue;
            $results[$val] = $database[$key];
        }
        // ------------------------------------------------------------------ //

        // Sort from A-Z if it's 1 otherwise from Z-A if it's 2
        if($sort_type == 1) {

            ksort($results);
        } elseif($sort_type == 2) {

            krsort($results);
        }

        echo "	<tr>
            <td><input type='submit' name='delete' value='Delete' class='btn_delete'></td>
            <td><a href='?sort=name-" . $sort_name . "'>" . $sort_name . "</a></td>
            <td><a href='?sort=last_name-" . $sort_last_name . "'>" . $sort_last_name . "</a></td>
        </tr>";

        // Count untill in reach the last row of the document
        foreach($results as $results_key => $results_value) {

            echo "<tr>";
            echo "<td><input type='checkbox' name='checkbox[]' value='" . $checkbox_val . "' class='checkbox'></td>";
            // Print the row starting from the name
            for($c = 1; $c <= 4; $c++) {
                        
                echo "<td>" . $results_value[$c] . "</td>";
            }
                    
            echo "<td><a href='?id=" . $results_value[0] . "' class='btn_update'>Update</a></td>";
            echo "</tr>";
        }
            
    } else {

        echo "	<tr>
            <td><input type='submit' name='delete' value='Delete' class='btn_delete'></td>
            <td><a href='?sort=name-" . $sort_name . "'>" . $sort_name . "</a></td>
            <td><a href='?sort=last_name-" . $sort_last_name . "'>" . $sort_last_name . "</a></td>
        </tr>";
        
        // Count untill in reach the last row of the document
        foreach($database as $database_key => $database_value) {
                
            // Skip the first row in the database
            if($database_key == 0) continue;	
            // Print each row the datas
            echo "<tr>";
            echo "<td><input type='checkbox' name='checkbox[]' value='" . $checkbox_val . "' class='checkbox'></td>";
            $checkbox_val++;
            // Print the row countin from the name
            for($d = 1; $d <= 4; $d++) {
            
                echo "<td>" . $database_value[$d] . "</td>";
            }

            echo "<td><a href='?id=" . $database_value[0] . "' class='btn_update'>Update</a></td>";
            echo "</tr>";
        }
    }
}

?>
