$(document).ready(function() {
    $('#organizationsTable').DataTable({
        "paging": true,
        "searching": true,
        "info": true,
        "lengthChange": true,
        "pageLength": 10,
        "ordering": true,
        "order": [],
    });
});

// Handle Add Organization Form Submission
$('#addOrganizationForm').on('submit', function(event) {
    event.preventDefault(); 

    $.ajax({
        url: 'add_organization.php', 
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            try {
                response = JSON.stringify(response);
                console.log(response);

                if (response.success) {
                    $('#errorMessage').addClass('d-none');
                    $('#successMessage').removeClass('d-none');

                    setTimeout(function() {
                        $('#addOrganizationModal').modal('hide');
                        $('#addOrganizationForm')[0].reset();
                        $('#successMessage').addClass('d-none');
                        location.reload();
                    }, 2000);
                } else {
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
            console.error('Error adding organization:', error);
        }
    });
});

// Edit Organization Button Click
$('.edit-btn').on('click', function() {
    var organizationId = $(this).data('id');
    console.log("Selected Organization ID:", organizationId);

    $.ajax({
        url: 'get_organization_details.php', 
        type: 'POST',
        data: { organization_id: organizationId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#editOrganizationId').val(response.data.organization_id);
                $('#editOrganizationName').val(response.data.organization_name);
                $('#editOrganizationLogo').val(response.data.organization_logo);
                $('#editOrganizationMembers').val(response.data.organization_members);
                $('#editOrganizationStatus').val(response.data.organization_status);
                
                $('#editOrganizationModal').modal('show');
            } else {
                console.log("Error fetching data: ", response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", error);
        }
    });
});

// Handle Edit Organization Form Submission
$('#editOrganizationForm').on('submit', function(event) {
    event.preventDefault(); 

    $.ajax({
        url: 'update_organization.php', 
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            try {
                response = JSON.parse(response);
                console.log(response);

                if (response.success) {
                    $('#errorMessage').addClass('d-none');
                    $('#successMessage').removeClass('d-none');

                    setTimeout(function() {
                        $('#editOrganizationModal').modal('hide');
                        $('#editOrganizationForm')[0].reset();
                        $('#successMessage').addClass('d-none');
                        location.reload();
                    }, 2000);
                } else {
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
            console.error('Error updating organization:', error);
            console.log(xhr.responseText);
        }
    });
});

// Event delegation for dynamically loaded buttons (Archive)
$(document).on('click', '.archive-btn', function() {
    var organizationId = $(this).data('id');
    $('#archiveOrganizationId').val(organizationId);
    $('#archiveModal').modal('show');
    console.log('Selected Organization ID: ' + organizationId);
});

// Handle archive confirmation when "Archive" button in modal is clicked
$('#confirmArchiveBtn').on('click', function() {
    var organizationId = $('#archiveOrganizationId').val();
    
    $.ajax({
        url: 'archive_organization.php', 
        type: 'POST',
        data: { organization_id: organizationId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                console.log(response.message);
                location.reload();
            } else {
                alert("Error archiving organization: " + response.message);
            }
            $('#archiveModal').modal('hide');
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", error);
        }
    });
});
