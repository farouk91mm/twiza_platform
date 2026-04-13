<?php ob_start(); ?>

<div class="card border-0 shadow-sm" style="border-radius:12px">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-plus-circle text-success me-2"></i>
            إضافة مشروع خيري جديد
        </h6>
    </div>
    <div class="card-body p-4">

        <form method="POST"
              action="<?= APP_URL ?>/association/projects/add"
              enctype="multipart/form-data"
              novalidate>

            <input type="hidden"
                   name="csrf_token"
                   value="<?= Session::csrfToken() ?>">

            <div class="row g-3">

                <!-- عنوان المشروع -->
                <div class="col-12">
                    <label class="form-label fw-semibold">عنوان المشروع *</label>
                    <input type="text" name="title"
                           value="<?= htmlspecialchars($old['title'] ?? '') ?>"
                           class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                           placeholder="مثال: كفالة 10 أيتام في ولاية الجزائر">
                    <?php if (isset($errors['title'])): ?>
                        <div class="invalid-feedback"><?= $errors['title'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- الفئة والمبلغ -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">فئة المشروع *</label>
                    <select name="category_id"
                            class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>">
                        <option value="">اختر الفئة</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= (($old['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['category_id'])): ?>
                        <div class="invalid-feedback"><?= $errors['category_id'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">المبلغ المطلوب (دج) *</label>
                    <div class="input-group">
                        <input type="number" name="target_amount"
                               value="<?= htmlspecialchars($old['target_amount'] ?? '') ?>"
                               class="form-control <?= isset($errors['target_amount']) ? 'is-invalid' : '' ?>"
                               placeholder="0" min="1000">
                        <span class="input-group-text">دج</span>
                        <?php if (isset($errors['target_amount'])): ?>
                            <div class="invalid-feedback"><?= $errors['target_amount'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- الوصف -->
                <div class="col-12">
                    <label class="form-label fw-semibold">وصف المشروع *</label>
                    <textarea name="description" rows="4"
                              class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                              placeholder="اشرح هدف المشروع، المستفيدين، وكيف ستُصرف الأموال..."
                              ><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback"><?= $errors['description'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- عدد المستفيدين والموعد -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">عدد المستفيدين</label>
                    <input type="number" name="beneficiary_count"
                           value="<?= htmlspecialchars($old['beneficiary_count'] ?? '') ?>"
                           class="form-control"
                           placeholder="اختياري" min="1">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">الموعد النهائي</label>
                    <input type="date" name="deadline"
                           value="<?= htmlspecialchars($old['deadline'] ?? '') ?>"
                           class="form-control"
                           min="<?= date('Y-m-d') ?>">
                </div>

                <!-- صورة الغلاف -->
                <div class="col-12">
                    <label class="form-label fw-semibold">صورة الغلاف</label>
                    <input type="file" name="cover_image"
                           class="form-control"
                           accept="image/jpeg,image/png,image/webp">
                    <div class="form-text">JPG, PNG أو WebP — بحد أقصى 5MB</div>
                </div>

                <!-- خيارات إضافية -->
                <div class="col-12">
                    <label class="form-label fw-semibold">خيارات إضافية</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input type="checkbox" name="allow_recurring"
                                   id="allow_recurring"
                                   class="form-check-input"
                                   value="1"
                                   <?= isset($old['allow_recurring']) ? 'checked' : 'checked' ?>>
                            <label class="form-check-label" for="allow_recurring">
                                قبول التبرعات الشهرية المتكررة
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="allow_groups"
                                   id="allow_groups"
                                   class="form-check-input"
                                   value="1"
                                   <?= isset($old['allow_groups']) ? 'checked' : 'checked' ?>>
                            <label class="form-check-label" for="allow_groups">
                                قبول المجموعات الخيرية
                            </label>
                        </div>
                    </div>
                </div>

                <!-- أزرار -->
                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                    <a href="<?= APP_URL ?>/association/dashboard"
                       class="btn btn-outline-secondary">
                        إلغاء
                    </a>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-circle me-2"></i>
                        نشر المشروع
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