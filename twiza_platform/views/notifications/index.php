<?php ob_start(); ?>

<div class="card content-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-bell text-success me-2"></i>
            الإشعارات
        </span>
        <?php if (!empty($notifications)): ?>
            <span class="badge bg-success rounded-pill">
                <?= count($notifications) ?>
            </span>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">

        <?php if (empty($notifications)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash fs-1 d-block mb-3"></i>
                لا توجد إشعارات
            </div>

        <?php else: ?>
            <?php foreach ($notifications as $notif): ?>
                <?php
                $icons = [
                    'donation_confirmed' => ['bi-check-circle-fill', 'success'],
                    'project_completed'  => ['bi-trophy-fill',       'warning'],
                    'project_update'     => ['bi-megaphone-fill',    'info'],
                    'group_approved'     => ['bi-people-fill',       'primary'],
                    'group_invite'       => ['bi-person-plus-fill',  'secondary'],
                    'general'            => ['bi-bell-fill',         'success'],
                ];
                [$icon, $color] =
                    $icons[$notif['type']] ?? ['bi-bell-fill', 'success'];
                $isRead = $notif['is_read'];
                ?>

                <div class="d-flex align-items-start gap-3 p-3 border-bottom
                            <?= !$isRead ? 'bg-light' : '' ?>">

                    <!-- أيقونة -->
                    <div class="rounded-circle d-flex align-items-center
                                justify-content-center flex-shrink-0"
                         style="width:42px;height:42px;
                                background:var(--bs-<?= $color ?>-bg-subtle,#f0fdf4)">
                        <i class="bi <?= $icon ?> text-<?= $color ?>"></i>
                    </div>

                    <!-- المحتوى -->
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="fw-semibold small <?= !$isRead ? 'text-dark' : 'text-muted' ?>">
                                <?= htmlspecialchars($notif['title']) ?>
                                <?php if (!$isRead): ?>
                                    <span class="badge bg-success ms-1"
                                          style="font-size:9px">
                                        جديد
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <small class="text-muted"
                                       style="font-size:11px;white-space:nowrap">
                                    <?= date('d/m/Y H:i',
                                        strtotime($notif['created_at'])) ?>
                                </small>

                                <!-- زر الحذف -->
                                <form method="POST"
                                      action="<?= APP_URL ?>/notifications/delete"
                                      class="d-inline">
                                    <input type="hidden"
                                           name="csrf_token"
                                           value="<?= Session::csrfToken() ?>">
                                    <input type="hidden"
                                           name="notif_id"
                                           value="<?= $notif['id'] ?>">
                                    <button type="submit"
                                            class="btn btn-sm btn-link
                                                   text-muted p-0"
                                            title="حذف">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <p class="text-muted small mb-0 mt-1">
                            <?= htmlspecialchars($notif['message']) ?>
                        </p>

                        <!-- رابط للمشروع أو التبرع -->
                        <?php if ($notif['related_id'] &&
                                  $notif['related_type'] === 'project'): ?>
                            <a href="<?= APP_URL ?>/projects/show?id=<?= $notif['related_id'] ?>"
                               class="btn btn-sm btn-outline-success mt-2"
                               style="font-size:12px">
                                <i class="bi bi-eye me-1"></i>
                                عرض المشروع
                            </a>
                        <?php elseif ($notif['related_id'] &&
                                      $notif['related_type'] === 'donation'): ?>
                            <a href="<?= APP_URL ?>/individual/donations"
                               class="btn btn-sm btn-outline-success mt-2"
                               style="font-size:12px">
                                <i class="bi bi-heart me-1"></i>
                                عرض تبرعاتي
                            </a>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/dashboard.php';
?>