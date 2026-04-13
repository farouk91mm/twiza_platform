<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-12 col-md-6">
        <div class="card content-card">
            <div class="card-header">
                <i class="bi bi-person-plus text-success me-2"></i>
                الانضمام لمجموعة خيرية
            </div>
            <div class="card-body p-4">

                <!-- نموذج البحث بالكود -->
                <form method="GET"
                      action="<?= APP_URL ?>/groups/join"
                      class="mb-4">
                    <label class="form-label fw-semibold">
                        أدخل كود الدعوة
                    </label>
                    <div class="input-group">
                        <input type="text"
                               name="code"
                               value="<?= htmlspecialchars($code) ?>"
                               class="form-control text-center fw-bold"
                               placeholder="XXXXXXXX"
                               maxlength="8"
                               style="letter-spacing:4px;font-size:1.2rem"
                               dir="ltr">
                        <button type="submit"
                                class="btn btn-success">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['invite_code'])): ?>
                        <div class="text-danger small mt-1">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            <?= $errors['invite_code'] ?>
                        </div>
                    <?php endif; ?>
                </form>

                <!-- معلومات المجموعة إن وُجدت -->
                <?php if ($group): ?>
                    <div class="card border-success mb-4">
                        <div class="card-body p-3">

                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="rounded-circle bg-success d-flex
                                            align-items-center justify-content-center"
                                     style="width:45px;height:45px">
                                    <i class="bi bi-people-fill text-white"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">
                                        <?= htmlspecialchars($group['name']) ?>
                                    </div>
                                    <div class="text-muted small">
                                        بواسطة:
                                        <?= htmlspecialchars($group['creator_name']) ?>
                                    </div>
                                </div>
                            </div>

                            <div class="small mb-2">
                                <i class="bi bi-folder text-success me-1"></i>
                                <strong>المشروع:</strong>
                                <?= htmlspecialchars($group['project_title']) ?>
                            </div>

                            <div class="small mb-2">
                                <i class="bi bi-building text-success me-1"></i>
                                <strong>الجمعية:</strong>
                                <?= htmlspecialchars($group['association_name']) ?>
                            </div>

                            <div class="small mb-3">
                                <i class="bi bi-cash text-success me-1"></i>
                                <strong>الهدف:</strong>
                                <?= number_format($group['target_amount'], 0, ',', '.') ?>
                                دج
                                <span class="text-muted">
                                    (تم جمع
                                    <?= number_format($group['collected_amount'], 0, ',', '.') ?>
                                    دج)
                                </span>
                            </div>

                            <!-- شريط التقدم -->
                            <?php
                            $percent = $group['target_amount'] > 0
                                ? min(100, round(
                                    ($group['collected_amount'] /
                                     $group['target_amount']) * 100
                                  ))
                                : 0;
                            ?>
                            <div class="progress mb-3" style="height:6px">
                                <div class="progress-bar bg-success"
                                     style="width:<?= $percent ?>%"></div>
                            </div>

                            <!-- نموذج الانضمام -->
                            <?php if ($group['status'] === 'active'): ?>

                                <form method="POST"
                                      action="<?= APP_URL ?>/groups/join">

                                    <input type="hidden"
                                           name="csrf_token"
                                           value="<?= Session::csrfToken() ?>">
                                    <input type="hidden"
                                           name="invite_code"
                                           value="<?= htmlspecialchars($code) ?>">

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">
                                            حصتي من المبلغ (دج)
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <input type="number"
                                                   name="pledged_amount"
                                                   class="form-control"
                                                   min="0"
                                                   placeholder="اختياري">
                                            <span class="input-group-text">دج</span>
                                        </div>
                                    </div>

                                    <button type="submit"
                                            class="btn btn-success w-100">
                                        <i class="bi bi-person-check me-2"></i>
                                        انضم للمجموعة
                                    </button>

                                </form>

                            <?php else: ?>
                                <div class="alert alert-warning small py-2 mb-0">
                                    <i class="bi bi-clock me-1"></i>
                                    <?php if ($group['status'] === 'pending'): ?>
                                        المجموعة بانتظار موافقة الجمعية
                                    <?php else: ?>
                                        المجموعة غير متاحة للانضمام
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/dashboard.php';
?>