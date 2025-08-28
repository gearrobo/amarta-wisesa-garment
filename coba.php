<?php
// progress_pekerja.php
// Contoh data: bisa diganti ambil dari MySQL
$workers = [
    ['name' => 'Asep',   'completed' => 42, 'target' => 50],
    ['name' => 'Siti',   'completed' => 30, 'target' => 40],
    ['name' => 'Budi',   'completed' => 15, 'target' => 30],
    ['name' => 'Rina',   'completed' => 25, 'target' => 25],
    ['name' => 'Joko',   'completed' => 5,  'target' => 20],
];

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

  <!-- Bootstrap 5 CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* sedikit penyesuaian visual */
    .worker-card { margin-bottom: 12px; }
    .progress { height: 28px; border-radius: 14px; overflow: hidden; }
    .progress .progress-bar { line-height: 28px; font-weight: 600; }
    .meta { font-size: 0.9rem; color: #555; }
  </style>
</head>
<body class="bg-light">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0">Progres Pekerja Garment</h3>
      <small class="text-muted">Update otomatis saat halaman dibuka</small>
    </div>

    <!-- Summary -->
    <?php
      $totalCompleted = array_sum(array_column($workers,'completed'));
      $totalTarget = array_sum(array_column($workers,'target'));
      $totalPct = pct($totalCompleted, $totalTarget);
    ?>
    <div class="card mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div>
            <strong>Total Progress</strong>
            <div class="meta">Selesai: <?= $totalCompleted ?> / Target: <?= $totalTarget ?></div>
          </div>
          <div><span class="badge bg-primary fs-6"><?= $totalPct ?>%</span></div>
        </div>

        <div class="progress" aria-label="Total progress">
          <div id="totalProgressBar" class="progress-bar progress-bar-striped progress-bar-animated"
               role="progressbar" style="width: 0%;" aria-valuemin="0" aria-valuemax="100"
               data-target="<?= $totalPct ?>">
            <?= $totalPct ?>%
          </div>
        </div>
      </div>
    </div>

    <!-- Per-worker -->
    <div class="row">
      <?php foreach ($workers as $w): 
        $p = pct($w['completed'], $w['target']);
        // pilih warna berdasarkan persentase
        if ($p >= 90) $color = 'bg-success';
        elseif ($p >= 60) $color = 'bg-info';
        elseif ($p >= 30) $color = 'bg-warning';
        else $color = 'bg-danger';
      ?>
        <div class="col-md-6 worker-card">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <div><strong><?= htmlspecialchars($w['name']) ?></strong></div>
                <div class="meta"><?= $w['completed'] ?> / <?= $w['target'] ?> tugas</div>
              </div>

              <div class="progress" aria-label="<?= htmlspecialchars($w['name']) ?> progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated <?= $color ?>"
                     role="progressbar" style="width: 0%;" aria-valuemin="0" aria-valuemax="100"
                     data-target="<?= $p ?>">
                  <?= $p ?>%
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-end mt-3">
      <small class="text-muted">Tip: ganti data PHP dengan query MySQL untuk data real-time.</small>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle (Popper included) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Simple animation: naikkan width dari 0 ke data-target dalam 800ms
    document.addEventListener('DOMContentLoaded', () => {
      const bars = document.querySelectorAll('.progress-bar[data-target]');
      bars.forEach(bar => {
        const target = parseFloat(bar.dataset.target) || 0;
        // animasi progres smooth
        const duration = 800; // ms
        const fps = 60;
        const steps = Math.round(duration / (1000 / fps));
        let step = 0;

        const start = performance.now();
        function animate(now) {
          const t = Math.min(1, (now - start) / duration);
          // easing (easeOutCubic)
          const ease = 1 - Math.pow(1 - t, 3);
          const value = Math.round(ease * target * 10) / 10;
          bar.style.width = value + '%';
          bar.textContent = value + '%';
          if (t < 1) requestAnimationFrame(animate);
          else {
            // pastikan final
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
