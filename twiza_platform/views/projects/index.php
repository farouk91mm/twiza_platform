<?php ob_start(); ?>

<!-- فلترة وبحث -->
<div class="card content-card mb-4">
    <div class="card-body p-3">
        <form method="GET"
              action="<?= APP_URL ?>/projects"
              class="row g-2 align-items-center">

            <!-- بحث -->
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text"
                           name="search"
                           value="<?= htmlspecialchars($search) ?>"
                           class="form-control border-start-0"
                           placeholder="ابحث عن مشروع...">
                </div>
            </div>

            <!-- فئات -->
            <div class="col-12 col-md-5">
                <select name="category" class="form-select">
                    <option value="">كل الفئات</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= $category == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-success w-100">
                    بحث
                </button>
            </div>

        </form>
    </div>
</div>

<!-- قائمة المشاريع -->
<?php if (empty($projects)): ?>
    <div class="text-center py-5">
        <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
        <h5 class="text-muted">لا توجد مشاريع</h5>
        <p class="text-muted small">جرب تغيير كلمة البحث أو الفئة</p>
    </div>

<?php else: ?>
    <div class="row g-3">
        <?php foreach ($projects as $project): ?>
            <?php
            $percent = $project['target_amount'] > 0
                ? min(100, round(
                    ($project['collected_amount'] / $project['target_amount']) * 100
                  ))
                : 0;
            ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card content-card h-100">

                    <!-- صورة الغلاف -->
                    <?php if ($project['cover_image']): ?>
                        <img src="<?= UPLOAD_URL . $project['cover_image'] ?>"
                             class="card-img-top"
                             style="height:180px;object-fit:cover"
                             alt="">
                    <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center"
                             style="height:180px;
                                    background:<?= $project['category_color'] ?? '#1a8a5a' ?>20">
                            <i class="bi <?= $project['category_icon'] ?? 'bi-heart' ?> fs-1"
                               style="color:<?= $project['category_color'] ?? '#1a8a5a' ?>"></i>
                        </div>
                    <?php endif; ?>

                    <div class="card-body d-flex flex-column">

                        <!-- الفئة -->
                        <span class="badge rounded-pill mb-2"
                              style="background:<?= $project['category_color'] ?? '#1a8a5a' ?>;
                                     width:fit-content">
                            <?= htmlspecialchars($project['category_name'] ?? '') ?>
                        </span>

                        <!-- العنوان -->
                        <h6 class="fw-bold mb-1">
                            <?= htmlspecialchars($project['title']) ?>
                        </h6>

                        <!-- الجمعية -->
                        <p class="text-muted small mb-2">
                            <i class="bi bi-building me-1"></i>
                            <?= htmlspecialchars($project['association_name'] ?? '') ?>
                        </p>

                        <!-- الوصف -->
                        <p class="text-muted small mb-3"
                           style="display:-webkit-box;
                                  -webkit-line-clamp:2;
                                  -webkit-box-orient:vertical;
                                  overflow:hidden">
                            <?= htmlspecialchars($project['description']) ?>
                        </p>

                        <div class="mt-auto">
                            <!-- شريط التقدم -->
                            <div class="d-flex justify-content-between
                                        align-items-center mb-1">
                                <small class="text-muted">
                                    <?= number_format($project['collected_amount'], 0, ',', '.') ?>
                                    دج
                                </small>
                                <small class="fw-bold text-success">
                                    <?= $percent ?>%
                                </small>
                            </div>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-success"
                                     style="width:<?= $percent ?>%"></div>
                            </div>

                            <!-- معلومات إضافية -->
                            <div class="d-flex justify-content-between
                                        align-items-center mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-people me-1"></i>
                                    <?= $project['donors_count'] ?> متبرع
                                </small>
                                <small class="text-muted">
                                    الهدف:
                                    <?= number_format($project['target_amount'], 0, ',', '.') ?>
                                    دج
                                </small>
                            </div>

                            <!-- زر التفاصيل -->
                            <a href="<?= APP_URL ?>/projects/show?id=<?= $project['id'] ?>"
                               class="btn btn-success w-100">
                                <i class="bi bi-eye me-1"></i>
                                عرض المشروع
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/dashboard.php';
?>