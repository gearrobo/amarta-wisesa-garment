<?php
// progress_pekerja.php

include 'config/db.php';


// --- Ambil data produksi ---
// Ambil semua pekerja, target, hasil (bisa disesuaikan WHERE status='proses')
$sql_worker = "SELECT pekerja, target, hasil FROM produksi";
$result_worker = $conn->query($sql_worker);

$workers = [];
if ($result_worker && $result_worker->num_rows > 0) {
    while ($row_worker = $result_worker->fetch_assoc()) {
        $workers[] = $row_worker;
    }
}
$conn->close();

// --- Helper fungsi ---
function pct($c, $t) {
    if ($t <= 0) return 0;
    $p = ($c / $t) * 100;
    return $p > 100 ? 100 : round($p, 1);
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Progress Pekerja Garment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .worker-card { margin-bottom: 12px; }
    .progress { height: 28px; border-radius: 14px; overflow: hidden; }
    .progress .progress-bar { line-height: 28px; font-weight: 600; }
    .meta { font-size: 0.9rem; color: #555; }
  </style>
</head>
<body class="bg-light">
  <div class="container py-4">
    <h3 class="mb-4">Progres Pekerja Garment</h3>

    <div class="row">
      <?php foreach ($workers as $w): 
        $p = pct($w['hasil'], $w['target']);
        if ($p >= 90) $color = 'bg-success';
        elseif ($p >= 60) $color = 'bg-info';
        elseif ($p >= 30) $color = 'bg-warning';
        else $color = 'bg-danger';
      ?>
        <div class="col-md-6 worker-card">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <div><strong><?= htmlspecialchars($w['pekerja']) ?></strong></div>
                <div class="meta"><?= $w['hasil'] ?> / <?= $w['target'] ?> tugas</div>
              </div>

              <div class="progress" aria-label="<?= htmlspecialchars($w['pekerja']) ?> progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated <?= $color ?>"
                     role="progressbar" style="width:0%;" aria-valuemin="0" aria-valuemax="100"
                     data-target="<?= $p ?>">
                  <?= $p ?>%
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // animasi naik dari 0 ke target
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.progress-bar[data-target]').forEach(bar => {
        const target = parseFloat(bar.dataset.target) || 0;
        const duration = 800;
        const start = performance.now();
        function animate(now) {
          const t = Math.min(1, (now - start) / duration);
          const ease = 1 - Math.pow(1 - t, 3);
          const value = Math.round(ease * target * 10) / 10;
          bar.style.width = value + '%';
          bar.textContent = value + '%';
          if (t < 1) requestAnimationFrame(animate);
          else {
            bar.style.width = target + '%';
            bar.textContent = target + '%';
          }
        }
        requestAnimationFrame(animate);
      });
    });
  </script>
</body>
</html>
