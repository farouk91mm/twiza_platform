<?php ob_start(); ?>

<!-- إحصائيات -->
<div class="row g-3 mb-4">

    <div class="col-6 col-md-3">
        <div class="stat-card"
             style="background:linear-gradient(135deg,#1a8a5a,#0d5c3a)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="number">
                        <?= $stats['total_donations'] ?? 0 ?>
                    </div>
                    <div class="label">إجمالي التبرعات</div>
                </div>
                <i class="bi bi-heart icon"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card"
             style="background:linear-gradient(135deg,#3498db,#2980b9)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="number">
                        <?= number_format($stats['total_amount'] ?? 0, 0) ?>
                    </div>
                    <div class="label">إجمالي المبالغ (دج)</div>
                </div>
                <i class="bi bi-cash icon"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card"
             style="background:linear-gradient(135deg,#2ecc71,#27ae60)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="number">
                        <?= $stats['confirmed_donations'] ?? 0 ?>
                    </div>
                    <div class="label">تبرعات مؤكدة</div>
                </div>
                <i class="bi bi-check-circle icon"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card"
             style="background:linear-gradient(135deg,#f39c12,#e67e22)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="number">
                        <?= $stats['pending_donations'] ?? 0 ?>
                    </div>
                    <div class="label">بانتظار التأكيد</div>
                </div>
                <i class="bi bi-clock icon"></i>
            </div>
        </div>
    </div>

</div>

<!-- أحدث المشاريع -->
<div class="card content-card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-grid text-success me-2"></i>
            أحدث المشاريع الخيرية
        </span>
        <a href="<?= APP_URL ?>/projects"
           class="btn btn-sm btn-outline-success">
            عرض الكل
        </a>
    </div>
    <div class="card-body p-3">

        <?php if (empty($latestProjects)): ?>
            <div class="text-center py-4 text-muted">
                <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                لا توجد مشاريع نشطة حالياً
            </div>
        <?php else: ?>

            <div class="row g-3">
                <?php foreach ($latestProjects as $project): ?>
                    <?php
                    $percent = $project['target_amount'] > 0
                        ? min(100, round(
                            ($project['collected_amount'] /
                             $project['target_amount']) * 100
                          ))
                        : 0;
                    ?>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm"
                             style="border-radius:12px;overflow:hidden">

                            <!-- صورة المشروع -->
<!-- صورة المشروع -->
<?php if ($project['cover_image']): ?>
    <div style="position:relative;
                width:100%;
                padding-top:56.25%;
                overflow:hidden;
                background:#f0f0f0">
        <img src="<?= UPLOAD_URL . $project['cover_image'] ?>"
             alt="<?= htmlspecialchars($project['title']) ?>"
             style="position:absolute;
                    top:0; left:0;
                    width:100%;
                    height:100%;
                    object-fit:contain;
                    object-position:center;
                    transition:transform 0.4s ease"
             onmouseover="this.style.transform='scale(1.08)'"
             onmouseout="this.style.transform='scale(1)'">
    </div>

<?php else: ?>
    <div class="d-flex align-items-center justify-content-center"
         style="padding-top:56.25%;
                position:relative;
                background:<?= $project['category_color'] ?? '#1a8a5a' ?>20">
        <i class="bi <?= $project['category_icon'] ?? 'bi-heart' ?> fs-1
                   position-absolute top-50 start-50 translate-middle"
           style="color:<?= $project['category_color'] ?? '#1a8a5a' ?>;
                  transition:transform 0.4s ease"
             onmouseover="this.style.transform='translate(-50%,-50%) scale(1.2)'"
             onmouseout="this.style.transform='translate(-50%,-50%) scale(1)'">
        </i>
    </div>
<?php endif; ?>

                            <div class="card-body p-3">

                                <!-- الفئة -->
                                <span class="badge rounded-pill mb-2"
                                      style="background:<?= $project['category_color'] ?? '#1a8a5a' ?>;
                                             font-size:11px">
                                    <i class="bi <?= $project['category_icon'] ?? 'bi-heart' ?> me-1"></i>
                                    <?= htmlspecialchars($project['category_name'] ?? '') ?>
                                </span>

                                <!-- العنوان -->
                                <h6 class="fw-bold mb-1"
                                    style="font-size:0.9rem;
                                           display:-webkit-box;
                                           -webkit-line-clamp:1;
                                           -webkit-box-orient:vertical;
                                           overflow:hidden">
                                    <?= htmlspecialchars($project['title']) ?>
                                </h6>

                                <!-- الجمعية -->
                                <p class="text-muted mb-2"
                                   style="font-size:11px">
                                    <i class="bi bi-building me-1"></i>
                                    <?= htmlspecialchars(
                                        $project['association_name'] ?? ''
                                    ) ?>
                                </p>

                                <!-- شريط التقدم -->
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted"
                                           style="font-size:11px">
                                        <?= number_format(
                                            $project['collected_amount'],
                                            0, ',', '.'
                                        ) ?> دج
                                    </small>
                                    <small class="fw-bold text-success"
                                           style="font-size:11px">
                                        <?= $percent ?>%
                                    </small>
                                </div>

                                <div class="progress mb-2"
                                     style="height:5px">
                                    <div class="progress-bar bg-success"
                                         style="width:<?= $percent ?>%">
                                    </div>
                                </div>

                                <!-- معلومات إضافية -->
                                <div class="d-flex justify-content-between
                                            align-items-center mb-3">
                                    <small class="text-muted"
                                           style="font-size:11px">
                                        <i class="bi bi-people me-1"></i>
                                        <?= $project['donors_count'] ?>
                                        متبرع
                                    </small>
                                    <small class="text-muted"
                                           style="font-size:11px">
                                        <?= number_format(
                                            $project['target_amount'],
                                            0, ',', '.'
                                        ) ?> دج
                                    </small>
                                </div>

                                <!-- زر التبرع -->
                                <a href="<?= APP_URL ?>/projects/show?id=<?= $project['id'] ?>"
                                   class="btn btn-success w-100 btn-sm rounded-pill">
                                    <i class="bi bi-heart me-1"></i>
                                    تبرع الآن
                                </a>

                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>
</div>

<!-- آخر تبرعاتي -->
<div class="card content-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-clock-history text-success me-2"></i>
            آخر تبرعاتي
        </span>
        <a href="<?= APP_URL ?>/individual/donations"
           class="btn btn-sm btn-outline-success">
            عرض الكل
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($donations)): ?>
            <div class="text-center py-4 text-muted">
                <i class="bi bi-heart fs-1 d-block mb-2"></i>
                لم تتبرع بعد
                <br>
                <a href="<?= APP_URL ?>/projects"
                   class="btn btn-success btn-sm mt-2">
                    ابدأ الآن
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>المشروع</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach (array_slice($donations, 0, 5) as $d): ?>
                        <?php
                        $badges = [
                            'pending'   => ['warning', 'بانتظار التأكيد'],
                            'confirmed' => ['success', 'مؤكد ✅'],
                            'rejected'  => ['danger',  'مرفوض'],
                        ];
                        [$color, $label] = $badges[$d['status']] ?? ['secondary', ''];
                        ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($d['project_title']) ?>
                            </td>
                            <td class="fw-bold text-success">
                                <?= number_format($d['amount'], 0, ',', '.') ?>
                                دج
                            </td>
                            <td>
                                <span class="badge bg-<?= $color ?>">
                                    <?= $label ?>
                                </span>
                            </td>
                            <td class="text-muted">
                                <?= date('d/m/Y', strtotime($d['created_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/dashboard.php';
?>