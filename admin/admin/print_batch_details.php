<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['batch_id'])) {
    $batchId = intval($_POST['batch_id']);

    // Fetch batch, supervisor, and members details
    $batchQuery = "
        SELECT b.batch_name, b.batch_title, IFNULL(s.name, 'Not Allocated') AS supervisor_name
        FROM batches b
        LEFT JOIN supervisors s ON b.supervisor = s.username
        WHERE b.batch_id = $batchId
    ";
    $batchResult = $conn->query($batchQuery);
    $batch = $batchResult->fetch_assoc();

    $membersQuery = "SELECT rollno, name AS student_name, email, mobile FROM students WHERE batch_id = $batchId";
    $membersResult = $conn->query($membersQuery);

    // Generate HTML content for the PDF
    ob_start();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Batch Details</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                color: #333;
                background-color: #f9f9f9;
            }
            h1, h2 {
                color: #0056b3;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            table, th, td {
                border: 1px solid #ddd;
            }
            th, td {
                padding: 10px;
                text-align: left;
            }
            th {
                background-color: #007bff;
                color: #fff;
            }
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            tr:hover {
                background-color: #e2e2e2;
            }
            .logo {
                text-align: center;
                margin-bottom: 20px;
            }
            .logo img {
                max-width: 150px;
                height: auto;
            }
            .bold {
                font-weight: bold;
            }
        </style>
        <script>
            window.onload = function() {
                window.print();
            };
        </script>
    </head>
    <body>
        <h1>Batch Details</h1>
        <p><strong>Batch Name:</strong> <?php echo htmlspecialchars($batch['batch_name']); ?></p>
        <p><strong>Batch Title:</strong> <?php echo htmlspecialchars($batch['batch_title']); ?></p>
        <p><strong>Supervisor:</strong> <?php echo htmlspecialchars($batch['supervisor_name']); ?></p>

        <h2>Members</h2>
        <table>
            <thead>
                <tr>
                    <th>Roll Number</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($member = $membersResult->fetch_assoc()): ?>
                <tr>
                    <td class="bold"><?php echo htmlspecialchars($member['rollno']); ?></td>
                    <td class="bold"><?php echo htmlspecialchars($member['student_name']); ?></td>
                    <td class="bold"><?php echo htmlspecialchars($member['email']); ?></td>
                    <td class="bold"><?php echo htmlspecialchars($member['mobile']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    $content = ob_get_clean();
    echo $content;
} else {
    echo 'Invalid request.';
}
?>
