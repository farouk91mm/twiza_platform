<?php ob_start(); ?>

<div class="card content-card">
    <div class="card-header">
        <i class="bi bi-heart text-success me-2"></i>
        كل تبرعاتي
    </div>
    <div class="card-body p-0">

        <?php if (empty($donations)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-heart fs-1 d-block mb-3"></i>
                لا توجد تبرعات بعد
                <br>
                <a href="<?= APP_URL ?>/projects"
                   class="btn btn-success mt-3">
                    تصفح المشاريع
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>المشروع</th>
                            <th>الجمعية</th>
                            <th>المبلغ</th>
                            <th>طريقة الدفع</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($donations as $d): ?>
                        <?php
                        $badges = [
                            'pending'   => ['warning', 'بانتظار التأكيد'],
                            'confirmed' => ['success', 'مؤكد ✅'],
                            'rejected'  => ['danger',  'مرفوض'],
                        ];
                        $methods = [
                            'ccp'           => 'بريد الجزائر',
                            'bank_transfer' => 'تحويل بنكي',
                            'cash'          => 'نقداً',
                            'other'         => 'أخرى',
                        ];
                        [$color, $label] =
                            $badges[$d['status']] ?? ['secondary', ''];
                        ?>
                        <tr>
                            <td class="fw-semibold small">
                                <a href="<?= APP_URL ?>/projects/show?id=<?= $d['project_id'] ?>"
                                   class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($d['project_title']) ?>
                                </a>
                            </td>
                            <td class="small text-muted">
                                <?= htmlspecialchars($d['association_name']) ?>
                            </td>
                            <td class="fw-bold text-success">
                                <?= number_format($d['amount'], 0, ',', '.') ?> دج
                            </td>
                            <td class="small">
                                <?= $methods[$d['payment_method']] ?? $d['payment_method'] ?>
                            </td>
                            <td class="small">
                                <?= $d['donation_type'] === 'recurring' ? '🔄 شهري' : '1️⃣ مرة واحدة' ?>
                            </td>
                            <td>
    <?php if ($d['status'] === 'rejected'): ?>

        <!-- زر يفتح سبب الرفض -->
        <button type="button"
                class="btn btn-sm btn-danger"
                onclick="showRejectionReason(
                    '<?= htmlspecialchars(addslashes($d['project_title']), ENT_QUOTES) ?>',
                    '<?= htmlspecialchars(addslashes($d['notes'] ?? 'لم يُذكر سبب'), ENT_QUOTES) ?>'
                )">
            <i class="bi bi-x-circle me-1"></i> مرفوض — عرض السبب
        </button>

    <?php elseif ($d['status'] === 'confirmed'): ?>

        <span class="badge bg-success">
            <i class="bi bi-check-circle me-1"></i> مؤكد ✅
        </span>
        <?php if ($d['confirmed_at']): ?>
            <div class="text-muted" style="font-size:10px">
                <?= date('d/m/Y', strtotime($d['confirmed_at'])) ?>
            </div>
        <?php endif; ?>

    <?php else: ?>

        <span class="badge bg-warning text-dark">
            <i class="bi bi-clock me-1"></i> بانتظار التأكيد
        </span>

    <?php endif; ?>
</td>
                            <td class="small text-muted">
                                <?= date('d/m/Y', strtotime($d['created_at'])) ?>
                            </td>
                            <td>
    <?php if ($d['status'] === 'pending'): ?>
        <div class="d-flex gap-1">

            <!-- تعديل -->
            <a href="<?= APP_URL ?>/donations/edit?id=<?= $d['id'] ?>"
               class="btn btn-sm btn-outline-success"
               title="تعديل">
                <i class="bi bi-pencil"></i>
            </a>

            <!-- حذف -->
            <button type="button"
                    class="btn btn-sm btn-outline-danger"
                    title="حذف"
                    onclick="confirmDeleteDonation(
                        <?= $d['id'] ?>,
                        '<?= htmlspecialchars(
                            addslashes($d['project_title'])
                        ) ?>',
                        <?= $d['amount'] ?>
                    )">
                <i class="bi bi-trash"></i>
            </button>

        </div>
    <?php else: ?>
        <span class="text-muted small">—</span>
    <?php endif; ?>
</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>
<!-- Modal تأكيد حذف التبرع -->
<div class="modal fade" id="deleteDonationModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold text-danger">
                    <i class="bi bi-trash me-2"></i>
                    حذف التبرع
                </h6>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <i class="bi bi-exclamation-triangle-fill
                           text-warning fs-1 d-block mb-3"></i>
                <p class="mb-1 small">هل تريد حذف تبرعك في مشروع:</p>
                <p class="fw-bold small"
                   id="deleteDonationProject"></p>
                <p class="text-success fw-bold"
                   id="deleteDonationAmount"></p>
                <p class="text-muted small mb-0">
                    لا يمكن التراجع عن هذا الإجراء
                </p>
            </div>

            <form method="POST"
                  action="<?= APP_URL ?>/donations/delete">
                <input type="hidden"
                       name="csrf_token"
                       value="<?= Session::csrfToken() ?>">
                <input type="hidden"
                       name="donation_id"
                       id="deleteDonationId">

                <div class="modal-footer border-0 justify-content-center">
                    <button type="button"
                            class="btn btn-outline-secondary btn-sm px-3"
                            data-bs-dismiss="modal">
                        إلغاء
                    </button>
                    <button type="submit"
                            class="btn btn-danger btn-sm px-3">
                        <i class="bi bi-trash me-1"></i>
                        حذف
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
                    text-white bg-success border-0">
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
                    text-white bg-danger border-0">
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
function confirmDeleteDonation(id, project, amount) {
    document.getElementById('deleteDonationId').value = id;
    document.getElementById('deleteDonationProject').textContent =
        project;
    document.getElementById('deleteDonationAmount').textContent =
        new Intl.NumberFormat('ar-DZ').format(amount) + ' دج';

    new bootstrap.Modal(
        document.getElementById('deleteDonationModal')
    ).show();
}
</script>

<!-- Modal سبب الرفض -->
<div class="modal fade" id="rejectionModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold text-danger">
                    <i class="bi bi-x-circle me-2"></i> سبب الرفض
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border mb-3 py-2">
                    <small class="text-muted d-block">المشروع:</small>
                    <span class="fw-semibold small" id="rejectionProject"></span>
                </div>
                <div class="alert alert-danger py-2">
                    <small class="text-muted d-block mb-1">
                        <i class="bi bi-info-circle me-1"></i> سبب الرفض من الجمعية:
                    </small>
                    <p class="mb-0 small fw-semibold" id="rejectionReason"></p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
                <a href="<?= APP_URL ?>/projects" class="btn btn-success btn-sm">تبرع مجدداً</a>
            </div>
        </div>
    </div>
</div>

<script>
function showRejectionReason(project, reason) {
    document.getElementById('rejectionProject').textContent = project;
    document.getElementById('rejectionReason').textContent  = reason;
    new bootstrap.Modal(document.getElementById('rejectionModal')).show();
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/dashboard.php';
?>