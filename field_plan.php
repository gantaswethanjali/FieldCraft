<?php
session_start();
?>
<?php include 'header.php'; ?>

<style>
/* ===== Layout & spacing to clear fixed navbar ===== */
:root{
  --nav-h: 72px; --nav-gap: 20px;
  --fc-green:#4caf50; --fc-green-2:#81c784; --fc-sky:#d0efff; --fc-card:#ffffff; --fc-ink:#212529;
}
@media (max-width: 991.98px){ :root{ --nav-h: 60px; } }
html, body { height: 100%; }
body{
  padding-top: calc(var(--nav-h) + var(--nav-gap));
  background: linear-gradient(to top, #98db9a 0%, #bfe7ae 35%, var(--fc-sky) 100%);
  min-height: 100vh; overflow-x: hidden; color: var(--fc-ink);
}
/* Reduced motion */
@media (prefers-reduced-motion: reduce){
  .field-bg *,.section-card,.sticky-summary,[data-magnetic]{animation:none!important;transition:none!important}
}
/* ===== FieldCraft background ===== */
.field-bg{position:fixed;inset:0;z-index:-2;overflow:hidden;background:linear-gradient(to top,#8fd694 0%,#b4e0a1 40%,#d0efff 100%)}
.field-bg .grass{position:absolute;left:0;right:0;bottom:0;height:200px;background:linear-gradient(to top,var(--fc-green) 0%,var(--fc-green-2) 100%);overflow:hidden}
.field-bg .grass::before{content:"";position:absolute;inset:0;background:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='140'><path fill='%234caf50' d='M0,140 C40,120 80,120 120,140 C160,160 200,160 240,140 C280,120 320,120 360,140 L400,140 L400,0 L0,0 Z'/></svg>") repeat-x bottom;animation:wave 9s linear infinite;opacity:.55}
@keyframes wave{from{background-position-x:0}to{background-position-x:400px}}
.field-bg .cloud{position:absolute;background:#fff;border-radius:50%;
  box-shadow:60px 20px 0 10px #fff,120px 25px 0 0 #fff,90px -10px 0 10px #fff,30px 10px 0 10px #fff;
  width:110px;height:65px;opacity:.85;animation:drift 55s linear infinite}
.field-bg .cloud.c1{top:90px;left:-160px;animation-delay:-8s}
.field-bg .cloud.c2{top:150px;left:-260px;width:140px;height:80px;animation-delay:-28s}
.field-bg .cloud.c3{top:60px;left:-420px;width:170px;height:90px;animation-delay:-18s}
@keyframes drift{from{transform:translateX(0)}to{transform:translateX(130vw)}}
.field-bg .leaf{position:absolute;top:-40px;width:22px;height:22px;background:radial-gradient(circle at 40% 40%,#ffb347 35%,#ffcc33 70%);
  border-radius:0 50% 50% 50%;transform:rotate(45deg);opacity:.9;animation:fall linear infinite}
@keyframes fall{0%{transform:translateY(0) rotate(0)}100%{transform:translateY(110vh) rotate(360deg)}}
/* ===== Cards & tables ===== */
.container{ max-width: 1200px; }
.page-intro{ text-align:center; margin-bottom: 1.25rem; }
.page-intro h2{ font-size: clamp(1.4rem, 1rem + 1.6vw, 2rem); line-height:1.2; margin-bottom:.25rem; }
.page-intro p{ margin:0 0 .75rem 0; color:#6c757d; }
.section-card{ background: var(--fc-card); border-radius: 15px; box-shadow: 0 0 15px rgba(0,0,0,0.08); margin-bottom:28px; padding:20px; animation: cardIn .6s ease forwards; }
@keyframes cardIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}
.section-card h3{ border-left:6px solid #198754; padding-left:10px; margin:0 0 14px 0; font-weight:700; font-size:clamp(1.05rem,.95rem+.6vw,1.25rem);}
.service-table th{background-color:#212529;color:#fff;text-align:center;vertical-align:middle}
.service-table td{vertical-align:middle;}
.service-table tbody tr:hover{ background:#f3f8f4; }
.table-actions{display:flex;gap:.5rem;flex-wrap:wrap;align-items:center}
.table-actions .btn-sm{border-radius:999px}
.sticky-summary{position:sticky; bottom:12px; z-index:10;background: rgba(255,255,255,.95); backdrop-filter: blur(6px); border-radius:16px; box-shadow:0 12px 24px rgba(0,0,0,.12); padding:10px 14px;}
.btn-continue{ position:relative; overflow:hidden; border-radius:12px }
.btn-continue:hover{ transform: translateY(-1px) }
.btn-continue .mower{ position:absolute; left:-42px; bottom:-6px; font-size:22px; animation:mower 2s ease-in-out infinite; opacity:.9 }
@keyframes mower{0%{transform:translateX(0)}50%{transform:translateX(26px) rotate(-2deg)}100%{transform:translateX(0)}}
</style>

<!-- Background -->
<div class="field-bg" aria-hidden="true">
  <div class="cloud c1"></div>
  <div class="cloud c2"></div>
  <div class="cloud c3"></div>
  <?php for($i=0;$i<8;$i++): ?>
    <div class="leaf" style="left:<?=rand(2,98)?>%; animation-duration: <?=rand(12,26)?>s; animation-delay:-<?=rand(0,26)?>s;"></div>
  <?php endfor; ?>
  <div class="grass"></div>
</div>

<div class="container py-4">
  <div class="page-intro">
    <h2 class="fw-bold text-success">FieldCraft Outdoor Services</h2>
    <p>Professional, reliable one-time maintenance services for your sports field.</p>
  </div>

  <?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($_SESSION['error']) ?></div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <form id="serviceForm" action="field_plan_regex.php" method="POST">
    <!-- Routine Section -->
    <div class="section-card">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="m-0">🏟️ Gardening Services</h3>
        <div class="table-actions">
          <button class="btn btn-sm btn-outline-success" type="button" id="selectAllRoutine">Select all</button>
          <button class="btn btn-sm btn-outline-secondary" type="button" id="clearRoutine">Clear</button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered service-table align-middle text-center">
          <thead><tr><th>Select</th><th>Service</th><th>Purpose / Definition</th></tr></thead>
          <tbody>
            <?php
            $routine_services = [
              ["Lawn Mowing (Regular Cutting)","Cutting grass to sport-specific height."],
              ["Edging & Hedge Trimming","Neatening pitch edges and borders."],
			  ["Weed & Pest Control","Targets weeds, insects, or turf diseases."]
            ];
            foreach($routine_services as $i=>$srv){
                $id="routine_{$i}";
                echo "<tr>
                        <td><input type='checkbox' name='services[]' id='{$id}' value='".htmlspecialchars($srv[0])."' class='form-check-input'></td>
                        <td class='text-start'><label for='{$id}' class='fw-semibold mb-0'>".htmlspecialchars($srv[0])."</label></td>
                        <td class='text-start'>".htmlspecialchars($srv[1])."</td>
                      </tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Special Section -->
    <div class="section-card">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="m-0">🌾 Sports Field – Specialised & Seasonal Works</h3>
        <div class="table-actions">
          <button class="btn btn-sm btn-outline-success" type="button" id="selectAllSpecial">Select all</button>
          <button class="btn btn-sm btn-outline-secondary" type="button" id="clearSpecial">Clear</button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered service-table align-middle text-center">
          <thead><tr><th>Select</th><th>Service</th><th>Purpose / Definition</th></tr></thead>
          <tbody>
            <?php
            $special_services = [
              ["Seasonal Renovation","End-of-season deep maintenance & restoration."],
              ["Match Preparation","Cutting, rolling, brushing, watering, prep."],
			        ["Line Marking","Professional pitch markings to regulation size."],
              ["Seasonal Grass Management","Overseeding for summer/winter transition."],
              ["Pest, Disease & Weed Monitoring","Inspection & early treatment."],
			        ["Aeration","Relieves compaction; improves drainage."],
              ["Fertilising","Nutrient boost for healthy turf."],
              ["Overseeding","Maintains turf density and recovery."],
              ["Top Dressing","Levels surface and improves soil texture."],
              ["Scarification","Removes thatch, moss, and debris."],
              ["Watering / Irrigation","Keeps turf hydrated in dry periods."]
              
             
              
            ];
            foreach($special_services as $i=>$srv){
                $id="special_{$i}";
                echo "<tr>
                        <td><input type='checkbox' name='services[]' id='{$id}' value='".htmlspecialchars($srv[0])."' class='form-check-input'></td>
                        <td class='text-start'><label for='{$id}' class='fw-semibold mb-0'>".htmlspecialchars($srv[0])."</label></td>
                        <td class='text-start'>".htmlspecialchars($srv[1])."</td>
                      </tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
      <p class="text-muted small mt-2 mb-0">Prices may vary based on field size, condition, and travel distance.</p>
    </div>

    <!-- Dates -->
    <div class="section-card">
      <h4 class="mb-2">Consultation Dates (up to 3)</h4>
      <input type="date" name="service_dates[]" class="form-control mb-2" min="<?=date('Y-m-d')?>">
      <input type="date" name="service_dates[]" class="form-control mb-2" min="<?=date('Y-m-d')?>">
      <input type="date" name="service_dates[]" class="form-control mb-2" min="<?=date('Y-m-d')?>">
      <small class="text-muted">Leave any unused dates empty.</small>
    </div>

    <!-- sticky summary -->
    <div class="sticky-summary d-flex flex-wrap justify-content-between align-items-center gap-3">
      <button type="submit" class="btn btn-success btn-lg btn-continue" id="continueBtn">
        <span class="mower">🚜</span> Continue
      </button>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(() => {
  const form = document.getElementById('serviceForm');

  // Select/Clear helpers
  function toggleGroup(prefix, checked){
    form.querySelectorAll('input[id^="'+prefix+'"]').forEach(cb=>cb.checked=checked);
  }
  document.getElementById('selectAllRoutine').onclick = ()=>toggleGroup('routine_', true);
  document.getElementById('clearRoutine').onclick = ()=>toggleGroup('routine_', false);
  document.getElementById('selectAllSpecial').onclick = ()=>toggleGroup('special_', true);
  document.getElementById('clearSpecial').onclick = ()=>toggleGroup('special_', false);
})();
</script>
