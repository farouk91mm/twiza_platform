<?php

class MerchantController extends Controller
{
    public function dashboard(): void
    {
        $this->requireRole('merchant');

        echo '
        <!DOCTYPE html>
        <html lang="ar" dir="rtl">
        <head>
            <meta charset="UTF-8">
            <link rel="stylesheet" 
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
        </head>
        <body class="bg-light">
        <div class="container mt-5">
            <div class="alert alert-info text-center fs-4">
                ✅ مرحباً ' . htmlspecialchars(Session::get('user_name')) . '
                <br>
                <small class="fs-6">لوحة تحكم التاجر</small>
            </div>
            <div class="text-center">
                <a href="' . APP_URL . '/auth/logout" class="btn btn-danger">
                    تسجيل الخروج
                </a>
            </div>
        </div>
        </body>
        </html>';
    }
}