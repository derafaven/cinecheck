<?php
require_once 'auth.php';
requireLogin();
$msg = $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth    = getAuth();
    $actual  = $_POST['actual']   ?? '';
    $nueva   = $_POST['nueva']    ?? '';
    $confirma= $_POST['confirma'] ?? '';
    if ($actual !== $auth['password'])  { $err = 'La clave actual es incorrecta.'; }
    elseif (strlen($nueva) < 4)         { $err = 'La nueva clave debe tener al menos 4 caracteres.'; }
    elseif ($nueva !== $confirma)        { $err = 'Las claves no coinciden.'; }
    else { $auth['password'] = $nueva; saveAuth($auth); $msg = 'Clave actualizada correctamente.'; }
}
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>CineCheck — Cambiar Clave</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root{--bg:#080a0f;--s1:#0d0f16;--s2:#0a0c12;--bd:#1a1e2e;--bd2:#242840;--ac:#e8c14a;--tx:#e2e4ee;--mu2:#7880a0;--gr:#2ecc71;--rd:#e74c3c;--sw:220px;}
*{margin:0;padding:0;box-sizing:border-box;}
body{background:var(--bg);color:var(--tx);font-family:'DM Sans',sans-serif;min-height:100vh;display:flex;}
.sidebar{width:var(--sw);background:var(--s2);border-right:1px solid var(--bd);display:flex;flex-direction:column;flex-shrink:0;position:fixed;top:0;left:0;height:100vh;}
.sb-brand{padding:24px 20px 20px;border-bottom:1px solid var(--bd);}
.sb-logo{font-family:'Bebas Neue',sans-serif;font-size:22px;color:var(--tx);}
.sb-logo span{color:var(--ac);}
.sb-tag{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--mu2);margin-top:2px;}
nav{padding:16px 0;flex:1;}
.ni{display:flex;align-items:center;gap:10px;padding:11px 20px;font-size:13px;color:var(--mu2);border-left:2px solid transparent;text-decoration:none;transition:color .15s,background .15s;}
.ni:hover{color:var(--tx);background:rgba(255,255,255,.03);}
.ni.active{color:var(--tx);border-left-color:var(--ac);background:rgba(232,193,74,.05);}
.sb-footer{padding:16px 20px;border-top:1px solid var(--bd);}
.main{margin-left:var(--sw);flex:1;padding:32px 36px;}
.pt{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:1px;margin-bottom:24px;}
.card{background:var(--s1);border:1px solid var(--bd2);border-radius:8px;padding:28px;max-width:380px;}
.field{margin-bottom:16px;}
.field label{display:block;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--mu2);margin-bottom:7px;}
.field input{width:100%;background:var(--bg);border:1px solid var(--bd2);border-radius:4px;color:var(--tx);font-family:'DM Sans',sans-serif;font-size:14px;padding:12px 14px;outline:none;transition:border-color .2s;}
.field input:focus{border-color:var(--ac);}
.btn{background:var(--ac);color:#000;border:none;font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:2px;padding:13px 28px;border-radius:4px;cursor:pointer;}
.btn:hover{background:#f5d060;}
.ok{background:rgba(46,204,113,.1);border:1px solid rgba(46,204,113,.25);color:var(--gr);padding:12px 14px;border-radius:4px;font-size:13px;margin-bottom:16px;}
.er{background:rgba(231,76,60,.1);border:1px solid rgba(231,76,60,.25);color:#e87060;padding:12px 14px;border-radius:4px;font-size:13px;margin-bottom:16px;}
</style>
</head>
<body>
<div class="sidebar">
  <div class="sb-brand"><div class="sb-logo">CINE<span>CHECK</span></div><div class="sb-tag">Admin Panel</div></div>
  <nav>
    <a href="dashboard.php" class="ni">⚙ &nbsp;Dashboard</a>
    <a href="sitios.php" class="ni">🌐 &nbsp;Sitios</a>
    <a href="cambiar_clave.php" class="ni active">🔒 &nbsp;Cambiar Clave</a>
  </nav>
  <div class="sb-footer"><a href="logout.php" class="ni" style="padding:0;color:var(--rd)">↩ &nbsp;Cerrar Sesión</a></div>
</div>
<div class="main">
  <div class="pt">Cambiar Clave</div>
  <div class="card">
    <?php if($msg): ?><div class="ok">✓ <?=htmlspecialchars($msg)?></div><?php endif; ?>
    <?php if($err): ?><div class="er">✗ <?=htmlspecialchars($err)?></div><?php endif; ?>
    <form method="POST">
      <div class="field"><label>Clave Actual</label><input type="password" name="actual" required></div>
      <div class="field"><label>Nueva Clave</label><input type="password" name="nueva" required></div>
      <div class="field"><label>Confirmar Nueva Clave</label><input type="password" name="confirma" required></div>
      <button class="btn" type="submit">ACTUALIZAR</button>
    </form>
  </div>
</div>
</body>
</html>
