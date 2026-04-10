<?php


$page_title = "Contact Us";
include_once '../config/db.php';
include_once '../includes/functions.php';

$create_contact_table = "CREATE TABLE IF NOT EXISTS `contact_messages` (
    `message_id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `subject` VARCHAR(200),
    `message` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
$conn->query($create_contact_table);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "All fields are required";
    } elseif (!validateEmail($email)) {
        $error = "Please enter a valid email address";
    } else {
        $insert_query = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        
        if ($stmt->execute()) {
            $success = "Thank you for contacting us! We will get back to you soon.";
        } else {
            $error = "Failed to send message. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - ECommerce Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/E-comm/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/navbar.php'; ?>

    <div class="container py-5">
        <h1 class="mb-4 text-center"><i class="fas fa-envelope"></i> Contact Us</h1>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-map-marker-alt fa-2x text-primary mb-3"></i>
                        <h5>Address</h5>
                        <p class="text-muted">102 Savar<br>Daffodil University,<br>Bangladesh</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-phone fa-2x text-primary mb-3"></i>
                        <h5>Phone</h5>
                        <p class="text-muted">
                            <strong>Sales:</strong> 01616793091<br>
                            <strong>Support:</strong> 01820028152<br>
                            <strong>Hours:</strong> 12/5
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-envelope fa-2x text-primary mb-3"></i>
                        <h5>Email</h5>
                        <p class="text-muted">
                            <strong>General:</strong> rayhan@ecommerce.com<br>
                            <strong>Support:</strong> support@ecommerce.com<br>
                            <strong>Sales:</strong> sales@ecommerce.com
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Message us</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo isset($name) ? $name : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Your Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($email) ? $email : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject *</label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       value="<?php echo isset($subject) ? $subject : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message *</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required><?php echo isset($message) ? $message : ''; ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


