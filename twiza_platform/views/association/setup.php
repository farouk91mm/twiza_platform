<?php ob_start(); ?>

<div class="card border-0 shadow-sm" style="border-radius:12px">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-building text-success me-2"></i>
            إعداد ملف الجمعية
        </h6>
    </div>
    <div class="card-body p-4">

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST"
              action="<?= APP_URL ?>/association/setup"
              enctype="multipart/form-data"
              novalidate>

            <input type="hidden"
                   name="csrf_token"
                   value="<?= Session::csrfToken() ?>">

            <div class="row g-3">

                <!-- اسم الجمعية الرسمي -->
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        اسم الجمعية الرسمي *
                    </label>
                    <input type="text"
                           name="official_name"
                           value="<?= htmlspecialchars($old['official_name'] ?? '') ?>"
                           class="form-control <?= isset($errors['official_name']) ? 'is-invalid' : '' ?>"
                           placeholder="الاسم الرسمي كما هو في وثيقة الاعتماد">
                    <?php if (isset($errors['official_name'])): ?>
                        <div class="invalid-feedback">
                            <?= $errors['official_name'] ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- رقم الاعتماد -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        رقم الاعتماد *
                    </label>
                    <input type="text"
                           name="registration_number"
                           value="<?= htmlspecialchars($old['registration_number'] ?? '') ?>"
                           class="form-control <?= isset($errors['registration_number']) ? 'is-invalid' : '' ?>"
                           placeholder="مثال: REG-2024-001">
                    <?php if (isset($errors['registration_number'])): ?>
                        <div class="invalid-feedback">
                            <?= $errors['registration_number'] ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- الولاية -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">الولاية</label>
                    <select name="wilaya" class="form-select">
                        <option value="">اختر الولاية</option>
                        <?php
                        $wilayas = [
                            'أدرار','الشلف','الأغواط','أم البواقي','باتنة',
                            'بجاية','بسكرة','بشار','البليدة','البويرة',
                            'تمنراست','تبسة','تلمسان','تيارت','تيزي وزو',
                            'الجزائر','الجلفة','جيجل','سطيف','سعيدة',
                            'سكيكدة','سيدي بلعباس','عنابة','قالمة','قسنطينة',
                            'المدية','مستغانم','المسيلة','معسكر','ورقلة',
                            'وهران','البيض','إليزي','برج بوعريريج','بومرداس',
                            'الطارف','تندوف','تيسمسيلت','الوادي','خنشلة',
                            'سوق أهراس','تيبازة','ميلة','عين الدفلى','النعامة',
                            'عين تموشنت','غرداية','غليزان'
                        ];
                        foreach ($wilayas as $w):
                        ?>
                            <option value="<?= $w ?>"
                                <?= (($old['wilaya'] ?? '') === $w) ? 'selected' : '' ?>>
                                <?= $w ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- رقم الهاتف -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">رقم الهاتف</label>
                    <input type="text"
                           name="phone"
                           value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                           class="form-control"
                           placeholder="05xxxxxxxx">
                </div>

                <!-- الشعار -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">شعار الجمعية</label>
                    <input type="file"
                           name="logo"
                           class="form-control"
                           accept="image/jpeg,image/png,image/webp">
                    <div class="form-text">اختياري — JPG أو PNG</div>
                </div>

                <!-- الوصف -->
                <div class="col-12">
                    <label class="form-label fw-semibold">نبذة عن الجمعية</label>
                    <textarea name="description"
                              rows="3"
                              class="form-control"
                              placeholder="اشرح أهداف الجمعية ومجال عملها..."
                              ><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                </div>

                <!-- معلومات التحويل -->
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-bank me-1 text-success"></i>
                        معلومات التحويل المالي
                    </label>
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-1"></i>
                        هذه المعلومات ستظهر للمتبرعين لتحويل الأموال مباشرة إليكم
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">رقم الحساب البنكي</label>
                    <input type="text"
                           name="bank_account"
                           value="<?= htmlspecialchars($old['bank_account'] ?? '') ?>"
                           class="form-control"
                           placeholder="اختياري">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">حساب بريد الجزائر CCP</label>
                    <input type="text"
                           name="ccp_account"
                           value="<?= htmlspecialchars($old['ccp_account'] ?? '') ?>"
                           class="form-control"
                           placeholder="اختياري">
                </div>

                <!-- زر الحفظ -->
                <div class="col-12 d-flex justify-content-end mt-2">
                    <button type="submit" class="btn btn-success px-5">
                        <i class="bi bi-check-circle me-2"></i>
                        حفظ وإكمال الإعداد
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