<?php ob_start(); ?>

<div class="card content-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-pencil text-success me-2"></i>
            تعديل التبرع
        </span>
        <a href="<?= APP_URL ?>/individual/donations"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i>
            رجوع
        </a>
    </div>
    <div class="card-body p-4">

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- معلومات التبرع الحالي -->
        <div class="alert alert-info mb-4">
            <div class="fw-semibold mb-1">
                <i class="bi bi-info-circle me-1"></i>
                معلومات التبرع الحالي
            </div>
            <div class="small">
                <strong>المشروع:</strong>
                <?= htmlspecialchars($donation['project_title'] ?? '') ?>
            </div>
            <div class="small">
                <strong>الجمعية:</strong>
                <?= htmlspecialchars($donation['association_name'] ?? '') ?>
            </div>
            <div class="small">
                <strong>تاريخ التسجيل:</strong>
                <?= date('d/m/Y H:i', strtotime($donation['created_at'])) ?>
            </div>
        </div>

        <form method="POST"
              action="<?= APP_URL ?>/donations/edit"
              enctype="multipart/form-data"
              novalidate>

            <input type="hidden"
                   name="csrf_token"
                   value="<?= Session::csrfToken() ?>">
            <input type="hidden"
                   name="donation_id"
                   value="<?= $donation['id'] ?>">

            <div class="row g-3">

                <!-- المبلغ -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        المبلغ (دج) *
                    </label>
                    <div class="input-group">
                        <input type="number"
                               name="amount"
                               value="<?= $donation['amount'] ?? '' ?>"
                               class="form-control <?= isset($errors['amount']) ? 'is-invalid' : '' ?>"
                               min="100">
                        <span class="input-group-text">دج</span>
                        <?php if (isset($errors['amount'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['amount'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- طريقة الدفع -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        طريقة الدفع *
                    </label>
                    <select name="payment_method"
                            class="form-select <?= isset($errors['payment_method']) ? 'is-invalid' : '' ?>">
                        <option value="ccp"
                            <?= ($donation['payment_method'] ?? '') === 'ccp'
                                ? 'selected' : '' ?>>
                            بريد الجزائر CCP
                        </option>
                        <option value="bank_transfer"
                            <?= ($donation['payment_method'] ?? '') === 'bank_transfer'
                                ? 'selected' : '' ?>>
                            تحويل بنكي
                        </option>
                        <option value="cash"
                            <?= ($donation['payment_method'] ?? '') === 'cash'
                                ? 'selected' : '' ?>>
                            نقداً في مقر الجمعية
                        </option>
                        <option value="other"
                            <?= ($donation['payment_method'] ?? '') === 'other'
                                ? 'selected' : '' ?>>
                            أخرى
                        </option>
                    </select>
                    <?php if (isset($errors['payment_method'])): ?>
                        <div class="invalid-feedback">
                            <?= $errors['payment_method'] ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- نوع التبرع -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        نوع التبرع
                    </label>
                    <select name="donation_type" class="form-select">
                        <option value="one_time"
                            <?= ($donation['donation_type'] ?? '') === 'one_time'
                                ? 'selected' : '' ?>>
                            مرة واحدة
                        </option>
                        <option value="recurring"
                            <?= ($donation['donation_type'] ?? '') === 'recurring'
                                ? 'selected' : '' ?>>
                            شهري متكرر
                        </option>
                    </select>
                </div>

                <!-- صورة الإثبات -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        صورة إثبات التحويل
                    </label>

                    <?php if ($donation['proof_image']): ?>
                        <div class="mb-2">
                            <a href="<?= UPLOAD_URL . $donation['proof_image'] ?>"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-image me-1"></i>
                                عرض الصورة الحالية
                            </a>
                        </div>
                    <?php endif; ?>

                    <input type="file"
                           name="proof_image"
                           class="form-control"
                           accept="image/jpeg,image/png,image/webp">
                    <div class="form-text">
                        اتركه فارغاً للإبقاء على الصورة الحالية
                    </div>
                </div>

                <!-- ملاحظات -->
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        ملاحظات
                    </label>
                    <textarea name="notes"
                              rows="2"
                              class="form-control"
                              placeholder="اختياري"
                              ><?= htmlspecialchars($donation['notes'] ?? '') ?></textarea>
                </div>

                <!-- إخفاء الاسم -->
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox"
                               name="is_anonymous"
                               id="anonymous"
                               class="form-check-input"
                               value="1"
                               <?= $donation['is_anonymous']
                                   ? 'checked' : '' ?>>
                        <label class="form-check-label"
                               for="anonymous">
                            تبرع بدون ذكر اسمي
                        </label>
                    </div>
                </div>

                <!-- أزرار -->
                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                    <a href="<?= APP_URL ?>/individual/donations"
                       class="btn btn-outline-secondary">
                        إلغاء
                    </a>
                    <button type="submit"
                            class="btn btn-success px-4">
                        <i class="bi bi-check-circle me-2"></i>
                        حفظ التعديلات
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/dashboard.php';
?>