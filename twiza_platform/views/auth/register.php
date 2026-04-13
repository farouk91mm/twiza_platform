<?php ob_start(); ?>

<h4 class="fw-bold text-center mb-4">
    <i class="bi bi-person-plus text-success me-2"></i>
    إنشاء حساب جديد
</h4>

<!-- أزرار OAuth -->
<div class="d-grid gap-2 mb-3">
    <a href="<?= APP_URL ?>/auth/google"
       class="btn btn-google d-flex align-items-center justify-content-center gap-2 py-2">
        <img src="https://www.google.com/favicon.ico" width="18" alt="">
        التسجيل عبر Google
    </a>
    <a href="<?= APP_URL ?>/auth/facebook"
       class="btn btn-facebook d-flex align-items-center justify-content-center gap-2 py-2">
        <i class="bi bi-facebook"></i>
        التسجيل عبر Facebook
    </a>
</div>

<div class="divider"><span>أو بالبريد الإلكتروني</span></div>

<form method="POST" action="<?= APP_URL ?>/auth/register" novalidate>
    <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

    <!-- نوع الحساب -->
    <div class="mb-3">
        <label class="form-label fw-semibold">نوع الحساب</label>
        <div class="row g-2" id="accountTypeGroup">

            <div class="col-6">
                <input type="radio" class="btn-check"
                       name="user_type" id="type_individual"
                       value="individual"
                       <?= (($old['user_type'] ?? '') === 'individual') ? 'checked' : '' ?>>
                <label class="btn btn-outline-success w-100 py-3"
                       for="type_individual">
                    <i class="bi bi-person-fill d-block fs-4 mb-1"></i>
                    فرد
                </label>
            </div>

            <div class="col-6">
                <input type="radio" class="btn-check"
                       name="user_type" id="type_merchant"
                       value="merchant"
                       <?= (($old['user_type'] ?? '') === 'merchant') ? 'checked' : '' ?>>
                <label class="btn btn-outline-success w-100 py-3"
                       for="type_merchant">
                    <i class="bi bi-shop d-block fs-4 mb-1"></i>
                    تاجر / مقدم خدمة
                </label>
            </div>

        </div>
        <?php if (isset($errors['user_type'])): ?>
            <div class="text-danger small mt-1">
                <i class="bi bi-exclamation-circle me-1"></i>
                <?= $errors['user_type'] ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- الاسم الكامل -->
    <div class="mb-3">
        <label class="form-label fw-semibold">الاسم الكامل</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="full_name"
                   value="<?= htmlspecialchars($old['full_name'] ?? '') ?>"
                   class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>"
                   placeholder="الاسم الكامل">
            <?php if (isset($errors['full_name'])): ?>
                <div class="invalid-feedback"><?= $errors['full_name'] ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- حقول التاجر (تظهر فقط عند اختيار تاجر) -->
    <div id="merchantFields" class="<?= (($old['user_type'] ?? '') === 'merchant') ? '' : 'd-none' ?>">

        <div class="mb-3">
            <label class="form-label fw-semibold">اسم المحل / النشاط</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-shop"></i></span>
                <input type="text" name="shop_name"
                       value="<?= htmlspecialchars($old['shop_name'] ?? '') ?>"
                       class="form-control <?= isset($errors['shop_name']) ? 'is-invalid' : '' ?>"
                       placeholder="اسم المحل أو النشاط التجاري">
                <?php if (isset($errors['shop_name'])): ?>
                    <div class="invalid-feedback"><?= $errors['shop_name'] ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">نوع النشاط</label>
            <select name="activity_type"
                    class="form-select <?= isset($errors['activity_type']) ? 'is-invalid' : '' ?>">
                <option value="">اختر نوع النشاط</option>
                <option value="commerce"
                    <?= (($old['activity_type'] ?? '') === 'commerce') ? 'selected' : '' ?>>
                    تجارة / بيع منتجات
                </option>
                <option value="services"
                    <?= (($old['activity_type'] ?? '') === 'services') ? 'selected' : '' ?>>
                    خدمات مهنية
                </option>
                <option value="education"
                    <?= (($old['activity_type'] ?? '') === 'education') ? 'selected' : '' ?>>
                    تعليم / تدريب
                </option>
                <option value="health"
                    <?= (($old['activity_type'] ?? '') === 'health') ? 'selected' : '' ?>>
                    صحة / طب
                </option>
                <option value="crafts"
                    <?= (($old['activity_type'] ?? '') === 'crafts') ? 'selected' : '' ?>>
                    حرف يدوية
                </option>
                <option value="other"
                    <?= (($old['activity_type'] ?? '') === 'other') ? 'selected' : '' ?>>
                    أخرى
                </option>
            </select>
            <?php if (isset($errors['activity_type'])): ?>
                <div class="invalid-feedback"><?= $errors['activity_type'] ?></div>
            <?php endif; ?>
        </div>

    </div>
    <!-- نهاية حقول التاجر -->

    <!-- البريد الإلكتروني -->
    <div class="mb-3">
        <label class="form-label fw-semibold">البريد الإلكتروني</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email"
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                   placeholder="example@email.com" dir="ltr">
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback"><?= $errors['email'] ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- كلمة المرور -->
    <div class="mb-3">
        <label class="form-label fw-semibold">كلمة المرور</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="pass1"
                   class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                   placeholder="8 أحرف على الأقل">
            <button type="button" class="btn btn-outline-secondary"
                    onclick="togglePass('pass1','eye1')">
                <i class="bi bi-eye" id="eye1"></i>
            </button>
            <?php if (isset($errors['password'])): ?>
                <div class="invalid-feedback"><?= $errors['password'] ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- تأكيد كلمة المرور -->
    <div class="mb-4">
        <label class="form-label fw-semibold">تأكيد كلمة المرور</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" name="password_confirm" id="pass2"
                   class="form-control"
                   placeholder="أعد كتابة كلمة المرور">
            <button type="button" class="btn btn-outline-secondary"
                    onclick="togglePass('pass2','eye2')">
                <i class="bi bi-eye" id="eye2"></i>
            </button>
        </div>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-success btn-lg fw-bold py-2">
            <i class="bi bi-person-check me-2"></i>
            إنشاء الحساب
        </button>
    </div>
</form>

<hr>
<p class="text-center mb-0">
    لديك حساب؟
    <a href="<?= APP_URL ?>/auth/login" class="text-success fw-bold">
        سجّل دخولك
    </a>
</p>

<script>
// إظهار/إخفاء حقول التاجر
document.querySelectorAll('input[name="user_type"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const mf = document.getElementById('merchantFields');
        mf.classList.toggle('d-none', this.value !== 'merchant');
    });
});

// إظهار/إخفاء كلمة المرور
function togglePass(fieldId, iconId) {
    const f = document.getElementById(fieldId);
    const i = document.getElementById(iconId);
    f.type = f.type === 'password' ? 'text' : 'password';
    i.classList.toggle('bi-eye');
    i.classList.toggle('bi-eye-slash');
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth.php';
?>