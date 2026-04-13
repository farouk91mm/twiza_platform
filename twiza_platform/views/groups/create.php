<?php ob_start(); ?>

<div class="card content-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-people text-success me-2"></i>
            إنشاء مجموعة خيرية
        </span>
        <a href="<?= APP_URL ?>/projects/show?id=<?= $project['id'] ?>"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i>
            رجوع
        </a>
    </div>
    <div class="card-body p-4">

        <!-- معلومات المشروع -->
        <div class="alert alert-success mb-4">
            <div class="fw-semibold mb-1">
                <i class="bi bi-folder me-1"></i>
                المشروع المختار
            </div>
            <div class="small">
                <strong><?= htmlspecialchars($project['title']) ?></strong>
                — <?= htmlspecialchars($project['association_name'] ?? '') ?>
            </div>
            <div class="small text-muted mt-1">
                الهدف:
                <?= number_format($project['target_amount'], 0, ',', '.') ?>
                دج
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST"
              action="<?= APP_URL ?>/groups/create"
              novalidate>

            <input type="hidden"
                   name="csrf_token"
                   value="<?= Session::csrfToken() ?>">
            <input type="hidden"
                   name="project_id"
                   value="<?= $project['id'] ?>">

            <div class="row g-3">

                <!-- اسم المجموعة -->
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        اسم المجموعة *
                    </label>
                    <input type="text"
                           name="name"
                           value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                           class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           placeholder="مثال: مجموعة الأصدقاء — كفالة أيتام">
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback">
                            <?= $errors['name'] ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- الوصف -->
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        وصف المجموعة
                    </label>
                    <textarea name="description"
                              rows="2"
                              class="form-control"
                              placeholder="اختياري — وصف قصير للمجموعة"
                              ><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                </div>

                <!-- المبلغ المستهدف -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        المبلغ المستهدف (دج) *
                    </label>
                    <div class="input-group">
                        <input type="number"
                               name="target_amount"
                               value="<?= htmlspecialchars($old['target_amount'] ?? '') ?>"
                               class="form-control <?= isset($errors['target_amount']) ? 'is-invalid' : '' ?>"
                               min="1000"
                               placeholder="المبلغ الذي تريد جمعه">
                        <span class="input-group-text">دج</span>
                        <?php if (isset($errors['target_amount'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['target_amount'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- حصتي -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        حصتي من المبلغ (دج)
                    </label>
                    <div class="input-group">
                        <input type="number"
                               name="my_pledge"
                               value="<?= htmlspecialchars($old['my_pledge'] ?? '') ?>"
                               class="form-control"
                               min="0"
                               placeholder="اختياري">
                        <span class="input-group-text">دج</span>
                    </div>
                    <div class="form-text">
                        المبلغ الذي ستساهم به شخصياً
                    </div>
                </div>

                <!-- تنبيه -->
                <div class="col-12">
                    <div class="alert alert-info small py-2">
                        <i class="bi bi-info-circle me-1"></i>
                        بعد الإنشاء ستحصل على كود دعوة
                        تشاركه مع أصدقائك للانضمام
                        <br>
                        <i class="bi bi-clock me-1"></i>
                        المجموعة تحتاج موافقة الجمعية قبل البدء
                    </div>
                </div>

                <!-- أزرار -->
                <div class="col-12 d-flex gap-2 justify-content-end">
                    <a href="<?= APP_URL ?>/projects/show?id=<?= $project['id'] ?>"
                       class="btn btn-outline-secondary">
                        إلغاء
                    </a>
                    <button type="submit"
                            class="btn btn-success px-4">
                        <i class="bi bi-people me-2"></i>
                        إنشاء المجموعة
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