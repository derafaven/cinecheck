<?php
require_once 'auth.php';
requireLogin();
$config = json_decode(file_get_contents(__DIR__ . '/../config/sitios.json'), true);
$sitios = $config['sitios'] ?? [];
$total  = count($sitios);
$activos= count(array_filter($sitios, fn($s) => $s['activo']));
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>CineCheck — Dashboard</title>
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
.pt{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:1px;margin-bottom:6px;}
.ps{font-size:13px;color:var(--mu2);margin-bottom:28px;}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:32px;}
.stat{background:var(--s1);border:1px solid var(--bd2);border-radius:8px;padding:18px 20px;}
.sl{font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--mu2);margin-bottom:8px;}
.sv{font-size:32px;font-family:'Bebas Neue',sans-serif;}
.sv.ac{color:var(--ac);}
.sv.gr{color:var(--gr);}
.sv.rd{color:var(--rd);}
.sec{font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:2px;color:var(--mu2);margin-bottom:12px;}
.qlinks{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;margin-bottom:32px;}
.ql{background:var(--s1);border:1px solid var(--bd2);border-radius:8px;padding:18px 20px;text-decoration:none;color:var(--tx);transition:border-color .2s,background .2s;display:block;}
.ql:hover{border-color:var(--ac);background:rgba(232,193,74,.03);}
.qi{font-size:20px;margin-bottom:8px;}
.qt{font-size:13px;font-weight:500;margin-bottom:3px;}
.qs{font-size:11px;color:var(--mu2);}
.sp-list{background:var(--s1);border:1px solid var(--bd2);border-radius:8px;overflow:hidden;}
.sp-row{display:flex;align-items:center;gap:12px;padding:13px 18px;border-bottom:1px solid var(--bd);}
.sp-row:last-child{border-bottom:none;}
.dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
.dot.ok{background:var(--gr);}
.dot.no{background:var(--rd);}
.sn{flex:1;font-size:13px;font-weight:500;}
.su{font-size:11px;color:var(--mu2);font-weight:300;}
.st{font-size:11px;color:var(--mu2);background:var(--s2);border:1px solid var(--bd);padding:2px 10px;border-radius:10px;}
</style>
</head>
<body>
<div class="sidebar">
  <div class="sb-brand"><div class="sb-logo">CINE<span>CHECK</span></div><div class="sb-tag">Admin Panel</div></div>
  <nav>
    <a href="dashboard.php" class="ni active">⚙ &nbsp;Dashboard</a>
    <a href="sitios.php" class="ni">🌐 &nbsp;Sitios</a>
    <a href="cambiar_clave.php" class="ni">🔒 &nbsp;Cambiar Clave</a>
  </nav>
  <div class="sb-footer"><a href="logout.php" class="ni" style="padding:0;color:var(--rd)">↩ &nbsp;Cerrar Sesión</a></div>
</div>
<div class="main">
  <div class="pt">Dashboard</div>
  <div class="ps">Gestiona los sitios de búsqueda de CineCheck.</div>
  <div class="stats">
    <div class="stat"><div class="sl">Total Sitios</div><div class="sv ac"><?=$total?></div></div>
    <div class="stat"><div class="sl">Activos</div><div class="sv gr"><?=$activos?></div></div>
    <div class="stat"><div class="sl">Inactivos</div><div class="sv rd"><?=$total-$activos?></div></div>
  </div>
  <div class="sec">Accesos Rápidos</div>
  <div class="qlinks">
    <a href="sitios.php" class="ql"><div class="qi">🌐</div><div class="qt">Gestionar Sitios</div><div class="qs">Agregar, editar o eliminar</div></a>
    <a href="cambiar_clave.php" class="ql"><div class="qi">🔒</div><div class="qt">Cambiar Clave</div><div class="qs">Actualizar contraseña</div></a>
    <a href="../" target="_blank" class="ql"><div class="qi">🎬</div><div class="qt">Ver Buscador</div><div class="qs">Abrir la app pública</div></a>
  </div>
  <?php if($sitios): ?>
  <div class="sec">Sitios Configurados</div>
  <div class="sp-list">
    <?php foreach($sitios as $s): ?>
    <div class="sp-row">
      <div class="dot <?=$s['activo']?'ok':'no'?>"></div>
      <div class="sn"><?=htmlspecialchars($s['nombre'])?> <span class="su"><?=htmlspecialchars($s['url_base'])?></span></div>
      <div class="st"><?=htmlspecialchars($s['tipo'])?></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
