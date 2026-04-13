<?php ob_start(); ?>

<!-- إحصائيات سريعة -->
<div class="row g-3 mb-4">

    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#1a8a5a,#0d5c3a)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number"><?= $stats['total_projects'] ?></div>
                    <div class="small opacity-75">إجمالي المشاريع</div>
                </div>
                <i class="bi bi-folder fs-2 opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#3498db,#2980b9)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number"><?= $stats['active_projects'] ?></div>
                    <div class="small opacity-75">مشاريع نشطة</div>
                </div>
                <i class="bi bi-activity fs-2 opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#e67e22,#d35400)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number"><?= $stats['total_donations'] ?></div>
                    <div class="small opacity-75">عدد التبرعات</div>
                </div>
                <i class="bi bi-people fs-2 opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#9b59b6,#8e44ad)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">
                        <?= number_format($stats['total_amount'], 0, ',', '.') ?>
                    </div>
                    <div class="small opacity-75">إجمالي المبالغ (دج)</div>
                </div>
                <i class="bi bi-cash fs-2 opacity-50"></i>
            </div>
        </div>
    </div>

</div>

<!-- قائمة المشاريع -->
<div class="card table-card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-folder text-success me-2"></i>
            مشاريعي الخيرية
        </h6>
        <a href="<?= APP_URL ?>/association/projects/add"
           class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle me-1"></i>
            إضافة مشروع
        </a>
    </div>
    <div class="card-body p-0">

        <?php if (empty($projects)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-folder-x fs-1 d-block mb-3"></i>
                لا توجد مشاريع بعد
                <br>
                <a href="<?= APP_URL ?>/association/projects/add"
                   class="btn btn-success mt-3">
                    أضف أول مشروع
                </a>
            </div>

        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>المشروع</th>
                            <th>الفئة</th>
                            <th>التقدم</th>
                            <th>المتبرعون</th>
                            <th>الحالة</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($projects as $project): ?>
                        <?php
                        $percent = $project['target_amount'] > 0
                            ? min(100, round(
                                ($project['collected_amount'] / $project['target_amount']) * 100
                              ))
                            : 0;
                        $statusLabels = [
                            'active'    => ['success', 'نشط'],
                            'completed' => ['primary', 'مكتمل'],
                            'paused'    => ['warning', 'موقوف'],
                            'cancelled' => ['danger',  'ملغى'],
                        ];
                        [$badgeColor, $badgeLabel] =
                            $statusLabels[$project['status']] ?? ['secondary', 'غير معروف'];
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if ($project['cover_image']): ?>
                                        <img src="<?= UPLOAD_URL . $project['cover_image'] ?>"
                                             width="45" height="45"
                                             class="rounded object-fit-cover"
                                             alt="">
                                    <?php else: ?>
                                        <div class="rounded d-flex align-items-center
                                                    justify-content-center text-white"
                                             style="width:45px;height:45px;
                                                    background:<?= $project['category_color'] ?? '#1a8a5a' ?>">
                                            <i class="bi <?= $project['category_icon'] ?? 'bi-heart' ?>"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-semibold small">
                                            <?= htmlspecialchars($project['title']) ?>
                                        </div>
                                        <div class="text-muted" style="font-size:11px">
                                            <?= number_format($project['target_amount'], 0, ',', '.') ?> دج
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill"
                                      style="background:<?= $project['category_color'] ?? '#1a8a5a' ?>">
                                    <?= htmlspecialchars($project['category_name'] ?? '') ?>
                                </span>
                            </td>
                            <td style="min-width:120px">
                                <div class="progress" style="height:8px">
                                    <div class="progress-bar bg-success"
                                         style="width:<?= $percent ?>%"></div>
                                </div>
                                <small class="text-muted"><?= $percent ?>%</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-people me-1"></i>
                                    <?= $project['donors_count'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $badgeColor ?>">
                                    <?= $badgeLabel ?>
                                </span>
                            </td>
                            <td>
    <div class="d-flex gap-1">

        <!-- عرض -->
        <a href="<?= APP_URL ?>/projects/show?id=<?= $project['id'] ?>"
           class="btn btn-sm btn-outline-secondary"
           title="عرض">
            <i class="bi bi-eye"></i>
        </a>

        <!-- تعديل -->
        <a href="<?= APP_URL ?>/association/projects/edit?id=<?= $project['id'] ?>"
           class="btn btn-sm btn-outline-success"
           title="تعديل">
            <i class="bi bi-pencil"></i>
        </a>

        <!-- حذف — فقط إذا لا توجد تبرعات -->
        <?php if ($project['donors_count'] == 0): ?>
            <button type="button"
                    class="btn btn-sm btn-outline-danger"
                    title="حذف"
                    onclick="confirmDelete(
                        <?= $project['id'] ?>,
                        '<?= htmlspecialchars(
                            addslashes($project['title'])
                        ) ?>'
                    )">
                <i class="bi bi-trash"></i>
            </button>
        <?php else: ?>
            <!-- زر معطل إذا له تبرعات -->
            <button type="button"
                    class="btn btn-sm btn-outline-danger"
                    title="لا يمكن الحذف — يوجد متبرعون"
                    disabled>
                <i class="bi bi-trash"></i>
            </button>
        <?php endif; ?>

    </div>
</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- Modal تأكيد الحذف -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold text-danger">
                    <i class="bi bi-trash me-2"></i>
                    حذف المشروع
                </h6>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <i class="bi bi-exclamation-triangle-fill
                           text-warning fs-1 d-block mb-3"></i>
                <p class="mb-1">هل أنت متأكد من حذف المشروع:</p>
                <p class="fw-bold" id="deleteProjectTitle"></p>
                <p class="text-muted small mb-0">
                    لا يمكن التراجع عن هذا الإجراء
                </p>
            </div>

            <form method="POST"
                  action="<?= APP_URL ?>/association/projects/delete">
                <input type="hidden"
                       name="csrf_token"
                       value="<?= Session::csrfToken() ?>">
                <input type="hidden"
                       name="project_id"
                       id="deleteProjectId">

                <div class="modal-footer border-0 justify-content-center">
                    <button type="button"
                            class="btn btn-outline-secondary btn-sm px-4"
                            data-bs-dismiss="modal">
                        إلغاء
                    </button>
                    <button type="submit"
                            class="btn btn-danger btn-sm px-4">
                        <i class="bi bi-trash me-1"></i>
                        حذف نهائياً
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- رسائل النجاح والخطأ -->
<?php if (Session::get('success')): ?>
    <div class="position-fixed bottom-0 end-0 p-3"
         style="z-index:9999">
        <div class="toast show align-items-center
                    text-white bg-success border-0"
             role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle me-2"></i>
                    <?= Session::get('success') ?>
                </div>
                <button type="button"
                        class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    <?php Session::set('success', null); ?>
<?php endif; ?>

<?php if (Session::get('error')): ?>
    <div class="position-fixed bottom-0 end-0 p-3"
         style="z-index:9999">
        <div class="toast show align-items-center
                    text-white bg-danger border-0"
             role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?= Session::get('error') ?>
                </div>
                <button type="button"
                        class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    <?php Session::set('error', null); ?>
<?php endif; ?>

<script>
function confirmDelete(projectId, projectTitle) {
    document.getElementById('deleteProjectId').value  = projectId;
    document.getElementById('deleteProjectTitle').textContent = projectTitle;
    new bootstrap.Modal(
        document.getElementById('deleteModal')
    ).show();
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/dashboard.php';
?>