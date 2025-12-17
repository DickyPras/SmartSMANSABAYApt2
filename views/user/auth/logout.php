<?php 
session_start();

// Hapus semua data session
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout...</title>
    <script>
        window.location.href = '../../user/auth/login.php';
    </script>
</head>
<body>
    <p>Sedang keluar...</p>
</body>
</html>