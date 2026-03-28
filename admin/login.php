<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pw = $_POST['password'] ?? '';
    if ($pw === ADMIN_PASSWORD) {
        $_SESSION['hm_admin'] = true;
        header('Location: index.php');
        exit;
    }
    $error = 'Incorrect password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login – Hotomobil USA</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#0d0d0d;color:#e0e0e0;font-family:'Segoe UI',sans-serif;
     display:flex;align-items:center;justify-content:center;min-height:100vh}
.card{background:#1a1a1a;border:1px solid #2a2a2a;border-radius:6px;padding:40px;width:340px}
h2{font-size:1.3rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;
   color:#fff;margin-bottom:28px;text-align:center}
label{display:block;font-size:0.75rem;letter-spacing:1px;color:#999;margin-bottom:6px}
input[type=password]{width:100%;background:#111;border:1px solid #333;border-radius:4px;
  padding:11px 14px;color:#fff;font-size:0.95rem;outline:none;transition:border .2s}
input[type=password]:focus{border-color:#29a8e0}
.btn{width:100%;margin-top:20px;background:#29a8e0;color:#fff;border:none;border-radius:4px;
     padding:12px;font-size:0.88rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;
     cursor:pointer;transition:background .2s}
.btn:hover{background:#1d8fc0}
.error{background:#3a1a1a;border:1px solid #c04040;color:#f08080;
       padding:10px 14px;border-radius:4px;font-size:0.82rem;margin-bottom:16px;text-align:center}
.logo{text-align:center;font-size:0.7rem;letter-spacing:3px;color:#555;margin-top:24px}
</style>
</head>
<body>
<div class="card">
  <h2>Admin Panel</h2>
  <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="POST">
    <label>Password</label>
    <input type="password" name="password" autofocus required>
    <button class="btn" type="submit">Login</button>
  </form>
  <p class="logo">HOTOMOBIL USA</p>
</div>
</body>
</html>
