<?php
session_start();
include 'db.php';

//Check if admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch total users count
$total_users = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];

//Search users
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $sql = "SELECT * FROM users WHERE username LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_param = "%$search%";
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    //Fetch all users
    $result = $conn->query("SELECT * FROM users");
}

//Handle user deletion
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $user_id");
    header("Location: admin_dashboard.php");
    exit();
}

//Handle adding new user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $new_username = trim($_POST['new_username']);
    $new_password = trim($_POST['new_password']);
    $security_question = trim($_POST['security_question']);
    $security_answer = trim($_POST['security_answer']);

    $conn->query("INSERT INTO users (username, password_hash, security_question, security_answer_hash) 
                  VALUES ('$new_username', '$new_password', '$security_question', '$security_answer')");
    header("Location: admin_dashboard.php");
    exit();
}

//Handle password reset
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $user_id = intval($_POST['user_id']);
    $new_password = trim($_POST['new_password']);
    $conn->query("UPDATE users SET password_hash = '$new_password' WHERE id = $user_id");
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: green;
            text-align: center;
            font-size: 12px;

        }
        .container {
            width: 80%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            color: #333;
        }
        .stats {
            background: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
        }
        table {
            width: 50%;
            margin-top: 20px;
            border-collapse: collapse;
            font-size: 10;
        }
        table, th, td {
            border: 1px solid #ddd;
            font-size: 10px;
        }
        th, td {
            padding: 1px;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
        }
        .btn {
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            margin: 5px;
        }
        .delete {
            background: #dc3545;
            color: white;
        }
        .reset {
            background: #ffc107;
            color: black;
        }
        .add-user {
            background: #28a745;
            color: white;
            padding: 10px 15px;
            display: block;
            margin: 10px auto;
            width: 200px;
        }
        .logout {
            background: #343a40;
            color: white;
            padding: 10px;
            display: inline-block;
            margin-top: 20px;
        }
        input {
            padding: 8px;
            margin: 5px;
            width: 80%;
        }
        form {
            display: inline-block;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Admin Profile -->
    <h2>👋 Welcome, <?php echo $_SESSION['admin_username']; ?>!</h2>

    <h3>📊 Basic Statistics</h3>
    <p class="stats">Total Users: <?php echo $total_users; ?></p>

    <!-- ✅ Search Users -->
    <h3>🔍 Search Users</h3>
    <form method="get">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Enter username">
        <button type="submit">Search</button>
    </form>

    <!-- ✅ Display Users -->
    <h3>📋 Registered Users</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Password</th>
            <th>Security Question</th>
            <th>Security Answer</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['password_hash']); ?></td>
                <td><?php echo htmlspecialchars($row['security_question']); ?></td>
                <td><?php echo htmlspecialchars($row['security_answer_hash']); ?></td>
                <td>
                    <a href="?delete=<?php echo $row['id']; ?>" class="btn delete" onclick="return confirm('Are you sure?')">🗑️ Delete</a>
                    <form method="post">
                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                        <input type="text" name="new_password" placeholder="New Password" required>
                        <button type="submit" name="reset_password" class="btn reset">🔄 Reset</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <!-- ✅ Add User -->
    <h3>➕ Add New User</h3>
    <form method="post">
        <input type="text" name="new_username" placeholder="Username" required>
        <input type="text" name="new_password" placeholder="Password" required><br>
        Security Question<select name="security_question" placeholder ="Security Question" required>
    <option value="" disabled selected>-- Select a security question --</option>
    <option value="What is your favorite color?">What is your favorite color?</option>
    <option value="What is your pet’s name?">What is your pet’s name?</option>
    <option value="What city were you born in?">What city were you born in?</option>
    <option value="What is your mother’s maiden name?">What is your mother’s maiden name?</option>
    <option value="What is your first school?">What is your first school?</option>
</select>

        <input type="text" name="security_answer" placeholder="Security Answer" required>
        <button type="submit" name="add_user" class="btn add-user">Add User</button>
    </form>

    <!-- ✅ Settings & Logout -->
    <a href="admin_logout.php" class="btn logout">🚪 Logout</a>
    <a href="settings.php" class="btn settings">Set allowable Time and Location</a>
</div>


</div>

</body>
</html>
