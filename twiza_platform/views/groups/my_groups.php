<?php ob_start(); ?>

<!-- رسائل -->
<?php if (Session::get('success')): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>
        <?= Session::get('success') ?>
    </div>
    <?php Session::set('success', null); ?>
<?php endif; ?>

<!-- أزرار الإجراءات -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="fw-bold mb-0">
        <i class="bi bi-people text-success me-2"></i>
        مجموعاتي الخيرية
    </h6>
    <div class="d-flex gap-2">
        <a href="<?= APP_URL ?>/groups/join"
           class="btn btn-outline-success btn-sm">
            <i class="bi bi-person-plus me-1"></i>
            انضم لمجموعة
        </a>
    </div>
</div>

<?php if (empty($groups)): ?>

    <!-- لا توجد مجموعات -->
    <div class="card content-card">
        <div class="card-body text-center py-5">
            <i class="bi bi-people fs-1 text-muted d-block mb-3"></i>
            <h6 class="text-muted">لا توجد مجموعات بعد</h6>
            <p class="text-muted small mb-4">
                أنشئ مجموعة مع أصدقائك أو انضم لمجموعة موجودة
            </p>
            <div class="d-flex gap-2 justify-content-center">
                <a href="<?= APP_URL ?>/projects"
                   class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>
                    إنشاء مجموعة جديدة
                </a>
                <a href="<?= APP_URL ?>/groups/join"
                   class="btn btn-outline-success btn-sm">
                    <i class="bi bi-person-plus me-1"></i>
                    انضم لمجموعة
                </a>
            </div>
        </div>
    </div>

<?php else: ?>

    <div class="row g-3">
        <?php foreach ($groups as $g): ?>
            <?php
            $percent = $g['target_amount'] > 0
                ? min(100, round(
                    ($g['collected_amount'] /
                     $g['target_amount']) * 100
                  ))
                : 0;

            $statusLabels = [
                'pending'   => ['warning', 'بانتظار الموافقة'],
                'active'    => ['success', 'نشطة'],
                'completed' => ['primary', 'مكتملة'],
                'cancelled' => ['danger',  'ملغاة'],
            ];
            [$sc, $sl] = $statusLabels[$g['status']]
                         ?? ['secondary', ''];
            ?>

            <div class="col-12 col-md-6">
                <div class="card content-card h-100">
                    <div class="card-body p-3">

                        <!-- Header -->
                        <div class="d-flex justify-content-between
                                    align-items-start mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-success
                                            text-white d-flex align-items-center
                                            justify-content-center"
                                     style="width:42px;height:42px;
                                            font-size:1.1rem">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small">
                                        <?= htmlspecialchars($g['name']) ?>
                                    </div>
                                    <div class="text-muted"
                                         style="font-size:11px">
                                        <?php if ($g['my_role'] === 'admin'): ?>
                                            <span class="badge bg-success"
                                                  style="font-size:9px">
                                                <i class="bi bi-star me-1"></i>
                                                مشرف
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark"
                                                  style="font-size:9px">
                                                عضو
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <span class="badge bg-<?= $sc ?>">
                                <?= $sl ?>
                            </span>
                        </div>

                        <!-- المشروع -->
                        <div class="small text-muted mb-2">
                            <i class="bi bi-folder text-success me-1"></i>
                            <?= htmlspecialchars($g['project_title']) ?>
                        </div>

                        <!-- الجمعية -->
                        <div class="small text-muted mb-3">
                            <i class="bi bi-building text-success me-1"></i>
                            <?= htmlspecialchars($g['association_name']) ?>
                        </div>

                        <!-- التقدم -->
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted" style="font-size:11px">
                                <?= number_format($g['collected_amount'], 0, ',', '.') ?>
                                دج
                            </small>
                            <small class="fw-bold text-success"
                                   style="font-size:11px">
                                <?= $percent ?>%
                            </small>
                        </div>
                        <div class="progress mb-2" style="height:5px">
                            <div class="progress-bar bg-success"
                                 style="width:<?= $percent ?>%"></div>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <small class="text-muted" style="font-size:11px">
                                <i class="bi bi-people me-1"></i>
                                <?= $g['members_count'] ?> أعضاء
                            </small>
                            <small class="text-muted" style="font-size:11px">
                                الهدف:
                                <?= number_format($g['target_amount'], 0, ',', '.') ?>
                                دج
                            </small>
                        </div>

                        <!-- حصتي -->
                        <?php if ($g['pledged_amount']): ?>
                            <div class="alert alert-light py-1 px-2 mb-3"
                                 style="font-size:11px">
                                <strong>حصتي:</strong>
                                <?= number_format($g['pledged_amount'], 0, ',', '.') ?>
                                دج
                                |
                                <strong>المدفوع:</strong>
                                <span class="text-success">
                                    <?= number_format($g['paid_amount'], 0, ',', '.') ?>
                                    دج
                                </span>
                            </div>
                        <?php endif; ?>

                        <!-- أزرار -->
                        <div class="d-flex gap-2">
                            <a href="<?= APP_URL ?>/groups/show?id=<?= $g['id'] ?>"
                               class="btn btn-success btn-sm flex-grow-1">
                                <i class="bi bi-eye me-1"></i>
                                عرض المجموعة
                            </a>
                            <?php if ($g['status'] === 'active' &&
                                      $g['my_role'] === 'admin'): ?>
                                <button type="button"
                                        class="btn btn-outline-success btn-sm"
                                        onclick="copyCode('<?= $g['invite_code'] ?>')"
                                        title="نسخ كود الدعوة">
                                    <i class="bi bi-share"></i>
                                </button>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    </div>

<?php endif; ?>

<script>
function copyCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        alert('تم نسخ كود الدعوة: ' + code);
    });
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/dashboard.php';
?>