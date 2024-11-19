<?php
require_once 'models.php';

// Initialize variables
$searchQuery = '';
$response = ['statusCode' => 200, 'querySet' => []]; // Default response structure

// Handle search functionality
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchQuery = trim($_GET['search']);
    $response = searchApplicants($searchQuery);
} else {
    $response = readApplicants();
}

// Retrieve applicants or set to empty array if response fails
$applicants = $response['querySet'] ?? [];

// Handle delete action
if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    deleteApplicant($_POST['id']);
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Job Application System</h1>
    </header>

    <main>
        <!-- Action bar for adding applicants and search -->
        <div class="action-bar">
            <a href="templates/create.php" class="button">+ Add Applicant</a>
            <form method="get" action="index.php" class="search-form">
                <input type="text" name="search" placeholder="Search applicants..." value="<?= htmlspecialchars($searchQuery) ?>">
                <button type="submit" class="button">Search</button>
            </form>
        </div>

        <!-- Display messages or results -->
        <?php if ($response['statusCode'] !== 200): ?>
            <p class="error">Error: <?= htmlspecialchars($response['message'] ?? 'Unknown error') ?></p>
        <?php elseif (empty($applicants)): ?>
            <p class="no-applicants">No applicants found.</p>
        <?php else: ?>
            <!-- Applicants table -->
            <table class="minimal-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Qualifications</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applicants as $applicant): ?>
                        <tr>
                            <td><?= htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']) ?></td>
                            <td><?= htmlspecialchars($applicant['email']) ?></td>
                            <td><?= htmlspecialchars($applicant['phone']) ?></td>
                            <td><?= htmlspecialchars($applicant['address']) ?></td>
                            <td><?= htmlspecialchars($applicant['qualifications']) ?></td>
                            <td>
                                <a href="templates/edit.php?id=<?= $applicant['id'] ?>" class="button small">Edit</a>
                                <form method="post" action="index.php" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($applicant['id']) ?>">
                                    <button type="submit" class="button small danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>
