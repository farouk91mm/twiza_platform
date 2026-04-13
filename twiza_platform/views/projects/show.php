<?php ob_start(); ?>

<?php
$percent = $project['target_amount'] > 0
    ? min(100, round(
        ($project['collected_amount'] / $project['target_amount']) * 100
      ))
    : 0;
?>

<div class="row g-4">

    <!-- العمود الرئيسي -->
    <div class="col-12 col-lg-8">

        <!-- صورة الغلاف -->
        <?php if ($project['cover_image']): ?>
            <img src="<?= UPLOAD_URL . $project['cover_image'] ?>"
                 class="w-100 rounded-3 mb-4"
                 style="max-height:350px;object-fit:cover"
                 alt="">
        <?php endif; ?>

        <!-- معلومات المشروع -->
        <div class="card content-card mb-4">
            <div class="card-body p-4">

                <span class="badge rounded-pill mb-3"
                      style="background:<?= $project['category_color'] ?? '#1a8a5a' ?>">
                    <i class="bi <?= $project['category_icon'] ?? 'bi-heart' ?> me-1"></i>
                    <?= htmlspecialchars($project['category_name'] ?? '') ?>
                </span>

                <h4 class="fw-bold mb-2">
                    <?= htmlspecialchars($project['title']) ?>
                </h4>

                <p class="text-muted small mb-4">
                    <i class="bi bi-building me-1"></i>
                    <?= htmlspecialchars($project['association_name'] ?? '') ?>
                    <?php if ($project['deadline']): ?>
                        &nbsp;|&nbsp;
                        <i class="bi bi-calendar me-1"></i>
                        ينتهي: <?= $project['deadline'] ?>
                    <?php endif; ?>
                </p>

                <p class="mb-0" style="line-height:1.8">
                    <?= nl2br(htmlspecialchars($project['description'])) ?>
                </p>

            </div>
        </div>

        <!-- التحديثات -->
        <?php if (!empty($updates)): ?>
            <div class="card content-card mb-4">
                <div class="card-header">
                    <i class="bi bi-bell text-success me-2"></i>
                    آخر التحديثات
                </div>
                <div class="card-body p-0">
                    <?php foreach ($updates as $update): ?>
                        <div class="p-3 border-bottom">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-semibold small">
                                    <?= htmlspecialchars($update['title']) ?>
                                </span>
                                <span class="text-muted"
                                      style="font-size:11px">
                                    <?= date('d/m/Y', strtotime($update['created_at'])) ?>
                                </span>
                            </div>
                            <p class="text-muted small mb-0">
                                <?= nl2br(htmlspecialchars($update['content'])) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- العمود الجانبي -->
    <div class="col-12 col-lg-4">

        <!-- بطاقة التقدم -->
        <div class="card content-card mb-3">
            <div class="card-body p-4">

                <div class="text-center mb-3">
                    <div class="fw-bold fs-4 text-success">
                        <?= number_format($project['collected_amount'], 0, ',', '.') ?>
                        <small class="fs-6">دج</small>
                    </div>
                    <div class="text-muted small">
                        من أصل
                        <?= number_format($project['target_amount'], 0, ',', '.') ?>
                        دج
                    </div>
                </div>

                <div class="progress mb-2" style="height:10px">
                    <div class="progress-bar bg-success"
                         style="width:<?= $percent ?>%"></div>
                </div>

                <div class="d-flex justify-content-between mb-4">
                    <small class="text-success fw-bold"><?= $percent ?>%</small>
                    <small class="text-muted">
                        <i class="bi bi-people me-1"></i>
                        <?= $project['donors_count'] ?> متبرع
                    </small>
                </div>

                <?php if ($project['beneficiary_count']): ?>
                    <div class="alert alert-success py-2 text-center small mb-3">
                        <i class="bi bi-heart me-1"></i>
                        يستفيد منه
                        <strong><?= $project['beneficiary_count'] ?></strong>
                        شخص
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- بطاقة معلومات التبرع -->
        <div class="card content-card mb-3">
            <div class="card-header">
                <i class="bi bi-info-circle text-success me-2"></i>
                كيف تتبرع؟
            </div>
            <div class="card-body p-3">
                <p class="small text-muted mb-3">
                    التبرع يتم مباشرة لحساب الجمعية
                </p>

                <?php if ($project['association_bank']): ?>
                    <div class="mb-2">
                        <small class="text-muted d-block">
                            <i class="bi bi-bank me-1"></i>
                            الحساب البنكي:
                        </small>
                        <code class="small">
                            <?= htmlspecialchars($project['association_bank']) ?>
                        </code>
                    </div>
                <?php endif; ?>

                <?php if ($project['association_ccp']): ?>
                    <div class="mb-2">
                        <small class="text-muted d-block">
                            <i class="bi bi-envelope me-1"></i>
                            حساب CCP:
                        </small>
                        <code class="small">
                            <?= htmlspecialchars($project['association_ccp']) ?>
                        </code>
                    </div>
                <?php endif; ?>

                <?php if ($project['association_phone']): ?>
                    <div class="mb-3">
                        <small class="text-muted d-block">
                            <i class="bi bi-telephone me-1"></i>
                            للتواصل:
                        </small>
                        <code class="small">
                            <?= htmlspecialchars($project['association_phone']) ?>
                        </code>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <?php if (Session::isLoggedIn() &&
          Session::get('user_type') === 'individual' &&
          $project['allow_groups']): ?>

    <div class="card content-card mb-3">
        <div class="card-body p-3 text-center">
            <i class="bi bi-people-fill fs-2 text-success d-block mb-2"></i>
            <h6 class="fw-bold mb-1">تبرع مع مجموعة</h6>
            <p class="text-muted small mb-3">
                اجمع مع أصدقائك وتبرعوا معاً
            </p>
            <div class="d-flex gap-2 justify-content-center">
                <a href="<?= APP_URL ?>/groups/create?project_id=<?= $project['id'] ?>"
                   class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>
                    إنشاء مجموعة
                </a>
                <a href="<?= APP_URL ?>/groups/join"
                   class="btn btn-outline-success btn-sm">
                    <i class="bi bi-person-plus me-1"></i>
                    انضم لمجموعة
                </a>
            </div>
        </div>
    </div>

<?php endif; ?>

<!-- نموذج التبرع -->
<?php if (Session::isLoggedIn() &&
          Session::get('user_type') === 'individual'): ?>

       

            <div class="card content-card">
                <div class="card-header">
                    <i class="bi bi-heart text-success me-2"></i>
                    سجّل تبرعك
                </div>
                <div class="card-body p-3">

                    <div class="alert alert-info small py-2 mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        بعد التحويل سجّل تبرعك هنا
                        وأرفق صورة الإثبات
                    </div>

                    <form method="POST"
                          action="<?= APP_URL ?>/donations/add"
                          enctype="multipart/form-data">

                        <input type="hidden"
                               name="csrf_token"
                               value="<?= Session::csrfToken() ?>">
                        <input type="hidden"
                               name="project_id"
                               value="<?= $project['id'] ?>">

                        <!-- المبلغ -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">
                                المبلغ (دج) *
                            </label>
                            <input type="number"
                                   name="amount"
                                   class="form-control"
                                   placeholder="أدخل المبلغ"
                                   min="100">
                        </div>

                        <!-- نوع التبرع -->
                        <?php if ($project['allow_recurring']): ?>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">
                                    نوع التبرع
                                </label>
                                <select name="donation_type"
                                        class="form-select form-select-sm">
                                    <option value="one_time">مرة واحدة</option>
                                    <option value="recurring">شهري متكرر</option>
                                </select>
                            </div>
                        <?php else: ?>
                            <input type="hidden"
                                   name="donation_type"
                                   value="one_time">
                        <?php endif; ?>

                        <!-- طريقة الدفع -->
                        <div class="mb-3">
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
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">
                                صورة إثبات التحويل
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

                        <!-- إخفاء الاسم -->
                        <div class="form-check mb-3">
                            <input type="checkbox"
                                   name="is_anonymous"
                                   id="anonymous"
                                   class="form-check-input"
                                   value="1">
                            <label class="form-check-label small"
                                   for="anonymous">
                                تبرع بدون ذكر اسمي
                            </label>
                        </div>

                        <button type="submit"
                                class="btn btn-success w-100">
                            <i class="bi bi-check-circle me-2"></i>
                            تسجيل التبرع
                        </button>

                    </form>
                </div>
            </div>

        <?php elseif (!Session::isLoggedIn()): ?>

            <div class="card content-card">
                <div class="card-body p-3 text-center">
                    <i class="bi bi-person-circle fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted small mb-3">
                        سجّل دخولك لتتمكن من التبرع
                    </p>
                    <a href="<?= APP_URL ?>/auth/login"
                       class="btn btn-success w-100">
                        تسجيل الدخول
                    </a>
                </div>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/dashboard.php';
?>