<?php
session_start();
session_unset(); // ✅ Clear all session variables
session_destroy(); // ✅ Destroy the session


echo "<script>
        alert('Logged out successfully!');
        window.location.href = 'admin_login.php';
      </script>";
exit();
?>
