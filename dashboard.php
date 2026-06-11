<?php 
require_once 'contenido.php';
require_once __DIR__ . '/models/dashboar/listarOrdenPorProximidad.php';

$model = new OrdenesProduccion();
$ordenes = $model->listarOrdenesPorProximidad();
$ordenesJson = json_encode($ordenes);
?>

<div style="background:var(--color-background-primary); border:0.5px solid var(--color-border-tertiary); border-radius:12px; overflow:hidden; font-family:sans-serif;">
  <div style="padding:16px 20px; border-bottom:0.5px solid var(--color-border-tertiary); display:flex; align-items:center; justify-content:space-between;">
    <span style="font-size:15px; font-weight:500; display:flex; align-items:center; gap:8px;">
      🕐 Órdenes por proximidad de vencimiento
    </span>
    <span id="total-badge" style="font-size:12px; color:#666; background:#f5f5f5; border:0.5px solid #ddd; border-radius:20px; padding:2px 10px;"></span>
  </div>

  <div style="overflow-x:auto">
    <table id="tabla-ordenes" style="width:100%; border-collapse:collapse; font-size:13px; table-layout:fixed;">
      <thead>
        <tr style="background:#f9f9f9; border-bottom:0.5px solid #e0e0e0;">
          <th style="padding:10px 16px; text-align:left; font-weight:500; font-size:12px; color:#888; width:130px;">Cliente</th>
          <th style="padding:10px 16px; text-align:left; font-weight:500; font-size:12px; color:#888; width:70px;">OP</th>
          <th style="padding:10px 16px; text-align:left; font-weight:500; font-size:12px; color:#888; width:130px;">Estilo</th>
          <th style="padding:10px 16px; text-align:left; font-weight:500; font-size:12px; color:#888; width:100px;">División</th>
          <th style="padding:10px 16px; text-align:left; font-weight:500; font-size:12px; color:#888; width:80px;">Color</th>
          <th style="padding:10px 16px; text-align:left; font-weight:500; font-size:12px; color:#888; width:105px;">Fecha fin</th>
          <th style="padding:10px 16px; text-align:left; font-weight:500; font-size:12px; color:#888; width:80px;">Días rest.</th>
          <th style="padding:10px 16px; text-align:left; font-weight:500; font-size:12px; color:#888; width:95px;">Estado</th>
        </tr>
      </thead>
      <tbody id="tabla-body"></tbody>
    </table>
  </div>

  <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:0.5px solid #e0e0e0;">
    <span id="pag-info" style="font-size:12px; color:#888;"></span>
    <div id="pag-btns" style="display:flex; gap:4px;"></div>
  </div>
</div>

<style>
  #tabla-ordenes tbody tr { border-bottom: 0.5px solid #f0f0f0; transition: background .12s; }
  #tabla-ordenes tbody tr:hover { background: #fafafa; }
  #tabla-ordenes tbody td { padding: 10px 16px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .badge { display:inline-block; font-size:11px; font-weight:500; padding:2px 8px; border-radius:20px; }
  .badge-vencida  { background:#FCEBEB; color:#A32D2D; }
  .badge-hoy      { background:#FAEEDA; color:#633806; }
  .badge-urgente  { background:#FAECE7; color:#712B13; }
  .badge-proxima  { background:#E6F1FB; color:#0C447C; }
  .badge-tiempo   { background:#EAF3DE; color:#27500A; }
  .pag-btn { background:transparent; border:0.5px solid #ccc; border-radius:8px; padding:4px 12px; font-size:12px; cursor:pointer; }
  .pag-btn:hover:not(:disabled) { background:#f5f5f5; }
  .pag-btn:disabled { opacity:.35; cursor:default; }
  .pag-btn.active { background:#E6F1FB; color:#0C447C; border-color:#378ADD; }
</style>

<script>
const datos = <?= $ordenesJson ?>;
const POR_PAG = 10;
let pagina = 1;

function badgeClass(e) {
  if (e === 'Vencida')   return 'badge-vencida';
  if (e === 'Vence hoy') return 'badge-hoy';
  if (e === 'Urgente')   return 'badge-urgente';
  if (e === 'Próxima')   return 'badge-proxima';
  return 'badge-tiempo';
}

function diasTexto(d) {
  if (d < 0)  return '<span style="color:#A32D2D;font-weight:500">' + Math.abs(d) + ' atrás</span>';
  if (d === 0) return '<span style="color:#854F0B;font-weight:500">Hoy</span>';
  if (d <= 7)  return '<span style="color:#854F0B;font-weight:500">' + d + '</span>';
  return '<span style="color:#3B6D11">' + d + '</span>';
}

function render() {
  const total = datos.length;
  const totalPags = Math.ceil(total / POR_PAG);
  const inicio = (pagina - 1) * POR_PAG;
  const slice = datos.slice(inicio, inicio + POR_PAG);

  document.getElementById('total-badge').textContent = total + ' registros';
  document.getElementById('pag-info').textContent =
    'Mostrando ' + (inicio+1) + '–' + Math.min(inicio + POR_PAG, total) + ' de ' + total;

  document.getElementById('tabla-body').innerHTML = slice.map(o => `
    <tr>
      <td>${o.cliente}</td>
      <td style="font-weight:500">${o.op}</td>
      <td>${o.estilo}</td>
      <td>${o.division}</td>
      <td>${o.color}</td>
      <td>${o.fechafin}</td>
      <td>${diasTexto(o.dias_restantes)}</td>
      <td><span class="badge ${badgeClass(o.estado)}">${o.estado}</span></td>
    </tr>`).join('');

  let html = `<button class="pag-btn" onclick="ir(${pagina-1})" ${pagina===1?'disabled':''}>← Ant</button>`;
  for (let p = 1; p <= totalPags; p++) {
    html += `<button class="pag-btn ${p===pagina?'active':''}" onclick="ir(${p})">${p}</button>`;
  }
  html += `<button class="pag-btn" onclick="ir(${pagina+1})" ${pagina===totalPags?'disabled':''}>Sig →</button>`;
  document.getElementById('pag-btns').innerHTML = html;
}

function ir(p) { pagina = p; render(); }
render();
</script>

<?php require_once 'footer.php'; ?>