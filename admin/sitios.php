<?php
require_once 'auth.php';
requireLogin();
$configFile = __DIR__ . '/../config/sitios.json';
function loadSitios(string $f): array { return json_decode(file_get_contents($f), true)['sitios'] ?? []; }
function saveSitios(string $f, array $s): void { file_put_contents($f, json_encode(['sitios'=>$s], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)); }

$msg = $err = '';
$action = $_GET['action'] ?? '';

if ($action === 'toggle' && isset($_GET['id'])) {
    $sitios = loadSitios($configFile);
    foreach ($sitios as &$s) { if ($s['id'] === $_GET['id']) $s['activo'] = !$s['activo']; }
    saveSitios($configFile, $sitios);
    header('Location: sitios.php?msg=Estado+actualizado'); exit;
}
if ($action === 'del' && isset($_GET['id'])) {
    $sitios = array_values(array_filter(loadSitios($configFile), fn($s) => $s['id'] !== $_GET['id']));
    saveSitios($configFile, $sitios);
    header('Location: sitios.php?msg=Sitio+eliminado'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sitios  = loadSitios($configFile);
    $tipo    = $_POST['tipo'] ?? 'dooplay_api';
    $esNuevo = empty($_POST['id_original']);
    $nuevo   = [
        'id'       => preg_replace('/[^a-z0-9_\-]/', '', strtolower(str_replace(' ', '-', $_POST['id'] ?? ''))),
        'nombre'   => trim($_POST['nombre'] ?? ''),
        'url_base' => rtrim(trim($_POST['url_base'] ?? ''), '/'),
        'activo'   => isset($_POST['activo']),
        'tipo'     => $tipo,
        'config'   => [],
    ];
    if (!$nuevo['id'] || !$nuevo['nombre'] || !$nuevo['url_base']) { $err = 'ID, Nombre y URL son obligatorios.'; }
    else {
        if ($tipo === 'dooplay_api')
            $nuevo['config'] = ['endpoint'=>trim($_POST['cfg_ep']??'/wp-json/dooplay/search/'),'param_keyword'=>trim($_POST['cfg_pk']??'keyword'),'necesita_nonce'=>isset($_POST['cfg_nn'])];
        elseif ($tipo === 'scraping_html')
            $nuevo['config'] = ['endpoint'=>trim($_POST['cfg_ep']??'/?s={keyword}'),'selector_titulo'=>trim($_POST['cfg_st']??'article h2'),'selector_enlace'=>trim($_POST['cfg_se']??'article h2 a'),'selector_imagen'=>trim($_POST['cfg_si']??'article img')];
        elseif ($tipo === 'api_get')
            $nuevo['config'] = ['endpoint'=>trim($_POST['cfg_ep']??'/api/search'),'param_keyword'=>trim($_POST['cfg_pk']??'q'),'campo_titulo'=>trim($_POST['cfg_ct']??'title'),'campo_url'=>trim($_POST['cfg_cu']??'url'),'campo_imagen'=>trim($_POST['cfg_ci']??'img')];

        if ($esNuevo) {
            if (array_filter($sitios, fn($s) => $s['id'] === $nuevo['id'])) { $err = 'Ya existe un sitio con ese ID.'; }
            else { $sitios[] = $nuevo; saveSitios($configFile, $sitios); header('Location: sitios.php?msg=Sitio+creado'); exit; }
        } else {
            foreach ($sitios as &$s) { if ($s['id'] === $_POST['id_original']) { $nuevo['id']=$s['id']; $s=$nuevo; break; } }
            saveSitios($configFile, $sitios);
            header('Location: sitios.php?msg=Sitio+actualizado'); exit;
        }
    }
}
$sitios   = loadSitios($configFile);
$editando = null;
if ($action === 'edit' && isset($_GET['id'])) foreach ($sitios as $s) { if ($s['id']===$_GET['id']) { $editando=$s; break; } }
if (isset($_GET['msg'])) $msg = urldecode($_GET['msg']);
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>CineCheck — Sitios</title>
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
.top{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
.pt{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:1px;}
.btn-add{background:var(--ac);color:#000;border:none;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:2px;padding:10px 20px;border-radius:4px;cursor:pointer;text-decoration:none;transition:background .15s;}
.btn-add:hover{background:#f5d060;}
.ok{background:rgba(46,204,113,.1);border:1px solid rgba(46,204,113,.25);color:var(--gr);padding:12px 16px;border-radius:4px;font-size:13px;margin-bottom:20px;}
.er{background:rgba(231,76,60,.1);border:1px solid rgba(231,76,60,.25);color:#e87060;padding:12px 16px;border-radius:4px;font-size:13px;margin-bottom:20px;}
table{width:100%;border-collapse:collapse;background:var(--s1);border:1px solid var(--bd2);border-radius:8px;overflow:hidden;}
th{padding:12px 16px;text-align:left;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--mu2);background:var(--s2);border-bottom:1px solid var(--bd2);}
td{padding:13px 16px;font-size:13px;border-bottom:1px solid var(--bd);vertical-align:middle;}
tr:last-child td{border-bottom:none;}
tr:hover td{background:rgba(255,255,255,.012);}
.badge{display:inline-block;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:500;}
.b-ok{background:rgba(46,204,113,.12);color:var(--gr);border:1px solid rgba(46,204,113,.2);}
.b-no{background:rgba(231,76,60,.10);color:var(--rd);border:1px solid rgba(231,76,60,.18);}
.b-tp{background:var(--s2);color:var(--mu2);border:1px solid var(--bd);}
.acts{display:flex;gap:6px;}
.bsm{font-size:11px;padding:5px 12px;border-radius:3px;cursor:pointer;font-weight:500;text-decoration:none;display:inline-block;border:1px solid;}
.be{background:rgba(232,193,74,.1);color:var(--ac);border-color:rgba(232,193,74,.2);}
.be:hover{background:rgba(232,193,74,.2);}
.bt{background:rgba(46,204,113,.1);color:var(--gr);border-color:rgba(46,204,113,.2);}
.bt:hover{background:rgba(46,204,113,.2);}
.bt.off{background:rgba(231,76,60,.08);color:var(--rd);border-color:rgba(231,76,60,.2);}
.bdel{background:rgba(231,76,60,.08);color:var(--rd);border-color:rgba(231,76,60,.18);}
.bdel:hover{background:rgba(231,76,60,.18);}
.mbg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.78);z-index:100;align-items:center;justify-content:center;padding:20px;}
.mbg.open{display:flex;}
.modal{background:var(--s1);border:1px solid var(--bd2);border-radius:10px;padding:28px;width:100%;max-width:500px;max-height:90vh;overflow-y:auto;}
.mt{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:1px;margin-bottom:22px;}
.field{margin-bottom:15px;}
.field label{display:block;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--mu2);margin-bottom:7px;}
.field input,.field select{width:100%;background:var(--bg);border:1px solid var(--bd2);border-radius:4px;color:var(--tx);font-family:'DM Sans',sans-serif;font-size:13px;padding:11px 13px;outline:none;transition:border-color .2s;}
.field input:focus,.field select:focus{border-color:var(--ac);}
.field select option{background:var(--bg);}
.chk{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--tx);cursor:pointer;}
.chk input{width:15px;height:15px;accent-color:var(--ac);}
.sep{border:none;border-top:1px solid var(--bd);margin:18px 0;}
.cfg{display:none;}
.cfg.on{display:block;}
.macts{display:flex;gap:10px;justify-content:flex-end;margin-top:22px;}
.bsave{background:var(--ac);color:#000;border:none;font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:2px;padding:11px 24px;border-radius:4px;cursor:pointer;}
.bsave:hover{background:#f5d060;}
.bcancel{background:transparent;color:var(--mu2);border:1px solid var(--bd2);font-size:13px;padding:11px 18px;border-radius:4px;cursor:pointer;}
.bcancel:hover{color:var(--tx);}
</style>
</head>
<body>
<div class="sidebar">
  <div class="sb-brand"><div class="sb-logo">CINE<span>CHECK</span></div><div class="sb-tag">Admin Panel</div></div>
  <nav>
    <a href="dashboard.php" class="ni">⚙ &nbsp;Dashboard</a>
    <a href="sitios.php" class="ni active">🌐 &nbsp;Sitios</a>
    <a href="cambiar_clave.php" class="ni">🔒 &nbsp;Cambiar Clave</a>
  </nav>
  <div class="sb-footer"><a href="logout.php" class="ni" style="padding:0;color:var(--rd)">↩ &nbsp;Cerrar Sesión</a></div>
</div>
<div class="main">
  <div class="top">
    <div class="pt">Sitios Configurados</div>
    <a href="#" class="btn-add" onclick="openModal(null);return false;">+ Agregar Sitio</a>
  </div>
  <?php if($msg): ?><div class="ok">✓ <?=htmlspecialchars($msg)?></div><?php endif; ?>
  <?php if($err): ?><div class="er">✗ <?=htmlspecialchars($err)?></div><?php endif; ?>
  <table>
    <thead><tr><th>Estado</th><th>Nombre</th><th>URL Base</th><th>Tipo</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach($sitios as $s): ?>
    <tr>
      <td><span class="badge <?=$s['activo']?'b-ok':'b-no'?>"><?=$s['activo']?'Activo':'Inactivo'?></span></td>
      <td><?=htmlspecialchars($s['nombre'])?></td>
      <td style="color:var(--mu2);font-size:12px;"><?=htmlspecialchars($s['url_base'])?></td>
      <td><span class="badge b-tp"><?=htmlspecialchars($s['tipo'])?></span></td>
      <td><div class="acts">
        <a href="#" class="bsm be" onclick='openModal(<?=json_encode($s,JSON_HEX_QUOT|JSON_HEX_TAG)?>);return false;'>Editar</a>
        <a href="sitios.php?action=toggle&id=<?=urlencode($s['id'])?>" class="bsm bt <?=$s['activo']?'':'off'?>"><?=$s['activo']?'Desactivar':'Activar'?></a>
        <a href="sitios.php?action=del&id=<?=urlencode($s['id'])?>" class="bsm bdel" onclick="return confirm('¿Eliminar este sitio?')">Eliminar</a>
      </div></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<div class="mbg" id="mbg">
  <div class="modal">
    <div class="mt" id="mtitle">Agregar Sitio</div>
    <form method="POST">
      <input type="hidden" name="id_original" id="fOrig">
      <div class="field"><label>ID único</label><input name="id" id="fId" placeholder="ej: misitiocine" required></div>
      <div class="field"><label>Nombre visible</label><input name="nombre" id="fNombre" placeholder="ej: Mi Sitio Cine" required></div>
      <div class="field"><label>URL Base</label><input name="url_base" id="fUrl" placeholder="https://misitio.com" required></div>
      <div class="field"><label>Tipo</label>
        <select name="tipo" id="fTipo" onchange="switchTipo(this.value)">
          <option value="dooplay_api">DooPlay API</option>
          <option value="scraping_html">Scraping HTML</option>
          <option value="api_get">API GET Genérica</option>
        </select>
      </div>
      <label class="chk"><input type="checkbox" name="activo" id="fActivo" checked> Sitio activo</label>
      <hr class="sep">
      <div class="cfg on" id="cfg_dooplay_api">
        <div class="field"><label>Endpoint</label><input name="cfg_ep" id="dp_ep" value="/wp-json/dooplay/search/"></div>
        <div class="field"><label>Parámetro keyword</label><input name="cfg_pk" id="dp_pk" value="keyword"></div>
        <label class="chk" style="margin-top:4px"><input type="checkbox" name="cfg_nn" id="dp_nn" checked> Requiere Nonce</label>
      </div>
      <div class="cfg" id="cfg_scraping_html">
        <div class="field"><label>Endpoint (usar {keyword})</label><input name="cfg_ep" value="/?s={keyword}"></div>
        <div class="field"><label>Selector título</label><input name="cfg_st" value="article h2"></div>
        <div class="field"><label>Selector enlace</label><input name="cfg_se" value="article h2 a"></div>
        <div class="field"><label>Selector imagen</label><input name="cfg_si" value="article img"></div>
      </div>
      <div class="cfg" id="cfg_api_get">
        <div class="field"><label>Endpoint</label><input name="cfg_ep" value="/api/search"></div>
        <div class="field"><label>Parámetro keyword</label><input name="cfg_pk" value="q"></div>
        <div class="field"><label>Campo título en JSON</label><input name="cfg_ct" value="title"></div>
        <div class="field"><label>Campo URL en JSON</label><input name="cfg_cu" value="url"></div>
        <div class="field"><label>Campo imagen en JSON</label><input name="cfg_ci" value="img"></div>
      </div>
      <div class="macts">
        <button type="button" class="bcancel" onclick="closeModal()">Cancelar</button>
        <button type="submit" class="bsave">GUARDAR</button>
      </div>
    </form>
  </div>
</div>
<script>
function openModal(d){
  document.getElementById('mbg').classList.add('open');
  if(!d){
    document.getElementById('mtitle').textContent='Agregar Sitio';
    document.getElementById('fOrig').value='';
    document.getElementById('fId').value='';
    document.getElementById('fId').disabled=false;
    document.getElementById('fNombre').value='';
    document.getElementById('fUrl').value='';
    document.getElementById('fActivo').checked=true;
    document.getElementById('fTipo').value='dooplay_api';
    document.getElementById('dp_ep').value='/wp-json/dooplay/search/';
    document.getElementById('dp_pk').value='keyword';
    document.getElementById('dp_nn').checked=true;
    switchTipo('dooplay_api');
  } else {
    document.getElementById('mtitle').textContent='Editar Sitio';
    document.getElementById('fOrig').value=d.id;
    document.getElementById('fId').value=d.id;
    document.getElementById('fId').disabled=true;
    document.getElementById('fNombre').value=d.nombre;
    document.getElementById('fUrl').value=d.url_base;
    document.getElementById('fActivo').checked=d.activo;
    document.getElementById('fTipo').value=d.tipo;
    switchTipo(d.tipo);
    if(d.config&&d.tipo==='dooplay_api'){
      document.getElementById('dp_ep').value=d.config.endpoint||'';
      document.getElementById('dp_pk').value=d.config.param_keyword||'';
      document.getElementById('dp_nn').checked=!!d.config.necesita_nonce;
    }
  }
}
function closeModal(){document.getElementById('mbg').classList.remove('open');}
function switchTipo(t){
  document.querySelectorAll('.cfg').forEach(el=>el.classList.remove('on'));
  const s=document.getElementById('cfg_'+t);
  if(s)s.classList.add('on');
}
document.getElementById('mbg').addEventListener('click',function(e){if(e.target===this)closeModal();});
<?php if($editando): ?>openModal(<?=json_encode($editando,JSON_HEX_TAG|JSON_HEX_QUOT)?>);<?php endif; ?>
</script>
</body>
</html>
