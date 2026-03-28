<?php
require_once __DIR__ . '/auth.php';
requireAuth();
require_once __DIR__ . '/config.php';

$data  = loadUnits();
$units = $data['units'] ?? [];

$statusLabel = ['available' => 'Available', 'sold' => 'Sold', 'demo' => 'Demo'];
$statusColor = ['available' => '#2ecc71', 'sold' => '#e74c3c', 'demo' => '#e07800'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin – Hotomobil USA</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#0d0d0d;color:#e0e0e0;font-family:'Segoe UI',sans-serif;min-height:100vh}
a{color:#29a8e0;text-decoration:none}

/* Header */
.adm-header{background:#111;border-bottom:1px solid #222;padding:0 24px;
  display:flex;align-items:center;justify-content:space-between;height:60px}
.adm-header h1{font-size:1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#fff}
.adm-header a.logout{font-size:0.78rem;letter-spacing:1px;text-transform:uppercase;
  color:#888;border:1px solid #333;border-radius:3px;padding:6px 14px;transition:all .2s}
.adm-header a.logout:hover{color:#fff;border-color:#555}

/* Main */
.adm-main{max-width:1300px;margin:0 auto;padding:32px 24px}
.toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px}
.toolbar h2{font-size:1.1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#fff}
.btn-add{background:#29a8e0;color:#fff;border:none;border-radius:4px;
  padding:10px 22px;font-size:0.82rem;font-weight:700;letter-spacing:1.5px;
  text-transform:uppercase;cursor:pointer;transition:background .2s}
.btn-add:hover{background:#1d8fc0}

/* Table */
.tbl-wrap{overflow-x:auto;border-radius:6px;border:1px solid #222}
table{width:100%;border-collapse:collapse;font-size:0.83rem}
thead{background:#1a1a1a}
th{padding:12px 14px;text-align:left;font-size:0.7rem;letter-spacing:1.5px;
   text-transform:uppercase;color:#888;font-weight:600;white-space:nowrap}
td{padding:12px 14px;border-top:1px solid #1e1e1e;vertical-align:middle}
tr:hover td{background:#141414}
.unit-img{width:70px;height:50px;object-fit:cover;border-radius:3px;background:#222;display:block}
.badge{display:inline-block;padding:3px 10px;border-radius:2px;font-size:0.68rem;
       font-weight:700;letter-spacing:1px;text-transform:uppercase}
.price{font-weight:700;color:#29a8e0}
.actions{display:flex;gap:8px}
.btn-edit,.btn-del{border:none;border-radius:3px;padding:6px 14px;font-size:0.72rem;
  font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:all .2s}
.btn-edit{background:#1e3a4a;color:#29a8e0;border:1px solid #29a8e0}
.btn-edit:hover{background:#29a8e0;color:#fff}
.btn-del{background:#3a1515;color:#e74c3c;border:1px solid #e74c3c}
.btn-del:hover{background:#e74c3c;color:#fff}

/* Modal overlay */
.overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);
  z-index:1000;align-items:center;justify-content:center;padding:20px}
.overlay.open{display:flex}
.modal{background:#1a1a1a;border:1px solid #2a2a2a;border-radius:6px;
  width:100%;max-width:640px;max-height:90vh;overflow-y:auto;padding:32px}
.modal h3{font-size:1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;
  color:#fff;margin-bottom:28px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
.form-group{margin-bottom:16px}
.form-group.full{grid-column:1/-1}
label{display:block;font-size:0.72rem;letter-spacing:1px;color:#888;margin-bottom:6px;text-transform:uppercase}
input[type=text],input[type=number],select,textarea{width:100%;background:#111;border:1px solid #333;
  border-radius:4px;padding:10px 12px;color:#e0e0e0;font-size:0.88rem;outline:none;transition:border .2s;
  font-family:inherit}
input:focus,select:focus,textarea:focus{border-color:#29a8e0}
select option{background:#1a1a1a}

/* Image upload */
.img-upload-area{border:2px dashed #333;border-radius:4px;padding:20px;text-align:center;
  cursor:pointer;transition:border .2s;position:relative}
.img-upload-area:hover{border-color:#29a8e0}
.img-upload-area input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer}
.img-preview{width:100%;max-height:160px;object-fit:cover;border-radius:3px;margin-top:10px;display:none}

/* Upgrades */
.upgrades-list{display:flex;flex-direction:column;gap:8px;margin-bottom:10px}
.upgrade-item{display:flex;gap:8px;align-items:center}
.upgrade-item input{flex:1}
.btn-remove-upgrade{background:#3a1515;color:#e74c3c;border:1px solid #e74c3c;
  border-radius:3px;padding:6px 10px;font-size:0.8rem;cursor:pointer;transition:all .2s;white-space:nowrap}
.btn-remove-upgrade:hover{background:#e74c3c;color:#fff}
.btn-add-upgrade{background:#1e2a1e;color:#2ecc71;border:1px solid #2ecc71;
  border-radius:3px;padding:8px 14px;font-size:0.75rem;font-weight:700;letter-spacing:1px;
  text-transform:uppercase;cursor:pointer;transition:all .2s;width:100%}
.btn-add-upgrade:hover{background:#2ecc71;color:#fff}

/* Modal footer */
.modal-footer{display:flex;gap:12px;justify-content:flex-end;margin-top:24px;
  padding-top:20px;border-top:1px solid #222}
.btn-cancel{background:none;border:1px solid #444;color:#888;border-radius:4px;
  padding:10px 22px;font-size:0.82rem;font-weight:700;letter-spacing:1px;
  text-transform:uppercase;cursor:pointer;transition:all .2s}
.btn-cancel:hover{border-color:#888;color:#fff}
.btn-save{background:#29a8e0;color:#fff;border:none;border-radius:4px;
  padding:10px 28px;font-size:0.82rem;font-weight:700;letter-spacing:1.5px;
  text-transform:uppercase;cursor:pointer;transition:background .2s}
.btn-save:hover{background:#1d8fc0}

/* Toast */
.toast{position:fixed;bottom:24px;right:24px;background:#1e3a1e;color:#2ecc71;
  border:1px solid #2ecc71;border-radius:4px;padding:12px 20px;font-size:0.85rem;
  font-weight:600;z-index:9999;opacity:0;transform:translateY(10px);
  transition:opacity .3s,transform .3s}
.toast.show{opacity:1;transform:translateY(0)}
.toast.error{background:#3a1a1a;color:#e74c3c;border-color:#e74c3c}

@media(max-width:600px){.form-row{grid-template-columns:1fr}}
</style>
</head>
<body>

<header class="adm-header">
  <h1>Hotomobil USA &mdash; Admin</h1>
  <a class="logout" href="logout.php">Log Out</a>
</header>

<main class="adm-main">
  <div class="toolbar">
    <h2>In Stock Units (<?= count($units) ?>)</h2>
    <button class="btn-add" onclick="openModal()">+ Add Unit</button>
  </div>

  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>Image</th>
          <th>Model</th>
          <th>Year</th>
          <th>Color</th>
          <th>Location</th>
          <th>VIN #</th>
          <th>Status</th>
          <th>Price</th>
          <th>Upgrades</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($units as $u):
          $img = $u['image'] ? ('../' . $u['image']) : defaultImage($u['model']);
          $sc  = $statusColor[$u['status']] ?? '#888';
          $sl  = $statusLabel[$u['status']] ?? $u['status'];
        ?>
        <tr>
          <td><img class="unit-img" src="<?= htmlspecialchars($img) ?>" alt=""></td>
          <td><strong><?= htmlspecialchars($u['model']) ?></strong></td>
          <td><?= htmlspecialchars($u['year']) ?></td>
          <td><?= htmlspecialchars($u['color']) ?></td>
          <td><?= htmlspecialchars($u['location']) ?></td>
          <td><code style="color:#aaa"><?= htmlspecialchars($u['vin']) ?></code></td>
          <td><span class="badge" style="background:<?= $sc ?>22;color:<?= $sc ?>;border:1px solid <?= $sc ?>44"><?= $sl ?></span></td>
          <td class="price"><?= htmlspecialchars($u['price'] ?: '—') ?></td>
          <td><?= count($u['upgrades']) ?> item<?= count($u['upgrades']) !== 1 ? 's' : '' ?></td>
          <td>
            <div class="actions">
              <button class="btn-edit" onclick="editUnit('<?= htmlspecialchars($u['id']) ?>')">Edit</button>
              <button class="btn-del"  onclick="deleteUnit('<?= htmlspecialchars($u['id']) ?>','<?= htmlspecialchars(addslashes($u['model'])) ?> - <?= htmlspecialchars($u['vin']) ?>')">Delete</button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$units): ?>
        <tr><td colspan="10" style="text-align:center;padding:40px;color:#555">No units yet. Click "+ Add Unit" to get started.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<!-- ── ADD / EDIT MODAL ────────────────────────────────── -->
<div class="overlay" id="overlay">
  <div class="modal">
    <h3 id="modal-title">Add Unit</h3>
    <form id="unit-form" enctype="multipart/form-data">
      <input type="hidden" id="f-id" name="id">
      <input type="hidden" id="f-existing-image" name="existing_image">

      <div class="form-row">
        <div class="form-group">
          <label>Model *</label>
          <select name="model" id="f-model">
            <option>Gladiator XL</option>
            <option>Gladiator XLE</option>
            <option>Gladiator L</option>
            <option>Gladiator S Premium</option>
            <option>Cyberglad Premium</option>
          </select>
        </div>
        <div class="form-group">
          <label>Year</label>
          <input type="text" name="year" id="f-year" placeholder="e.g. 2025">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Color / Finish</label>
          <input type="text" name="color" id="f-color" placeholder="e.g. Black Polyurea">
        </div>
        <div class="form-group">
          <label>Location (USA)</label>
          <input type="text" name="location" id="f-location" placeholder="e.g. Houston, TX">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>VIN / Product # *</label>
          <input type="text" name="vin" id="f-vin" placeholder="e.g. GLX098">
        </div>
        <div class="form-group">
          <label>Price</label>
          <input type="text" name="price" id="f-price" placeholder="e.g. $52,000">
        </div>
      </div>

      <div class="form-group">
        <label>Status</label>
        <select name="status" id="f-status">
          <option value="available">Available</option>
          <option value="sold">Sold</option>
          <option value="demo">Demo</option>
        </select>
      </div>

      <div class="form-group">
        <label>Unit Image</label>
        <div class="img-upload-area" id="upload-area">
          <input type="file" name="image" id="f-image" accept="image/jpeg,image/png,image/webp">
          <div id="upload-hint">
            <span style="font-size:1.4rem">📷</span><br>
            <span style="font-size:0.8rem;color:#666">Click to upload (JPEG/PNG/WebP, max 5 MB)</span>
          </div>
          <img id="img-preview" class="img-preview" alt="Preview">
        </div>
      </div>

      <div class="form-group">
        <label>Upgrades</label>
        <div class="upgrades-list" id="upgrades-list"></div>
        <button type="button" class="btn-add-upgrade" onclick="addUpgrade()">+ Add Upgrade</button>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn-save" id="btn-save">Save Unit</button>
      </div>
    </form>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
const overlay = document.getElementById('overlay');

// ── Open modal (add) ────────────────────────────────────
function openModal() {
  resetForm();
  document.getElementById('modal-title').textContent = 'Add Unit';
  overlay.classList.add('open');
}

// ── Open modal (edit) ───────────────────────────────────
async function editUnit(id) {
  const res  = await api({ action: 'get', id });
  if (!res.ok) return showToast(res.error, true);
  const u = res.data;
  resetForm();
  document.getElementById('modal-title').textContent = 'Edit Unit';
  document.getElementById('f-id').value    = u.id;
  document.getElementById('f-model').value = u.model;
  document.getElementById('f-year').value  = u.year;
  document.getElementById('f-color').value = u.color;
  document.getElementById('f-location').value = u.location;
  document.getElementById('f-vin').value   = u.vin;
  document.getElementById('f-price').value = u.price;
  document.getElementById('f-status').value = u.status;
  document.getElementById('f-existing-image').value = u.image || '';
  if (u.image) {
    const prev = document.getElementById('img-preview');
    prev.src = '../' + u.image;
    prev.style.display = 'block';
  }
  (u.upgrades || []).forEach(t => addUpgrade(t));
  overlay.classList.add('open');
}

// ── Close modal ─────────────────────────────────────────
function closeModal() { overlay.classList.remove('open'); }
overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });

// ── Reset form ──────────────────────────────────────────
function resetForm() {
  document.getElementById('unit-form').reset();
  document.getElementById('f-id').value = '';
  document.getElementById('f-existing-image').value = '';
  document.getElementById('img-preview').style.display = 'none';
  document.getElementById('upgrades-list').innerHTML = '';
}

// ── Image preview ───────────────────────────────────────
document.getElementById('f-image').addEventListener('change', function() {
  if (!this.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const prev = document.getElementById('img-preview');
    prev.src = e.target.result;
    prev.style.display = 'block';
  };
  reader.readAsDataURL(this.files[0]);
});

// ── Upgrades ────────────────────────────────────────────
function addUpgrade(value = '') {
  const list = document.getElementById('upgrades-list');
  const item = document.createElement('div');
  item.className = 'upgrade-item';
  item.innerHTML = `
    <input type="text" name="upgrades[]" value="${escHtml(value)}" placeholder="Upgrade description">
    <button type="button" class="btn-remove-upgrade" onclick="this.parentElement.remove()">✕ Remove</button>`;
  list.appendChild(item);
}

function escHtml(s) {
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Submit form ─────────────────────────────────────────
document.getElementById('unit-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.getElementById('btn-save');
  btn.disabled = true; btn.textContent = 'Saving…';
  const fd = new FormData(this);
  fd.append('action', 'save');
  try {
    const res = await fetch('api.php', { method: 'POST', body: fd });
    const json = await res.json();
    if (json.ok) { showToast('Unit saved successfully.'); setTimeout(() => location.reload(), 900); }
    else showToast(json.error || 'Save failed.', true);
  } catch { showToast('Network error.', true); }
  btn.disabled = false; btn.textContent = 'Save Unit';
});

// ── Delete ──────────────────────────────────────────────
async function deleteUnit(id, label) {
  if (!confirm('Delete "' + label + '"?\nThis cannot be undone.')) return;
  const res = await api({ action: 'delete', id });
  if (res.ok) { showToast('Unit deleted.'); setTimeout(() => location.reload(), 800); }
  else showToast(res.error || 'Delete failed.', true);
}

// ── Generic API call (JSON POST, no file) ───────────────
async function api(params) {
  const fd = new FormData();
  for (const [k, v] of Object.entries(params)) fd.append(k, v);
  const res = await fetch('api.php', { method: 'POST', body: fd });
  return res.json();
}

// ── Toast ────────────────────────────────────────────────
function showToast(msg, isError = false) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast' + (isError ? ' error' : '');
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}
</script>

</body>
</html>
