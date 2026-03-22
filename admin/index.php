<?php
require_once 'auth.php';
if (isLoggedIn()) { header('Location: dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = getAuth();
    if (($_POST['password'] ?? '') === $auth['password']) {
        $_SESSION['cc_admin'] = true;
        header('Location: dashboard.php'); exit;
    }
    $error = 'Clave incorrecta.';
}
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>CineCheck — Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root{--bg:#080a0f;--s1:#0d0f16;--bd2:#242840;--ac:#e8c14a;--tx:#e2e4ee;--mu2:#7880a0;--rd:#e74c3c;}
*{margin:0;padding:0;box-sizing:border-box;}
body{background:var(--bg);color:var(--tx);font-family:'DM Sans',sans-serif;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;}
body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse 60% 40% at 50% 0%,rgba(232,193,74,.05),transparent 60%);pointer-events:none;}
.logo{font-family:'Bebas Neue',sans-serif;font-size:48px;color:var(--tx);margin-bottom:4px;}
.logo span{color:var(--ac);}
.sub{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--mu2);margin-bottom:36px;}
.card{background:var(--s1);border:1px solid var(--bd2);border-radius:8px;padding:32px;width:100%;max-width:300px;position:relative;z-index:1;}
.label{display:block;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--mu2);margin-bottom:8px;}
.card input{width:100%;background:var(--bg);border:1px solid var(--bd2);border-radius:4px;color:var(--tx);font-family:'DM Sans',sans-serif;font-size:15px;padding:13px 14px;outline:none;transition:border-color .2s;margin-bottom:16px;}
.card input:focus{border-color:var(--ac);}
.btn{width:100%;background:var(--ac);color:#000;border:none;font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;padding:14px;border-radius:4px;cursor:pointer;transition:background .15s;}
.btn:hover{background:#f5d060;}
.err{background:rgba(231,76,60,.1);border:1px solid rgba(231,76,60,.25);color:#e87060;padding:11px 14px;border-radius:4px;font-size:13px;margin-bottom:18px;}
</style>
</head>
<body>
<div class="logo">CINE<span>CHECK</span></div>
<div class="sub">Administración</div>
<div class="card">
  <?php if($error): ?><div class="err"><?=htmlspecialchars($error)?></div><?php endif; ?>
  <form method="POST">
    <label class="label">Clave del administrador</label>
    <input type="password" name="password" required autofocus placeholder="••••••••">
    <button class="btn" type="submit">INGRESAR</button>
  </form>
</div>
</body>
</html>
