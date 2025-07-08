<?php
include('auth.php');
$title = "Admin | Marks Analytics";
include('../head.php');
include('../db.php');

$username = $conn->real_escape_string($_SESSION['user']);
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];

// Fetch Evaluation Types
$evaluationTypes = [];
$sql = "SELECT * FROM evaluation_type";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($rownew = $result->fetch_assoc()) {
        $evaluationTypes[$rownew['id']] = $rownew['name'];
    }
}
?>
<style>
    @media screen {
  #printSection {
      display: none;
  }
}

@media print {
  body * {
    visibility:hidden;
  }
  #printSection, #printSection * {
    visibility:visible;
  }
  #printSection {
    position:absolute;
    left:0;
    top:0;
  }
}



</style>
<body>
<div id="layout-wrapper">
    <?php include('header.php'); ?>
    <?php include('navbar.php'); ?>
    <div class="vertical-overlay"></div>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                            <h4 class="mb-sm-0">Marks Analytics</h4>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item active">Marks Analytics</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Choose Filter Options:</h5>
                            </div>
                            <div class="card-body">
                                <form id="marks-filter-form">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="filter-type" class="form-label">View By:</label>
                                            <select class="form-select" id="filter-type">
                                                <option value="student">Student Wise</option>
                                                <option value="evaluator">Evaluator Wise</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4" id="evaluation-type-container">
                                            <label for="evaluation-type" class="form-label">Evaluation Type:</label>
                                            <select class="form-select" id="evaluation-type">
                                                <option value="">Select Evaluation Type</option>
                                                <?php foreach ($evaluationTypes as $id => $name): ?>
                                                    <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="search" class="form-label">Search:</label>
                                            <input type="text" class="form-control" id="search" placeholder="Enter roll no, name, etc.">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">Apply Filters</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="marks-table-container" style="display: none;">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Marks Data</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-nowrap align-middle mb-0" id="marks-data-table">
                                        <thead></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center mt-3" id="pagination-container">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="printThis">
    <!-- Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Evaluation Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body-content">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnPrint">Print Details</button>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>

document.getElementById("btnPrint").onclick = function () {
    printElement(document.getElementById("detailsModal"));
};

function printElement(elem) {
    var domClone = elem.cloneNode(true);

    // Ensure the print section exists
    var printSection = document.getElementById("printSection");
    if (!printSection) {
        printSection = document.createElement("div");
        printSection.id = "printSection";
        document.body.appendChild(printSection);
    }

    // Clear previous content and append new content
    printSection.innerHTML = "";
    printSection.appendChild(domClone);

    // Add styles for printing
    const style = document.createElement("style");
    style.textContent = `
        #printSection {
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            background: white;
            color: black;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            #printSection, #printSection * {
                visibility: visible;
            }
            #printSection {
                position: absolute;
                top: 0;
                left: 0;
            }
        }
    `;
    document.head.appendChild(style);

    // Trigger print
    window.print();

    // Cleanup: Remove the print section and style after printing
    document.body.removeChild(printSection);
    document.head.removeChild(style);
}

    $(document).ready(function () {
        var filterType = 'student';
        var currentPage = 1;
        var rowsPerPage = 10;
        var selectedEvaluationType = '';
        var searchTerm = '';

        $('#marks-filter-form').submit(function (event) {
            event.preventDefault();
            currentPage = 1;
            filterType = $('#filter-type').val();
            selectedEvaluationType = $('#evaluation-type').val();
            searchTerm = $('#search').val();
            loadMarksData();
        });

        $('#filter-type').change(function() {
            filterType = $(this).val();
            if (filterType === 'student') {
                $('#evaluation-type-container').show();
            } else {
                $('#evaluation-type-container').hide();
            }
        });

        function loadMarksData() {
            $.ajax({
                url: 'get_marks_data.php',
                type: 'POST',
                data: {
                    filterType: filterType,
                    evaluationType: selectedEvaluationType,
                    searchTerm: searchTerm,
                    page: currentPage,
                    rowsPerPage: rowsPerPage
                },
                dataType: 'json',
                success: function (response) {
                    console.log('Server response:', response);
                    if (response.status === 'success') {
                        populateMarksTable(response.data);
                        currentPage = response.currentPage;
                        initPagination(response.totalPages);
                        $('#marks-table-container').show();
                    } else {
                        console.error('Error loading data:', response.message);
                        alert('Error loading data: ' + response.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    console.log('Server response:', jqXHR.responseText);
                    alert('An error occurred while fetching data. Please check the console for more details.');
                }
            });
        }

        function populateMarksTable(data) {
            var tableHead = $('#marks-data-table thead');
            var tableBody = $('#marks-data-table tbody');
            tableHead.empty();
            tableBody.empty();

            // Dynamically generate table header
            var headerRow = $('<tr>');
            if (filterType === 'student') {
                headerRow.append('<th>Roll No</th>');
                headerRow.append('<th>Student Name</th>');
                headerRow.append('<th>Batch ID</th>'); // Add this line
                headerRow.append('<th>Evaluation Type</th>');
                headerRow.append('<th>MRG Evaluators (Avg)</th>');
                headerRow.append('<th>DPEG Evaluators (Avg)</th>');
                headerRow.append('<th>Supervisor Marks</th>');
                headerRow.append('<th>Details</th>');
            } else { // Evaluator wise
                headerRow.append('<th>Evaluator Name</th>');
                headerRow.append('<th>Evaluation Type</th>');
                headerRow.append('<th>Average Marks</th>');
                headerRow.append('<th>Total Students Evaluated</th>');
                headerRow.append('<th>Batch IDs</th>'); // Add this line
                headerRow.append('<th>Details</th>');
            }
            tableHead.append(headerRow);

            // Populate table rows
            $.each(data, function (index, row) {
                var newRow = $('<tr>');
                if (filterType === 'student') {
                    newRow.append('<td>' + row.rollno + '</td>');
                    newRow.append('<td>' + row.name + '</td>');
                    newRow.append('<td>' + row.batch_id + '</td>'); // Add this line
                    newRow.append('<td>' + row.evaluation_type_name + '</td>');
                    newRow.append('<td>' + (row.avg_mrg_marks ? row.avg_mrg_marks : '-') + '</td>');
                    newRow.append('<td>' + (row.avg_dpeg_marks ? row.avg_dpeg_marks : '-') + '</td>');
                    newRow.append('<td>' + (row.supervisor_marks || '-') + '</td>');
                    newRow.append('<td><button type="button" class="btn btn-primary btn-sm details-btn" data-rollno="' + row.rollno + '" data-evaluation-type="' + row.evaluation_type_id + '">Details</button></td>');
                } else { // Evaluator wise
                    newRow.append('<td>' + row.evaluator_name + '</td>');
                    newRow.append('<td>' + row.evaluation_type + '</td>');
                    newRow.append('<td>' + row.avg_marks + '</td>');
                    newRow.append('<td>' + row.total_students + '</td>');
                    newRow.append('<td>' + row.batch_ids + '</td>'); // Add this line
                    newRow.append('<td><button type="button" class="btn btn-primary btn-sm details-btn" data-evaluator-id="' + row.evaluator_id + '" data-evaluation-type="' + row.evaluation_type_id + '">Details</button></td>'); 
                }
                tableBody.append(newRow);
            });

            // Event listener for "Details" button
            $('.details-btn').click(function () {
                if (filterType === 'student') {
                    var rollno = $(this).data('rollno');
                    var evaluationType = $(this).data('evaluation-type');
                    getStudentEvaluationDetails(rollno, evaluationType);
                } else {
                    var evaluatorId = $(this).data('evaluator-id');
                    selectedEvaluationType = $(this).data('evaluation-type');
                    getEvaluatorEvaluations(evaluatorId, selectedEvaluationType);
                }
            });
        }

        function initPagination(totalPages) {
        $('#pagination-container').empty();
        if (totalPages > 1) {
            var paginationUl = $('<ul class="pagination">');
            for (var i = 1; i <= totalPages; i++) {
                var pageItem = $('<li class="page-item' + (i === currentPage ? ' active' : '') + '">');
                var pageLink = $('<a class="page-link" href="#">').text(i);
                pageLink.data('page', i);
                pageItem.append(pageLink);
                paginationUl.append(pageItem);
            }
            $('#pagination-container').append(paginationUl);

            $('.page-link').click(function (event) {
                event.preventDefault();
                currentPage = $(this).data('page');
                loadMarksData();
            });
        }
    }

        function getStudentEvaluationDetails(rollno, evaluationType) {
            $.ajax({
                url: 'get_student_evaluation_details.php',
                type: 'POST',
                data: {
                    rollno: rollno,
                    evaluationType: evaluationType
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        displayStudentEvaluationDetails(response.data);
                    } else {
                        alert('Error loading student details: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while fetching student details.');
                }
            });
        }

        function displayStudentEvaluationDetails(data) {
            var modalBody = $('#modal-body-content');
            modalBody.empty();

            if (data) {
                // Student Details
                if (data.studentDetails) {
                    modalBody.append('<h4 class="mt-4 mb-3">Student Details</h4>');
                    var studentDetailsTable = $('<table class="table table-striped table-hover table-bordered">');
                    studentDetailsTable.append('<thead class="table-light"><tr><th>Roll No</th><th>Name</th><th>Batch ID</th></tr></thead>');
                    var studentDetailsTableBody = $('<tbody>');
                    var studentRow = $('<tr>');
                    studentRow.append('<td>' + data.studentDetails.rollno + '</td>');
                    studentRow.append('<td>' + data.studentDetails.name + '</td>');
                    studentRow.append('<td>' + data.studentDetails.batch_id + '</td>');
                    studentDetailsTableBody.append(studentRow);
                    studentDetailsTable.append(studentDetailsTableBody);
                    modalBody.append(studentDetailsTable);
                }

                // MRG Evaluations
                if (data.mrgEvaluations && data.mrgEvaluations.length > 0) {
                    modalBody.append('<h4 class="mt-4 mb-3">MRG Evaluations</h4>');
                    var mrgTable = $('<table class="table table-striped table-hover table-bordered">');
                    mrgTable.append('<thead class="table-primary"><tr><th>Evaluator</th><th>Marks</th><th>Sub Parts</th><th>Remarks</th></tr></thead>');
                    var mrgTableBody = $('<tbody>');
                    $.each(data.mrgEvaluations, function (index, evaluation) {
                        var mrgRow = $('<tr>');
                        mrgRow.append('<td>' + evaluation.evaluator_name + '</td>');
                        mrgRow.append('<td>' + evaluation.marks + '</td>');
                        var subParts = JSON.parse(evaluation.sub_parts);
                        var subPartsHtml = '<ul class="list-unstyled mb-0">';
                        $.each(subParts, function (key, value) {
                            subPartsHtml += '<li>' + key + ': ' + value + '</li>';
                        });
                        subPartsHtml += '</ul>';
                        mrgRow.append('<td>' + subPartsHtml + '</td>');
                        mrgRow.append('<td>' + evaluation.remarks + '</td>');
                        mrgTableBody.append(mrgRow);
                    });
                    mrgTable.append(mrgTableBody);
                    modalBody.append(mrgTable);
                }

                // DPEG Evaluations
                if (data.dpegEvaluations && data.dpegEvaluations.length > 0) {
                    modalBody.append('<h4 class="mt-4 mb-3">DPEG Evaluations</h4>');
                    var dpegTable = $('<table class="table table-striped table-hover table-bordered">');
                    dpegTable.append('<thead class="table-success"><tr><th>Evaluator</th><th>Marks</th><th>Sub Parts</th><th>Remarks</th></thead>');
                    var dpegTableBody = $('<tbody>');
                    $.each(data.dpegEvaluations, function (index, evaluation) {
                        var dpegRow = $('<tr>');
                        dpegRow.append('<td>' + evaluation.evaluator_name + '</td>');
                        dpegRow.append('<td>' + evaluation.marks + '</td>');
                        var subParts = JSON.parse(evaluation.sub_parts);
                        var subPartsHtml = '<ul class="list-unstyled mb-0">';
                        $.each(subParts, function (key, value) {
                            subPartsHtml += '<li>' + key + ': ' + value + '</li>';
                        });
                        subPartsHtml += '</ul>';
                        dpegRow.append('<td>' + subPartsHtml + '</td>');
                        dpegRow.append('<td>' + evaluation.remarks + '</td>');
                        dpegTableBody.append(dpegRow);
                    });
                    dpegTable.append(dpegTableBody);
                    modalBody.append(dpegTable);
                }

                // Supervisor Evaluations
                if (data.supervisorEvaluations && data.supervisorEvaluations.length > 0) {
                    modalBody.append('<h4 class="mt-4 mb-3">Supervisor Evaluations</h4>');
                    var supervisorTable = $('<table class="table table-striped table-hover table-bordered">');
                    supervisorTable.append('<thead class="table-info"><tr><th>Supervisor</th><th>Marks</th><th>Sub Parts</th><th>Remarks</th></thead>');
                    var supervisorTableBody = $('<tbody>');
                    $.each(data.supervisorEvaluations, function (index, evaluation) {
                        var supervisorRow = $('<tr>');
                        supervisorRow.append('<td>' + evaluation.supervisor_name + '</td>');
                        supervisorRow.append('<td>' + evaluation.marks + '</td>');
                        var subParts = JSON.parse(evaluation.sub_parts);
                        var subPartsHtml = '<ul class="list-unstyled mb-0">';
                        $.each(subParts, function (key, value) {
                            subPartsHtml += '<li>' + key + ': ' + value + '</li>';
                        });
                        subPartsHtml += '</ul>';
                        supervisorRow.append('<td>' + subPartsHtml + '</td>');
                        supervisorRow.append('<td>' + evaluation.remarks + '</td>');
                        supervisorTableBody.append(supervisorRow);
                    });
                    supervisorTable.append(supervisorTableBody);
                    modalBody.append(supervisorTable);
                }

            } else {
                modalBody.append('<p class="alert alert-info">No evaluation data found.</p>');
            }
            $('#detailsModal').modal('show');
        }

        function getEvaluatorEvaluations(evaluatorId, evaluationTypeId, page = 1) {
            $.ajax({
                url: 'get_evaluator_evaluations.php',
                type: 'POST',
                data: {
                    evaluatorId: evaluatorId,
                    evaluationTypeId: evaluationTypeId,
                    page: page,
                    rowsPerPage: 10 // You can adjust this number as needed
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        displayEvaluatorEvaluations(response.data, evaluationTypeId, response.totalPages, page, evaluatorId);
                    } else {
                        alert('Error loading evaluator evaluations: ' + response.message);
                    }
                },
                error: function () {
                    alert('An error occurred while fetching evaluator evaluations.');
                }
            });
        }

        function displayEvaluatorEvaluations(data, evaluationTypeId, totalPages, currentPage, evaluatorId) {
            var modalBody = $('#modal-body-content');
            modalBody.empty();

            if (data.length > 0) {
                var evaluationTypeName = $('#evaluation-type option[value="' + evaluationTypeId + '"]').text();
                modalBody.append('<h4 class="mt-3 mb-3">' + evaluationTypeName + ' Evaluations</h4>');

                var table = $('<table class="table table-striped table-hover table-bordered">');
                table.append('<thead class="table-primary"><tr><th>Student Roll No</th><th>Student Name</th><th>Marks</th><th>Sub Parts</th><th>Remarks</th></tr></thead>');
                var tableBody = $('<tbody>');

                $.each(data, function (index, evaluation) {
                    var row = $('<tr>');
                    row.append('<td>' + evaluation.student_rollno + '</td>');
                    row.append('<td>' + evaluation.student_name + '</td>');
                    row.append('<td>' + evaluation.marks + '</td>');

                    var subParts = JSON.parse(evaluation.sub_parts);
                    var subPartsHtml = '<ul class="list-unstyled mb-0">';
                    $.each(subParts, function (key, value) {
                        subPartsHtml += '<li>' + key + ': ' + value + '</li>';
                    });
                    subPartsHtml += '</ul>';
                    row.append('<td>' + subPartsHtml + '</td>');

                    row.append('<td>' + evaluation.remarks + '</td>');
                    tableBody.append(row);
                });

                table.append(tableBody);
                modalBody.append(table);

                // Add pagination
                if (totalPages > 1) {
                    var paginationContainer = $('<div class="d-flex justify-content-center mt-3">');
                    var paginationUl = $('<ul class="pagination">');
                    for (var i = 1; i <= totalPages; i++) {
                        var pageItem = $('<li class="page-item' + (i === currentPage ? ' active' : '') + '">');
                        var pageLink = $('<a class="page-link" href="#">').text(i);
                        pageLink.click(function(e) {
                            e.preventDefault();
                            getEvaluatorEvaluations(evaluatorId, evaluationTypeId, $(this).text());
                        });
                        pageItem.append(pageLink);
                        paginationUl.append(pageItem);
                    }
                    paginationContainer.append(paginationUl);
                    modalBody.append(paginationContainer);
                }
            } else {
                modalBody.append('<p class="alert alert-info">No evaluations found for this evaluator.</p>');
            }

            $('#detailsModal').modal('show');
        }

        // Updated Print Functionality
        $('#print-btn').click(function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'pt', 'a4');
            
            const modalContent = document.getElementById('modal-body-content');
            
            // Remove pagination from the print view
            const paginationElements = modalContent.querySelectorAll('.pagination');
            paginationElements.forEach(el => el.style.display = 'none');
            
            html2canvas(modalContent, { scale: 1 }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const imgWidth = doc.internal.pageSize.getWidth();
                const imgHeight = canvas.height * imgWidth / canvas.width;
                let heightLeft = imgHeight;
                let position = 0;

                doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= doc.internal.pageSize.getHeight();

                while (heightLeft > 0) {
                    position = heightLeft - imgHeight;
                    doc.addPage();
                    doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= doc.internal.pageSize.getHeight();
                }

                doc.save('evaluation-details.pdf');
            });
        });

    });
</script>
<?php include('../footer.php'); ?>
</body>
</html>