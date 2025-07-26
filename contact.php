<?php include 'header.php'; ?>
<?php
session_start();

$name = $email = $message = '';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') {
        $errors[] = "Name is required.";
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }

    if ($message === '') {
        $errors[] = "Message cannot be empty.";
    }

    if (empty($errors)) {
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us | Smart Job Board</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        form { max-width: 600px; }
        input, textarea { width: 100%; padding: 8px; margin: 5px 0; }
        .error { color: red; }
        .success { color: green; }
        .intro { margin-bottom: 30px; }
    </style>
</head>
<body>

<h2>Contact Us</h2>

<div class="intro">
    <p>
        Welcome to our intelligent job board — a next-generation employment platform that integrates advanced
        <strong>AI tools</strong> and <strong>labor market analysis</strong> to deliver smarter matches between
        job seekers and employers. Whether you're a candidate looking to find the right role or an organization seeking top talent,
        our platform uses real-time data and predictive algorithms to help you make informed career and hiring decisions.
    </p>
    <p>
        We'd love to hear from you! Please use the form below to get in touch with questions, suggestions, or feedback.
    </p>
</div>

<?php if ($success): ?>
    <p class="success">Thank you! Your message has been sent successfully.</p>
<?php else: ?>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="contact.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>">

        <label for="message">Message:</label>
        <textarea id="message" name="message"><?= htmlspecialchars($message) ?></textarea>

        <button type="submit">Send Message</button>
    </form>
<?php endif; ?>

</body>
</html>
<?php include 'footer.php'; ?>