<?php ob_start(); ?>

<?php
$percent = $group['target_amount'] > 0
    ? min(100, round(
        ($group['collected_amount'] / $group['target_amount']) * 100
      ))
    : 0;

$statusLabels = [
    'pending'   => ['warning', 'بانتظار موافقة الجمعية'],
    'active'    => ['success', 'نشطة'],
    'completed' => ['primary', 'مكتملة'],
    'cancelled' => ['danger',  'ملغاة'],
];
[$statusColor, $statusLabel] =
    $statusLabels[$group['status']] ?? ['secondary', ''];
?>

<!-- رسائل -->
<?php if (Session::get('success')): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>
        <?= Session::get('success') ?>
    </div>
    <?php Session::set('success', null); ?>
<?php endif; ?>

<?php if (Session::get('error')): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle me-2"></i>
        <?= Session::get('error') ?>
    </div>
    <?php Session::set('error', null); ?>
<?php endif; ?>

<div class="row g-4">

    <!-- العمود الرئيسي -->
    <div class="col-12 col-lg-8">

        <!-- معلومات المجموعة -->
        <div class="card content-card mb-4">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between
                            align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <?= htmlspecialchars($group['name']) ?>
                        </h5>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-person me-1"></i>
                            أنشأها:
                            <?= htmlspecialchars($group['creator_name']) ?>
                        </p>
                    </div>
                    <span class="badge bg-<?= $statusColor ?>">
                        <?= $statusLabel ?>
                    </span>
                </div>

                <?php if ($group['description']): ?>
                    <p class="text-muted small mb-3">
                        <?= htmlspecialchars($group['description']) ?>
                    </p>
                <?php endif; ?>

                <!-- المشروع -->
                <div class="alert alert-light border mb-3 py-2">
                    <small class="text-muted d-block">المشروع الخيري</small>
                    <a href="<?= APP_URL ?>/projects/show?id=<?= $group['project_id'] ?>"
                       class="fw-semibold text-success text-decoration-none">
                        <i class="bi bi-folder me-1"></i>
                        <?= htmlspecialchars($group['project_title']) ?>
                    </a>
                    <small class="text-muted d-block">
                        <?= htmlspecialchars($group['association_name']) ?>
                    </small>
                </div>

                <!-- التقدم -->
                <div class="d-flex justify-content-between mb-1">
                    <span class="small text-muted">
                        <?= number_format($group['collected_amount'], 0, ',', '.') ?>
                        دج
                    </span>
                    <span class="small fw-bold text-success">
                        <?= $percent ?>%
                    </span>
                </div>
                <div class="progress mb-1" style="height:8px">
                    <div class="progress-bar bg-success"
                         style="width:<?= $percent ?>%"></div>
                </div>
                <div class="text-muted small mb-0">
                    الهدف:
                    <?= number_format($group['target_amount'], 0, ',', '.') ?>
                    دج
                </div>

            </div>
        </div>

        <!-- كود الدعوة — للمشرف فقط -->
        <?php if ($isAdmin && $group['status'] === 'active'): ?>
            <div class="card content-card mb-4">
                <div class="card-header">
                    <i class="bi bi-share text-success me-2"></i>
                    شارك كود الدعوة
                </div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fw-bold fs-3 text-success
                                    letter-spacing-4 font-monospace"
                             id="inviteCode"
                             style="letter-spacing:6px">
                            <?= $group['invite_code'] ?>
                        </div>
                        <button class="btn btn-outline-success btn-sm"
                                onclick="copyCode()">
                            <i class="bi bi-clipboard me-1"></i>
                            نسخ
                        </button>
                    </div>
                    <div class="text-muted small mt-2">
                        شارك هذا الكود مع أصدقائك للانضمام عبر:
                        <a href="<?= APP_URL ?>/groups/join">
                            <?= APP_URL ?>/groups/join
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- أعضاء المجموعة -->
        <div class="card content-card mb-4">
            <div class="card-header d-flex justify-content-between">
                <span>
                    <i class="bi bi-people text-success me-2"></i>
                    الأعضاء
                    <span class="badge bg-success ms-1">
                        <?= count($members) ?>
                    </span>
                </span>
                <?php if (!$isMember &&
                          $group['status'] === 'active'): ?>
                    <a href="<?= APP_URL ?>/groups/join?code=<?= $group['invite_code'] ?>"
                       class="btn btn-sm btn-success">
                        <i class="bi bi-person-plus me-1"></i>
                        انضم
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>العضو</th>
                                <th>الدور</th>
                                <th>الحصة الملتزم بها</th>
                                <th>المدفوع</th>
                                <th>تاريخ الانضمام</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($members as $m): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-success
                                                    text-white d-flex align-items-center
                                                    justify-content-center"
                                             style="width:32px;height:32px;
                                                    font-size:0.8rem;font-weight:700">
                                            <?= mb_substr($m['full_name'], 0, 1) ?>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($m['full_name']) ?>
                                            </div>
                                            <?php if ($m['user_id'] == $userId): ?>
                                                <small class="text-success">أنت</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($m['role'] === 'admin'): ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-star me-1"></i>
                                            مشرف
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark">
                                            عضو
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-success fw-semibold">
                                    <?php if ($m['pledged_amount']): ?>
                                        <?= number_format($m['pledged_amount'], 0, ',', '.') ?>
                                        دج
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($m['paid_amount'] > 0): ?>
                                        <span class="text-success">
                                            <?= number_format($m['paid_amount'], 0, ',', '.') ?>
                                            دج
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">0 دج</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted">
                                    <?= date('d/m/Y', strtotime($m['joined_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- سجل تبرعات المجموعة -->
        <?php if (!empty($donations)): ?>
            <div class="card content-card">
                <div class="card-header">
                    <i class="bi bi-cash-stack text-success me-2"></i>
                    سجل التبرعات
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>المبلغ</th>
                                    <th>طريقة الدفع</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                    <th>إثبات</th>
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
                                [$bc, $bl] = $badges[$d['status']]
                                             ?? ['secondary', ''];
                                ?>
                                <tr>
                                    <td class="fw-bold text-success">
                                        <?= number_format($d['total_amount'], 0, ',', '.') ?>
                                        دج
                                    </td>
                                    <td>
                                        <?= $methods[$d['payment_method']]
                                            ?? $d['payment_method'] ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $bc ?>">
                                            <?= $bl ?>
                                        </span>
                                    </td>
                                    <td class="text-muted">
                                        <?= date('d/m/Y', strtotime($d['created_at'])) ?>
                                    </td>
                                    <td>
                                        <?php if ($d['proof_image']): ?>
                                            <a href="<?= UPLOAD_URL . $d['proof_image'] ?>"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-image"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- العمود الجانبي -->
    <div class="col-12 col-lg-4">

        <!-- تسجيل تبرع المجموعة — للمشرف فقط -->
        <?php if ($isAdmin &&
                  $group['status'] === 'active'): ?>
            <div class="card content-card mb-3">
                <div class="card-header">
                    <i class="bi bi-cash text-success me-2"></i>
                    تسجيل تبرع المجموعة
                </div>
                <div class="card-body p-3">

                    <div class="alert alert-info small py-2 mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        بعد تجميع المبلغ من الأعضاء
                        وتحويله للجمعية — سجّله هنا
                    </div>

                    <!-- معلومات التحويل -->
                    <?php if ($group['association_bank']): ?>
                        <div class="small mb-2">
                            <i class="bi bi-bank text-success me-1"></i>
                            <strong>حساب بنكي:</strong>
                            <code><?= htmlspecialchars($group['association_bank']) ?></code>
                        </div>
                    <?php endif; ?>

                    <?php if ($group['association_ccp']): ?>
                        <div class="small mb-3">
                            <i class="bi bi-envelope text-success me-1"></i>
                            <strong>CCP:</strong>
                            <code><?= htmlspecialchars($group['association_ccp']) ?></code>
                        </div>
                    <?php endif; ?>

                    <form method="POST"
                          action="<?= APP_URL ?>/groups/donate"
                          enctype="multipart/form-data">

                        <input type="hidden"
                               name="csrf_token"
                               value="<?= Session::csrfToken() ?>">
                        <input type="hidden"
                               name="group_id"
                               value="<?= $group['id'] ?>">

                        <!-- المبلغ -->
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">
                                المبلغ الإجمالي (دج) *
                            </label>
                            <input type="number"
                                   name="total_amount"
                                   class="form-control form-control-sm"
                                   min="1"
                                   placeholder="المبلغ المُحوَّل للجمعية">
                        </div>

                        <!-- طريقة الدفع -->
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">
                                طريقة الدفع *
                            </label>
                            <select name="payment_method"
                                    class="form-select form-select-sm">
                                <option value="ccp">بريد الجزائر CCP</option>
                                <option value="bank_transfer">تحويل بنكي</option>
                                <option value="cash">نقداً في مقر الجمعية</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>

                        <!-- صورة الإثبات -->
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">
                                صورة الإثبات
                            </label>
                            <input type="file"
                                   name="proof_image"
                                   class="form-control form-control-sm"
                                   accept="image/jpeg,image/png,image/webp">
                        </div>

                        <!-- ملاحظات -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">
                                ملاحظات
                            </label>
                            <textarea name="notes"
                                      rows="2"
                                      class="form-control form-control-sm"
                                      placeholder="اختياري"></textarea>
                        </div>

                        <button type="submit"
                                class="btn btn-success w-100 btn-sm">
                            <i class="bi bi-check-circle me-1"></i>
                            تسجيل التبرع
                        </button>

                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- حالة المجموعة -->
        <?php if ($group['status'] === 'pending'): ?>
            <div class="card content-card border-warning">
                <div class="card-body p-3 text-center">
                    <i class="bi bi-clock-history fs-1 text-warning d-block mb-2"></i>
                    <h6 class="fw-bold">بانتظار الموافقة</h6>
                    <p class="text-muted small mb-0">
                        المجموعة بانتظار موافقة الجمعية
                        على قبولها — ستصلك إشعار عند الموافقة
                    </p>
                </div>
            </div>
        <?php endif; ?>

    </div>

</div>

<script>
function copyCode() {
    const code = document.getElementById('inviteCode').textContent.trim();
    navigator.clipboard.writeText(code).then(() => {
        alert('تم نسخ الكود: ' + code);
    });
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/dashboard.php';
?>