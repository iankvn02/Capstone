$(document).ready(function () {
    // Handle Grades/Cert. OSY form submission
    $("#formGradesCert").submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "your_upload_grades_cert_php_script.php", // Replace with the actual PHP script URL
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                // Handle success, e.g., display a success message
                alert("Grades/Cert. OSY uploaded successfully.");
            },
            error: function () {
                // Handle errors, e.g., display an error message
                alert("Error uploading Grades/Cert. OSY.");
            }
        });
    });

    // Handle ITR/Cert. Indigency form submission
    $("#formITRCert").submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "your_upload_itr_cert_php_script.php", // Replace with the actual PHP script URL
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                // Handle success, e.g., display a success message
                alert("ITR/Cert. Indigency uploaded successfully.");
            },
            error: function () {
                // Handle errors, e.g., display an error message
                alert("Error uploading ITR/Cert. Indigency.");
            }
        });
    });

    // Handle Additional Document form submission
    $("#formAdditionalDocument").submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "your_upload_additional_document_php_script.php", // Replace with the actual PHP script URL
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                // Handle success, e.g., display a success message
                alert("Additional Document uploaded successfully.");
            },
            error: function () {
                // Handle errors, e.g., display an error message
                alert("Error uploading Additional Document.");
            }
        });
    });
});