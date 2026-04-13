<?php ob_start(); ?>

<div class="card border-0 shadow-sm" style="border-radius:12px">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-pencil-square text-success me-2"></i>
            تعديل المشروع
        </h6>
        <a href="<?= APP_URL ?>/association/dashboard"
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

        <form method="POST"
              action="<?= APP_URL ?>/association/projects/edit"
              enctype="multipart/form-data"
              novalidate>

            <input type="hidden"
                   name="csrf_token"
                   value="<?= Session::csrfToken() ?>">
            <input type="hidden"
                   name="project_id"
                   value="<?= $project['id'] ?>">

            <div class="row g-3">

                <!-- عنوان المشروع -->
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        عنوان المشروع *
                    </label>
                    <input type="text"
                           name="title"
                           value="<?= htmlspecialchars($project['title'] ?? '') ?>"
                           class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                           placeholder="عنوان المشروع">
                    <?php if (isset($errors['title'])): ?>
                        <div class="invalid-feedback">
                            <?= $errors['title'] ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- الفئة والمبلغ -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        فئة المشروع *
                    </label>
                    <select name="category_id"
                            class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>">
                        <option value="">اختر الفئة</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= $project['category_id'] == $cat['id']
                                    ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['category_id'])): ?>
                        <div class="invalid-feedback">
                            <?= $errors['category_id'] ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        المبلغ المطلوب (دج) *
                    </label>
                    <div class="input-group">
                        <input type="number"
                               name="target_amount"
                               value="<?= $project['target_amount'] ?? '' ?>"
                               class="form-control <?= isset($errors['target_amount']) ? 'is-invalid' : '' ?>"
                               min="1000">
                        <span class="input-group-text">دج</span>
                        <?php if (isset($errors['target_amount'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['target_amount'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- الوصف -->
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        وصف المشروع *
                    </label>
                    <textarea name="description"
                              rows="4"
                              class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                              placeholder="وصف تفصيلي للمشروع..."
                              ><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback">
                            <?= $errors['description'] ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- عدد المستفيدين والموعد -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        عدد المستفيدين
                    </label>
                    <input type="number"
                           name="beneficiary_count"
                           value="<?= $project['beneficiary_count'] ?? '' ?>"
                           class="form-control"
                           min="1">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        الموعد النهائي
                    </label>
                    <input type="date"
                           name="deadline"
                           value="<?= $project['deadline'] ?? '' ?>"
                           class="form-control">
                </div>

                <!-- حالة المشروع -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        حالة المشروع
                    </label>
                    <select name="status" class="form-select">
                        <option value="active"
                            <?= ($project['status'] ?? '') === 'active'
                                ? 'selected' : '' ?>>
                            نشط
                        </option>
                        <option value="paused"
                            <?= ($project['status'] ?? '') === 'paused'
                                ? 'selected' : '' ?>>
                            موقوف مؤقتاً
                        </option>
                        <option value="completed"
                            <?= ($project['status'] ?? '') === 'completed'
                                ? 'selected' : '' ?>>
                            مكتمل
                        </option>
                        <option value="cancelled"
                            <?= ($project['status'] ?? '') === 'cancelled'
                                ? 'selected' : '' ?>>
                            ملغى
                        </option>
                    </select>
                </div>

                <!-- صورة الغلاف -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        صورة الغلاف
                    </label>

                    <?php if ($project['cover_image']): ?>
                        <div class="mb-2">
                            <img src="<?= UPLOAD_URL . $project['cover_image'] ?>"
                                 class="rounded"
                                 style="height:80px;object-fit:cover"
                                 alt="الصورة الحالية">
                            <small class="text-muted d-block mt-1">
                                الصورة الحالية
                            </small>
                        </div>
                    <?php endif; ?>

                    <input type="file"
                           name="cover_image"
                           class="form-control"
                           accept="image/jpeg,image/png,image/webp">
                    <div class="form-text">
                        اتركه فارغاً للإبقاء على الصورة الحالية
                    </div>
                </div>

                <!-- خيارات إضافية -->
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        خيارات إضافية
                    </label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input type="checkbox"
                                   name="allow_recurring"
                                   id="allow_recurring"
                                   class="form-check-input"
                                   value="1"
                                   <?= $project['allow_recurring']
                                       ? 'checked' : '' ?>>
                            <label class="form-check-label"
                                   for="allow_recurring">
                                قبول التبرعات الشهرية
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox"
                                   name="allow_groups"
                                   id="allow_groups"
                                   class="form-check-input"
                                   value="1"
                                   <?= $project['allow_groups']
                                       ? 'checked' : '' ?>>
                            <label class="form-check-label"
                                   for="allow_groups">
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