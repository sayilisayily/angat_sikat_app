<?php
// Database connection
require 'connection.php';

// Check if 'purchase_id' is passed in the URL
if (isset($_GET['purchase_id']) && !empty($_GET['purchase_id'])) {
    $purchase_id = intval($_GET['purchase_id']); // Get and sanitize the purchase_id from the URL

    // Prepare SQL query to fetch purchase details
    $stmt = $conn->prepare("SELECT * FROM purchases WHERE purchase_id = ?");
    
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("i", $purchase_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if purchase exists
    if ($result->num_rows > 0) {
        $purchase = $result->fetch_assoc();
    } else {
        echo "No purchase found.";
        exit;
    }

    // Fetch the items associated with the purchase
    $itemStmt = $conn->prepare("SELECT * FROM purchase_items WHERE purchase_id = ?");
    
    if ($itemStmt === false) {
        die('Prepare for items failed: ' . $conn->error);
    }

    $itemStmt->bind_param("i", $purchase_id);
    $itemStmt->execute();
    $itemsResult = $itemStmt->get_result();

    // Store items in an array
    $items = [];
    if ($itemsResult->num_rows > 0) {
        while ($row = $itemsResult->fetch_assoc()) {
            $items[] = $row;
        }
    }

    // Close the statements
    $stmt->close();
    $itemStmt->close();
} else {
    echo "No purchase ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'head.php'; ?>
        <title> Purchases Table </title>
    </head>
<body>
<body>
    <div class="container mt-5 p-4">
        <h2>Financial Plan</h2>

        <h4>Title: <?php echo $purchase['title']; ?></h4>

        <h4>Items<button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addItemModal"><i class="fa-solid fa-plus"></i> Add Item</button></h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Amount</th>
                    <th>Total Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($items)) {
                    foreach ($items as $item) {
                        $total_amount = $item['quantity'] * $item['amount'];
                        echo "<tr>
                                <td>{$item['description']}</td>
                                <td>{$item['quantity']}</td>
                                <td>{$item['unit']}</td>
                                <td>{$item['amount']}</td>
                                <td>{$total_amount}</td>
                                <td>
                                    <button class='btn btn-primary btn-sm' 
                                        data-bs-toggle='modal' 
                                        data-bs-target='#editItemModal' 
                                        data-id='{$item['item_id']}'
                                        data-description='{$item['description']}'
                                        data-quantity='{$item['quantity']}'
                                        data-unit='{$item['unit']}'
                                        data-amount='{$item['amount']}'
                                    ><i class='fa-solid fa-pen'></i> Edit</button>
                                    <button class='btn btn-danger btn-sm' 
                                        data-bs-toggle='modal' 
                                        data-bs-target='#deleteItemModal' 
                                        data-id='{$item['item_id']}'
                                    ><i class='fa-solid fa-trash'></i> Delete</button>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No items found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Add Item Modal -->
        <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="addItemForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addItemModalLabel">Add Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                        
                            <!-- Modal content for adding item -->
                            <input type="hidden" name="purchase_id" value="<?php echo $purchase_id; ?>"> <!-- purchase ID -->

                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="text" class="form-control" id="description" name="description" required>
                            </div>

                            <div class="form-group">
                                <label for="quantity">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" required>
                            </div>

                            <div class="form-group">
                                <label for="unit">Unit</label>
                                <input type="text" class="form-control" id="unit" name="unit" required>
                            </div>

                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                            </div>

                            <!-- Success Message Alert -->
                            <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                                    purchase added successfully!
                            </div>  
                            <!-- Error Message Alert -->
                            <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                                <ul id="errorList"></ul> <!-- List for showing validation errors -->
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Add Item</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Item Modal -->
        <div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editItemForm">
                <div class="modal-body">
                <input type="hidden" id="edit_item_id" name="item_id">
                <input type="hidden" id="edit_event_id" name="purchase_id" value="<?php echo $purchase_id; ?>"> <!-- Add purchase_id -->
                <div class="mb-3">
                    <label for="edit_description" class="form-label">Description</label>
                    <input type="text" class="form-control" id="edit_description" name="description" required>
                </div>
                <div class="mb-3">
                    <label for="edit_quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="edit_quantity" name="quantity" required>
                </div>
                <div class="mb-3">
                    <label for="edit_unit" class="form-label">Unit</label>
                    <input type="text" class="form-control" id="edit_unit" name="unit" required>
                </div>
                <div class="mb-3">
                    <label for="edit_amount" class="form-label">Amount</label>
                    <input type="number" step="0.01" class="form-control" id="edit_amount" name="amount" required>
                </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
            </div>
        </div>
        </div>


        <!-- Delete Item Modal -->
        <div class="modal fade" id="deleteItemModal" tabindex="-1" aria-labelledby="deleteItemModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteItemModalLabel">Delete Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this item?</p>
                        <form id="deleteItemForm" action="delete_item.php" method="POST">
                            <input type="hidden" name="item_id" id="delete_item_id">
                            <input type="hidden" id="delete_event_id" name="purchase_id">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-1" onclick="history.back()"> Cancel </button>
            <button type="button" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save </button>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery (needed to handle modals) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $('#addItemForm').on('submit', function(purchase) {
            purchase.preventDefault(); // Prevent the form from submitting in the traditional way

            $.ajax({
                url: 'add_purchase_item.php', // The PHP file that processes adding the item
                type: 'POST',
                data: $(this).serialize(), // Serialize the form data
                success: function(response) {
                    try {
                        // Parse the JSON response
                        response = JSON.parse(response);

                        if (response.success) {
                            // Hide any error messages
                            $('#errorMessage').addClass('d-none');

                            // Show success message
                            $('#successMessage').removeClass('d-none');

                            // Close the modal after a short delay
                            setTimeout(function() {
                                $('#addItemModal').modal('hide');

                                // Reset the form and hide the success message
                                $('#addItemForm')[0].reset();
                                $('#successMessage').addClass('d-none');

                                // Reload the page to reflect the new item
                                location.reload();
                            }, 2000); // Adjust the delay as needed
                        } else {
                            // Hide any success messages
                            $('#successMessage').addClass('d-none');

                            // Show error messages
                            $('#errorMessage').removeClass('d-none');
                            let errorHtml = '';
                            for (let field in response.errors) {
                                errorHtml += `<li>${response.errors[field]}</li>`;
                            }
                            $('#errorList').html(errorHtml);
                        }
                    } catch (error) {
                        console.error('Error parsing JSON:', error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error adding item:', error);
                }
            });
        });

        $('.edit-btn').on('click', function() {
            var itemId = $(this).data('id'); // Get item_id from the button
            console.log("Selected Item ID:", itemId); // Log the item ID for debugging

            // Send an AJAX request to fetch the item details using the item ID
            $.ajax({
                url: 'get_purchase_item_details.php', // PHP file to fetch item data
                type: 'POST',
                data: { item_id: itemId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Populate the form with item data
                        $('#edit_item_id').val(response.data.item_id); // Hidden field for item ID
                        $('#edit_description').val(response.data.description);
                        $('#edit_quantity').val(response.data.quantity);
                        $('#edit_unit').val(response.data.unit);
                        $('#edit_amount').val(response.data.amount);

                        // Show the modal
                        $('#editItemModal').modal('show');
                    } else {
                        console.log("Error fetching data: ", response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", error);
                }
            });
        });

        $('#editItemForm').on('submit', function(purchase) {
            purchase.preventDefault(); // Prevent the form from submitting the default way

            $.ajax({
                url: 'edit_item.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    try {
                        // Parse the JSON response
                        response = JSON.parse(response);

                        if (response.success) {
                            // Show success message and hide any error messages
                            $('#errorMessage').addClass('d-none');
                            $('#successMessage').removeClass('d-none');

                            // Close the modal after a short delay
                            setTimeout(function() {
                                $('#editItemModal').modal('hide');

                                // Reset the form and hide the success message
                                $('#editItemForm')[0].reset();
                                $('#successMessage').addClass('d-none');

                                // Reload the page to reflect the updated item
                                location.reload();
                            }, 2000); // Delay (2 seconds)
                        } else {
                            // Hide the success message and show the error message
                            $('#successMessage').addClass('d-none');
                            $('#errorMessage').removeClass('d-none');
                            let errorHtml = '';
                            for (let field in response.errors) {
                                errorHtml += `<li>${response.errors[field]}</li>`;
                            }
                            $('#errorList').html(errorHtml);
                        }
                    } catch (error) {
                        console.error('Error parsing JSON:', error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error editing item:', error);
                }
            });
        });



        // Pass data to Delete Modal
        $('#deleteItemModal').on('show.bs.modal', function (purchase) {
            var button = $(purchase.relatedTarget); // Button that triggered the modal
            var itemId = button.data('id');
            var eventId = button.data('purchase-id'); // Get the purchase_id

            // Update the modal's field
            var modal = $(this);
            modal.find('#delete_item_id').val(itemId);
            modal.find('#delete_event_id').val(eventId); // Pass purchase_id to the form if needed
        });

    </script>
</body>
</html>
