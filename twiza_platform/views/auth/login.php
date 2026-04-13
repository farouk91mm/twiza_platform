<?php
// نجمع محتوى الصفحة في buffer ثم نمرره للـ layout
ob_start();
?>

<h4 class="fw-bold text-center mb-4">
    <i class="bi bi-box-arrow-in-right text-success me-2"></i>
    تسجيل الدخول
</h4>

<!-- أزرار OAuth -->
<div class="d-grid gap-2 mb-3">
    <a href="<?= APP_URL ?>/auth/google"
       class="btn btn-google d-flex align-items-center justify-content-center gap-2 py-2">
        <img src="https://www.google.com/favicon.ico" width="18" alt="Google">
        متابعة عبر Google
    </a>
    <a href="<?= APP_URL ?>/auth/facebook"
       class="btn btn-facebook d-flex align-items-center justify-content-center gap-2 py-2">
        <i class="bi bi-facebook"></i>
        متابعة عبر Facebook
    </a>
</div>

<div class="divider"><span>أو بالبريد الإلكتروني</span></div>

<!-- نموذج تسجيل الدخول -->
<form method="POST" action="<?= APP_URL ?>/auth/login" novalidate>
    <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

    <!-- البريد الإلكتروني -->
    <div class="mb-3">
        <label class="form-label fw-semibold">البريد الإلكتروني</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email"
                   name="email"
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                   placeholder="example@email.com"
                   dir="ltr">
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback"><?= $errors['email'] ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- كلمة المرور -->
    <div class="mb-4">
        <label class="form-label fw-semibold">كلمة المرور</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password"
                   name="password"
                   id="passwordField"
                   class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                   placeholder="••••••••">
            <button type="button"
                    class="btn btn-outline-secondary"
                    onclick="togglePassword()">
                <i class="bi bi-eye" id="eyeIcon"></i>
            </button>
            <?php if (isset($errors['password'])): ?>
                <div class="invalid-feedback"><?= $errors['password'] ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- زر الدخول -->
    <div class="d-grid">
        <button type="submit" class="btn btn-success btn-lg fw-bold py-2">
            <i class="bi bi-box-arrow-in-right me-2"></i>
            دخول
        </button>
    </div>
</form>

<!-- رابط التسجيل -->
<hr>
<p class="text-center mb-0">
    ليس لديك حساب؟
    <a href="<?= APP_URL ?>/auth/register" class="text-success fw-bold">
        سجّل الآن
    </a>
</p>

<script>
function togglePassword() {
    const field = document.getElementById('passwordField');
    const icon  = document.getElementById('eyeIcon');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth.php';
?>