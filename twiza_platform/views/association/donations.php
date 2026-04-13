<?php ob_start(); ?>

<!-- إحصائيات سريعة -->
<div class="row g-3 mb-4">

    <div class="col-6 col-md-3">
        <div class="stat-card"
             style="background:linear-gradient(135deg,#f39c12,#e67e22)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="number">
                        <?= count(array_filter($donations,
                            fn($d) => $d['status'] === 'pending')) ?>
                    </div>
                    <div class="label">بانتظار التأكيد</div>
                </div>
                <i class="bi bi-clock icon"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card"
             style="background:linear-gradient(135deg,#1a8a5a,#0d5c3a)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="number">
                        <?= count(array_filter($donations,
                            fn($d) => $d['status'] === 'confirmed')) ?>
                    </div>
                    <div class="label">تبرعات مؤكدة</div>
                </div>
                <i class="bi bi-check-circle icon"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card"
             style="background:linear-gradient(135deg,#3498db,#2980b9)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="number">
                        <?= count($donations) ?>
                    </div>
                    <div class="label">إجمالي التبرعات</div>
                </div>
                <i class="bi bi-list icon"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card"
             style="background:linear-gradient(135deg,#9b59b6,#8e44ad)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="number">
                        <?= number_format(
                            array_sum(array_map(
                                fn($d) => $d['status'] === 'confirmed'
                                          ? $d['amount'] : 0,
                                $donations
                            )), 0
                        ) ?>
                    </div>
                    <div class="label">إجمالي المبالغ (دج)</div>
                </div>
                <i class="bi bi-cash icon"></i>
            </div>
        </div>
    </div>

</div>

<!-- فلترة -->
<div class="card content-card mb-3">
    <div class="card-body p-3">
        <form method="GET"
              action="<?= APP_URL ?>/association/donations"
              class="row g-2 align-items-center">

            <div class="col-12 col-md-4">
                <select name="status" class="form-select form-select-sm">
                    <option value="">كل التبرعات</option>
                    <option value="pending"
                        <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>
                        بانتظار التأكيد
                    </option>
                    <option value="confirmed"
                        <?= ($_GET['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>
                        مؤكدة
                    </option>
                    <option value="rejected"
                        <?= ($_GET['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>
                        مرفوضة
                    </option>
                </select>
            </div>

            <div class="col-12 col-md-4">
                <select name="project_id" class="form-select form-select-sm">
                    <option value="">كل المشاريع</option>
                    <?php
                    // جمع المشاريع الفريدة من التبرعات
                    $uniqueProjects = [];
                    foreach ($donations as $d) {
                        if (!isset($uniqueProjects[$d['project_id']])) {
                            $uniqueProjects[$d['project_id']] = $d['project_title'];
                        }
                    }
                    foreach ($uniqueProjects as $pid => $ptitle):
                    ?>
                        <option value="<?= $pid ?>"
                            <?= ($_GET['project_id'] ?? '') == $pid ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ptitle) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-2">
                <button type="submit"
                        class="btn btn-success btn-sm w-100">
                    <i class="bi bi-funnel me-1"></i>
                    فلترة
                </button>
            </div>

            <div class="col-12 col-md-2">
                <a href="<?= APP_URL ?>/association/donations"
                   class="btn btn-outline-secondary btn-sm w-100">
                    إعادة تعيين
                </a>
            </div>

        </form>
    </div>
</div>

<!-- قائمة التبرعات -->
<div class="card content-card">
    <div class="card-header">
        <i class="bi bi-cash-stack text-success me-2"></i>
        قائمة التبرعات
    </div>
    <div class="card-body p-0">

        <?php if (empty($donations)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                لا توجد تبرعات بعد
            </div>

        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>المتبرع</th>
                            <th>المشروع</th>
                            <th>المبلغ</th>
                            <th>طريقة الدفع</th>
                            <th>إثبات التحويل</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                            <th>إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($donations as $d): ?>
                        <?php
                        // فلترة حسب الحالة
                        $filterStatus  = $_GET['status']     ?? '';
                        $filterProject = $_GET['project_id'] ?? '';

                        if ($filterStatus !== '' &&
                            $d['status'] !== $filterStatus) continue;

                        if ($filterProject !== '' &&
                            $d['project_id'] != $filterProject) continue;

                        $badges = [
                            'pending'   => ['warning', 'بانتظار التأكيد'],
                            'confirmed' => ['success', 'مؤكد'],
                            'rejected'  => ['danger',  'مرفوض'],
                        ];
                        $methods = [
                            'ccp'           => 'بريد الجزائر',
                            'bank_transfer' => 'تحويل بنكي',
                            'cash'          => 'نقداً',
                            'other'         => 'أخرى',
                        ];
                        [$badgeColor, $badgeLabel] =
                            $badges[$d['status']] ?? ['secondary', ''];
                        ?>
                        <tr>
                            <td class="text-muted"><?= $d['id'] ?></td>

                            <td>
                                <?php if ($d['is_anonymous']): ?>
                                    <span class="text-muted">
                                        <i class="bi bi-incognito me-1"></i>
                                        مجهول
                                    </span>
                                <?php else: ?>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($d['donor_name']) ?>
                                    </div>
                                    <div class="text-muted"
                                         style="font-size:11px">
                                        <?= htmlspecialchars($d['donor_email']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="<?= APP_URL ?>/projects/show?id=<?= $d['project_id'] ?>"
                                   class="text-decoration-none text-dark fw-semibold">
                                    <?= htmlspecialchars($d['project_title']) ?>
                                </a>
                                <?php if ($d['donation_type'] === 'recurring'): ?>
                                    <span class="badge bg-info ms-1"
                                          style="font-size:9px">
                                        شهري
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="fw-bold text-success">
                                <?= number_format($d['amount'], 0, ',', '.') ?>
                                دج
                            </td>

                            <td>
                                <?= $methods[$d['payment_method']]
                                    ?? $d['payment_method'] ?>
                            </td>

                            <!-- صورة إثبات التحويل -->
                            <td class="text-center">
                                <?php if ($d['proof_image']): ?>
                                    <a href="<?= UPLOAD_URL . $d['proof_image'] ?>"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-image me-1"></i>
                                        عرض
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">
                                        لا توجد صورة
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="text-muted">
                                <?= date('d/m/Y', strtotime($d['created_at'])) ?>
                                <br>
                                <span style="font-size:10px">
                                    <?= date('H:i', strtotime($d['created_at'])) ?>
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-<?= $badgeColor ?>">
                                    <?= $badgeLabel ?>
                                </span>
                                <?php if ($d['confirmed_at']): ?>
                                    <div class="text-muted"
                                         style="font-size:10px">
                                        <?= date('d/m/Y', strtotime($d['confirmed_at'])) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- أزرار الإجراء -->
                            <td>
                                <?php if ($d['status'] === 'pending'): ?>
                                    <div class="d-flex gap-1">

                                        <!-- زر التأكيد -->
                                        <button type="button"
                                                class="btn btn-sm btn-success"
                                                onclick="openConfirmModal(
                                                    <?= $d['id'] ?>,
                                                    '<?= htmlspecialchars(
                                                        addslashes($d['donor_name'])
                                                    ) ?>',
                                                    <?= $d['amount'] ?>
                                                )">
                                            <i class="bi bi-check-lg"></i>
                                        </button>

                                        <!-- زر الرفض -->
                                        <button type="button"
                                                class="btn btn-sm btn-danger"
                                                onclick="openRejectModal(
                                                    <?= $d['id'] ?>
                                                )">
                                            <i class="bi bi-x-lg"></i>
                                        </button>

                                    </div>

                                <?php elseif ($d['status'] === 'confirmed'): ?>

                                    <!-- صورة التأكيد إن وجدت -->
                                    <?php if ($d['confirmation_image']): ?>
                                        <a href="<?= UPLOAD_URL . $d['confirmation_image'] ?>"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-image me-1"></i>
                                            وصل
                                        </a>
                                    <?php else: ?>
                                        <span class="text-success small">
                                            <i class="bi bi-check-circle"></i>
                                            مؤكد
                                        </span>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <span class="text-danger small">
                                        <i class="bi bi-x-circle"></i>
                                        مرفوض
                                    </span>
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


<!-- ══════════════════════════════════
     Modal تأكيد التبرع
══════════════════════════════════ -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-check-circle text-success me-2"></i>
                    تأكيد استلام التبرع
                </h6>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <form method="POST"
                  action="<?= APP_URL ?>/association/donations/confirm"
                  enctype="multipart/form-data">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= Session::csrfToken() ?>">
                <input type="hidden"
                       name="donation_id"
                       id="confirmDonationId">
                <input type="hidden"
                       name="action"
                       value="confirm">

                <div class="modal-body">

                    <!-- معلومات التبرع -->
                    <div class="alert alert-success py-2 mb-3">
                        <div class="small">
                            <strong>المتبرع:</strong>
                            <span id="confirmDonorName"></span>
                        </div>
                        <div class="small">
                            <strong>المبلغ:</strong>
                            <span id="confirmAmount"
                                  class="text-success fw-bold"></span>
                            دج
                        </div>
                    </div>

                    <!-- صورة التأكيد -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            صورة وصل الاستلام
                            <span class="text-muted">(اختياري)</span>
                        </label>
                        <input type="file"
                               name="confirmation_image"
                               class="form-control form-control-sm"
                               accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">
                            ارفع صورة وصل أو إثبات الاستلام
                        </div>
                    </div>

                    <!-- ملاحظات -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            ملاحظات للمتبرع
                        </label>
                        <textarea name="notes"
                                  rows="2"
                                  class="form-control form-control-sm"
                                  placeholder="شكر أو تفاصيل إضافية..."></textarea>
                    </div>

                </div>

                <div class="modal-footer border-0">
                    <button type="button"
                            class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">
                        إلغاء
                    </button>
                    <button type="submit"
                            class="btn btn-success btn-sm px-4">
                        <i class="bi bi-check-circle me-1"></i>
                        تأكيد الاستلام
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════
     Modal رفض التبرع
══════════════════════════════════ -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-x-circle text-danger me-2"></i>
                    رفض التبرع
                </h6>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <form method="POST"
                  action="<?= APP_URL ?>/association/donations/confirm"
                  enctype="multipart/form-data">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= Session::csrfToken() ?>">
                <input type="hidden"
                       name="donation_id"
                       id="rejectDonationId">
                <input type="hidden"
                       name="action"
                       value="reject">

                <div class="modal-body">

                    <div class="alert alert-danger py-2 mb-3 small">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        سيتم إشعار المتبرع برفض تبرعه
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            سبب الرفض *
                        </label>
                        <textarea name="notes"
                                  rows="3"
                                  class="form-control form-control-sm"
                                  placeholder="اشرح سبب الرفض للمتبرع..."
                                  required></textarea>
                    </div>

                </div>

                <div class="modal-footer border-0">
                    <button type="button"
                            class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">
                        إلغاء
                    </button>
                    <button type="submit"
                            class="btn btn-danger btn-sm px-4">
                        <i class="bi bi-x-circle me-1"></i>
                        تأكيد الرفض
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>


<script>
// فتح modal التأكيد
function openConfirmModal(donationId, donorName, amount) {
    document.getElementById('confirmDonationId').value = donationId;
    document.getElementById('confirmDonorName').textContent =
        donorName || 'مجهول';
    document.getElementById('confirmAmount').textContent =
        new Intl.NumberFormat('ar-DZ').format(amount);

    new bootstrap.Modal(
        document.getElementById('confirmModal')
    ).show();
}

// فتح modal الرفض
function openRejectModal(donationId) {
    document.getElementById('rejectDonationId').value = donationId;

    new bootstrap.Modal(
        document.getElementById('rejectModal')
    ).show();
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/dashboard.php';
?>