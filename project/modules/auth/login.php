<?php
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password_hash, nama FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if ($password === $user['password_hash']) {
            
            $_SESSION['logged_in'] = TRUE;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama'];

            header('Location: index.php?page=dashboard');
            exit();

        } else {
            $message = "<p style='color:red;'>Username atau password salah.</p>";
        }
    } else {
        $message = "<p style='color:red;'>Username atau password salah.</p>";
    }
    $stmt->close();
}
?>

<div class="login-box"> <h2>Silakan Login</h2>
    <form method="post" action="index.php?page=auth/login">
        
        <div class="input">
            <label>Username:</label>
            <input type="text" name="username" required/>
        </div>
        
        <div class="input">
            <label>Password:</label>
            <input type="password" name="password" required/>
        </div>
        
        <div class="submit">
            <input type="submit" name="submit" value="Masuk" />
        </div>
        
    </form>
</div>