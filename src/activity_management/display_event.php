<?php                
require 'connection.php'; // Include your database connection file
include '../session_check.php';

// Query to fetch event details along with the organization's color
$display_query = "
    SELECT e.event_id, e.title, e.event_start_date, e.event_end_date, o.organization_color
    FROM events e
    JOIN organizations o ON e.organization_id = o.organization_id  -- Assuming organization_id is the link
    WHERE e.archived = 0 AND e.event_status = 'Approved'";             

$results = mysqli_query($conn, $display_query); // Execute the query
$count = mysqli_num_rows($results);  // Count the number of results

// Check if events exist
if($count > 0) 
{
    $data_arr = array();  // Initialize an array to hold the events
    $i = 0;  // Initialize an index

    // Loop through the result set and fetch each row
    while($data_row = mysqli_fetch_array($results, MYSQLI_ASSOC)) 
    {   
        $data_arr[$i]['event_id'] = $data_row['event_id'];  // Event ID
        $data_arr[$i]['title'] = $data_row['title'];  // Event title
        $data_arr[$i]['start'] = date("Y-m-d", strtotime($data_row['event_start_date']));  // Start date (formatted)
        $data_arr[$i]['end'] = date("Y-m-d", strtotime($data_row['event_end_date']));  // End date (formatted)
        
        // Assign the organization color
        $data_arr[$i]['color'] = $data_row['organization_color'];  // Use the organization color

        // Optionally, include a URL to link to event details
        $data_arr[$i]['url'] = 'event_details.php?event_id=' . $data_row['event_id'];  

        $i++;  // Increment index
    }

    // Prepare success response
    $data = array(
        'status' => true,
        'msg' => 'Events fetched successfully!',
        'data' => $data_arr
    );
} 
else 
{
    // Prepare failure response if no events were found
    $data = array(
        'status' => false,
        'msg' => 'No events found!'
    );
}

// Output the result as JSON
echo json_encode($data);
?>
