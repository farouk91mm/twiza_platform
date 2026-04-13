<?php ob_start(); ?>

<!-- ═══════════ HERO ═══════════ -->
<section class="hero">
    <div class="container position-relative">
        <div class="row align-items-center g-5">

            <!-- النص الرئيسي -->
            <div class="col-12 col-lg-6">

                <div class="hero-badge">
                    <i class="bi bi-heart-fill"></i>
                    منصة العمل الخيري الرقمي في الجزائر
                </div>

                <h1 class="hero-title mb-4">
                    تبرّع بثقة،
                    <br>
                    <span>وتابع أثرك</span>
                    <br>
                    خطوة بخطوة
                </h1>

                <p class="hero-subtitle mb-4">
                    منصة رقمية تربط المتبرعين بالجمعيات الخيرية
                    بشفافية كاملة — أموالك تصل مباشرة،
                    ونوثّق لك كل خطوة
                </p>

                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= APP_URL ?>/auth/register"
                       class="btn-main">
                        <i class="bi bi-heart"></i>
                        ابدأ التبرع الآن
                    </a>
                    <a href="<?= APP_URL ?>/projects"
                       class="btn-outline-main">
                        <i class="bi bi-grid"></i>
                        تصفح المشاريع
                    </a>
                </div>

            </div>

            <!-- بطاقات الإحصائيات -->
            <div class="col-12 col-lg-6">
                <div class="row g-3">

                    <div class="col-6">
                        <div class="hero-card">
                            <div class="number">
                                <?= number_format($stats['projects']) ?>
                            </div>
                            <div class="small opacity-75 mt-1">
                                <i class="bi bi-folder me-1"></i>
                                مشروع خيري نشط
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="hero-card">
                            <div class="number">
                                <?= number_format($stats['donors']) ?>
                            </div>
                            <div class="small opacity-75 mt-1">
                                <i class="bi bi-people me-1"></i>
                                متبرع مشارك
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="hero-card">
                            <div class="number">
                                <?= number_format($stats['donations'], 0) ?>
                            </div>
                            <div class="small opacity-75 mt-1">
                                <i class="bi bi-cash me-1"></i>
                                دج تم جمعها
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="hero-card">
                            <div class="number">
                                <?= number_format($stats['associations']) ?>
                            </div>
                            <div class="small opacity-75 mt-1">
                                <i class="bi bi-building me-1"></i>
                                جمعية موثقة
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>


<!-- ═══════════ الفئات ═══════════ -->
<section class="section">
    <div class="container">

        <div class="text-center">
            <h2 class="section-title">فئات المشاريع</h2>
            <p class="section-subtitle">
                اختر المجال الذي تريد دعمه
            </p>
        </div>

        <div class="row g-3 justify-content-center">
            <?php foreach ($categories as $cat): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="<?= APP_URL ?>/projects?category=<?= $cat['id'] ?>"
                       class="category-card">
                        <div class="cat-icon"
                             style="background:<?= $cat['color'] ?? '#1a8a5a' ?>">
                            <i class="bi <?= $cat['icon'] ?? 'bi-heart' ?>"></i>
                        </div>
                        <div class="cat-name">
                            <?= htmlspecialchars($cat['name']) ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>


<!-- ═══════════ أحدث المشاريع ═══════════ -->
<section class="section section-alt">
    <div class="container">

        <div class="text-center">
            <h2 class="section-title">أحدث المشاريع الخيرية</h2>
            <p class="section-subtitle">
                ساهم في مشاريع حقيقية وتابع أثر تبرعك
            </p>
        </div>

        <?php if (empty($projects)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-folder-x fs-1 d-block mb-3"></i>
                لا توجد مشاريع نشطة حالياً
            </div>
        <?php else: ?>

            <div class="row g-4">
                <?php foreach ($projects as $project): ?>
                    <?php
                    $percent = $project['target_amount'] > 0
                        ? min(100, round(
                            ($project['collected_amount'] /
                             $project['target_amount']) * 100
                          ))
                        : 0;
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card project-card">

                            <?php if ($project['cover_image']): ?>
                                <img src="<?= UPLOAD_URL . $project['cover_image'] ?>"
                                     class="card-img-top"
                                     alt="">
                            <?php else: ?>
                                <div class="card-img-placeholder"
                                     style="background:<?= $project['category_color'] ?? '#1a8a5a' ?>20">
                                    <i class="bi <?= $project['category_icon'] ?? 'bi-heart' ?>"
                                       style="color:<?= $project['category_color'] ?? '#1a8a5a' ?>"></i>
                                </div>
                            <?php endif; ?>

                            <div class="card-body d-flex flex-column p-4">

                                <span class="badge rounded-pill mb-2"
                                      style="background:<?= $project['category_color'] ?? '#1a8a5a' ?>;
                                             width:fit-content">
                                    <?= htmlspecialchars($project['category_name'] ?? '') ?>
                                </span>

                                <h6 class="fw-bold mb-1">
                                    <?= htmlspecialchars($project['title']) ?>
                                </h6>

                                <p class="text-muted small mb-1">
                                    <i class="bi bi-building me-1"></i>
                                    <?= htmlspecialchars($project['association_name'] ?? '') ?>
                                </p>

                                <p class="text-muted small mb-3"
                                   style="display:-webkit-box;
                                          -webkit-line-clamp:2;
                                          -webkit-box-orient:vertical;
                                          overflow:hidden;
                                          flex:1">
                                    <?= htmlspecialchars($project['description']) ?>
                                </p>

                                <!-- التقدم -->
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">
                                        <?= number_format($project['collected_amount'], 0, ',', '.') ?>
                                        دج
                                    </small>
                                    <small class="fw-bold text-success">
                                        <?= $percent ?>%
                                    </small>
                                </div>
                                <div class="progress mb-3">
                                    <div class="progress-bar"
                                         style="width:<?= $percent ?>%"></div>
                                </div>

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

                                <a href="<?= APP_URL ?>/projects/show?id=<?= $project['id'] ?>"
                                   class="btn btn-success w-100 rounded-pill">
                                    <i class="bi bi-heart me-1"></i>
                                    تبرع الآن
                                </a>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-5">
                <a href="<?= APP_URL ?>/projects"
                   class="btn-main">
                    <i class="bi bi-grid"></i>
                    عرض كل المشاريع
                </a>
            </div>

        <?php endif; ?>

    </div>
</section>


<!-- ═══════════ كيف تعمل ═══════════ -->
<section class="section" id="how-it-works">
    <div class="container">

        <div class="text-center">
            <h2 class="section-title">كيف تعمل المنصة؟</h2>
            <p class="section-subtitle">
                3 خطوات بسيطة للمساهمة في العمل الخيري
            </p>
        </div>

        <div class="row g-4">

            <div class="col-12 col-md-4">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-title">سجّل حسابك</div>
                    <p class="step-desc">
                        أنشئ حساباً مجانياً في دقيقة واحدة
                        كفرد أو تاجر وابدأ رحلتك الخيرية
                    </p>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-title">اختر مشروعاً</div>
                    <p class="step-desc">
                        تصفح المشاريع الخيرية الموثقة
                        واختر ما يناسبك وحوّل المبلغ
                        مباشرة للجمعية
                    </p>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-title">تابع أثرك</div>
                    <p class="step-desc">
                        سجّل تبرعك في المنصة وتابع
                        تأكيد الجمعية وانعكاس أثر
                        تبرعك على المشروع
                    </p>
                </div>
            </div>

        </div>

    </div>
</section>


<!-- ═══════════ CTA ═══════════ -->
<section class="cta-section">
    <div class="container">

        <h2 class="text-white fw-800 mb-3" style="font-size:2rem;font-weight:800">
            ابدأ رحلتك الخيرية اليوم
        </h2>
        <p class="text-white mb-4" style="opacity:0.85">
            انضم لآلاف المتبرعين الذين يصنعون الفرق
        </p>

        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <a href="<?= APP_URL ?>/auth/register"
               class="btn btn-light btn-lg px-5 rounded-pill fw-bold"
               style="color:#1a8a5a">
                <i class="bi bi-person-plus me-2"></i>
                إنشاء حساب مجاني
            </a>
            <a href="<?= APP_URL ?>/projects"
               class="btn-outline-main btn-lg">
                <i class="bi bi-grid me-2"></i>
                تصفح المشاريع
            </a>
        </div>

    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>