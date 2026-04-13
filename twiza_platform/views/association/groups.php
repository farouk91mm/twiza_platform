<?php ob_start(); ?>

<!-- رسائل -->
<?php if (Session::get('success')): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>
        <?= Session::get('success') ?>
    </div>
    <?php Session::set('success', null); ?>
<?php endif; ?>

<div class="card content-card">
    <div class="card-header">
        <i class="bi bi-people text-success me-2"></i>
        المجموعات الخيرية
        <span class="badge bg-success ms-1">
            <?= count($groups) ?>
        </span>
    </div>
    <div class="card-body p-0">

        <?php if (empty($groups)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people fs-1 d-block mb-3"></i>
                لا توجد مجموعات بعد
            </div>

        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>المجموعة</th>
                            <th>المشروع</th>
                            <th>المنشئ</th>
                            <th>الأعضاء</th>
                            <th>التقدم</th>
                            <th>الحالة</th>
                            <th>إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($groups as $g): ?>
                        <?php
                        $percent = $g['target_amount'] > 0
                            ? min(100, round(
                                ($g['collected_amount'] /
                                 $g['target_amount']) * 100
                              ))
                            : 0;

                        $statusLabels = [
                            'pending'   => ['warning', 'بانتظار الموافقة'],
                            'active'    => ['success', 'نشطة'],
                            'completed' => ['primary', 'مكتملة'],
                            'cancelled' => ['danger',  'ملغاة'],
                        ];
                        [$sc, $sl] = $statusLabels[$g['status']]
                                     ?? ['secondary', ''];
                        ?>
                        <tr>
                            <td>
                                <div class="fw-semibold">
                                    <?= htmlspecialchars($g['name']) ?>
                                </div>
                                <small class="text-muted font-monospace">
                                    <?= $g['invite_code'] ?>
                                </small>
                            </td>
                            <td>
                                <?= htmlspecialchars($g['project_title']) ?>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($g['creator_name']) ?></div>
                                <small class="text-muted">
                                    <?= htmlspecialchars($g['creator_email']) ?>
                                </small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-people me-1"></i>
                                    <?= $g['members_count'] ?>
                                </span>
                            </td>
                            <td style="min-width:120px">
                                <div class="progress" style="height:6px">
                                    <div class="progress-bar bg-success"
                                         style="width:<?= $percent ?>%"></div>
                                </div>
                                <small class="text-muted">
                                    <?= number_format($g['collected_amount'], 0, ',', '.') ?>
                                    /
                                    <?= number_format($g['target_amount'], 0, ',', '.') ?>
                                    دج
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-<?= $sc ?>">
                                    <?= $sl ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($g['status'] === 'pending'): ?>
                                    <div class="d-flex gap-1">

                                        <!-- موافقة -->
                                        <form method="POST"
                                              action="<?= APP_URL ?>/association/groups/approve">
                                            <input type="hidden" name="csrf_token"
                                                   value="<?= Session::csrfToken() ?>">
                                            <input type="hidden" name="group_id"
                                                   value="<?= $g['id'] ?>">
                                            <input type="hidden" name="action"
                                                   value="approve">
                                            <button type="submit"
                                                    class="btn btn-sm btn-success"
                                                    title="موافقة">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>

                                        <!-- رفض -->
                                        <form method="POST"
                                              action="<?= APP_URL ?>/association/groups/approve">
                                            <input type="hidden" name="csrf_token"
                                                   value="<?= Session::csrfToken() ?>">
                                            <input type="hidden" name="group_id"
                                                   value="<?= $g['id'] ?>">
                                            <input type="hidden" name="action"
                                                   value="reject">
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    title="رفض">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>

                                    </div>

                                <?php elseif ($g['status'] === 'active'): ?>

                                    <a href="<?= APP_URL ?>/groups/show?id=<?= $g['id'] ?>"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-eye me-1"></i>
                                        عرض
                                    </a>

                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/dashboard.php';
?>