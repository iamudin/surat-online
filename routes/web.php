<?php

// Public Landing Page
// Main domain route
add_route("public", [
    "name" => "portal.landing.main",
    "path" => "surat-online",


    "method" => "get",
    "function" => "landing",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "portal.landing.custom",
    "path" => "/",


    "method" => "get",
    "function" => "landing",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Auth & Registration Routes
// Main domain route
add_route("public", [
    "name" => "portal.login.main",
    "path" => "surat-online/login",


    "method" => "get",
    "function" => "showLogin",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "portal.login.custom",
    "path" => "login",


    "method" => "get",
    "function" => "showLogin",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Main domain route
add_route("public", [
    "name" => "portal.login.submit.main",
    "path" => "surat-online/login",


    "method" => "post",
    "function" => "login",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "portal.login.submit.custom",
    "path" => "login",


    "method" => "post",
    "function" => "login",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Main domain route
add_route("public", [
    "name" => "portal.login.otp.request.main",
    "path" => "surat-online/login/otp/request",


    "method" => "post",
    "function" => "requestOtp",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "portal.login.otp.request.custom",
    "path" => "login/otp/request",


    "method" => "post",
    "function" => "requestOtp",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Main domain route
add_route("public", [
    "name" => "portal.login.otp.verify.main",
    "path" => "surat-online/login/otp/verify",


    "method" => "post",
    "function" => "verifyOtp",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "portal.login.otp.verify.custom",
    "path" => "login/otp/verify",


    "method" => "post",
    "function" => "verifyOtp",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Main domain route
add_route("public", [
    "name" => "warga.register.main",
    "path" => "surat-online/register",


    "method" => "get",
    "function" => "showRegister",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "warga.register.custom",
    "path" => "register",


    "method" => "get",
    "function" => "showRegister",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Main domain route
add_route("public", [
    "name" => "warga.register.submit.main",
    "path" => "surat-online/register",


    "method" => "post",
    "function" => "register",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "warga.register.submit.custom",
    "path" => "register",


    "method" => "post",
    "function" => "register",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Main domain route
add_route("public", [
    "name" => "portal.logout.main",
    "path" => "surat-online/logout",
    "method" => "post",
    "function" => "logout",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "portal.logout.custom",
    "path" => "logout",
    "method" => "post",
    "function" => "logout",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalAuthController::class,
]);

// Dashboard & Profile
// Main domain route
add_route("public", [
    "name" => "portal.dashboard.main",
    "path" => "surat-online/dashboard",


    "method" => "get",
    "function" => "index",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalDashboardController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "portal.dashboard.custom",
    "path" => "dashboard",


    "method" => "get",
    "function" => "index",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalDashboardController::class,
]);

// Main domain route
add_route("public", [
    "name" => "portal.warga.upload_ktp.main",
    "path" => "surat-online/warga/upload-ktp",


    "method" => "post",
    "function" => "uploadKtp",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalDashboardController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "portal.warga.upload_ktp.custom",
    "path" => "warga/upload-ktp",


    "method" => "post",
    "function" => "uploadKtp",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalDashboardController::class,
]);

// Main domain route
add_route("public", [
    "name" => "portal.warga.update_profile.main",
    "path" => "surat-online/warga/profile/update",


    "method" => "post",
    "function" => "updateProfile",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalDashboardController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "portal.warga.update_profile.custom",
    "path" => "warga/profile/update",


    "method" => "post",
    "function" => "updateProfile",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalDashboardController::class,
]);

// Main domain route
add_route("public", [
    "name" => "portal.rt.verify_warga.main",
    "path" => "surat-online/rt/warga/{id}/verify",


    "method" => "post",
    "function" => "verifyWarga",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalDashboardController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "portal.rt.verify_warga.custom",
    "path" => "rt/warga/{id}/verify",


    "method" => "post",
    "function" => "verifyWarga",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalDashboardController::class,
]);

// Main domain route
add_route("public", [
    "name" => "portal.rt.surat_validate.main",
    "path" => "surat-online/rt/surat/{id}/validate",


    "method" => "post",
    "function" => "validateSurat",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalDashboardController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "portal.rt.surat_validate.custom",
    "path" => "rt/surat/{id}/validate",


    "method" => "post",
    "function" => "validateSurat",


    "method" => "post",
    "function" => "validateSurat",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalDashboardController::class,
]);

// Main domain route
add_route("public", [
    "name" => "portal.kades.sign_surat.main",
    "path" => "surat-online/kades/sign",


    "method" => "post",
    "function" => "kadesSignSurat",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalDashboardController::class,
]);

add_route('admin', [
    'title' => 'Cetak Laporan Permohonan Masuk',
    'name' => 'surat-requests.report',
    'path' => 'surat-online/surat-requests/report',
    'method' => 'get',
    'icon' => 'fa-print',
    'function' => 'report',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\SuratRequestController::class,
    'show_in_sidebar' => false,
]);

add_route('admin', [
    'title' => 'Permohonan Masuk',
    'name' => 'admin.surat.requests',
    'icon' => 'fa-envelope',
    'path' => 'surat-online/surat-requests',
    'method' => 'resource',
    'function' => 'index',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\SuratRequestController::class,
    'show_in_sidebar' => true,
]);

add_route('admin', [
    'title' => 'Permohonan Masuk',
    'name' => 'surat-requests.uploadFinal',
    'path' => 'surat-online/surat-requests/{id}/upload-final',
    'icon' => 'fa-envelope',
    'method' => ['post'],
    'function' => 'uploadFinal',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\SuratRequestController::class,
    'show_in_sidebar' => false,
]);

add_route('admin', [
    'title' => 'Jenis & Form Surat',
    'name' => 'admin.surat.types',
    'icon' => 'fa-cog',
    'path' => 'surat-online/surat-types',
    'method' => 'resource',
    'function' => 'index',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\SuratTypeController::class,
    'show_in_sidebar' => true,
]);

add_route('admin', [
    'title' => 'Simpan Field Surat',
    'name' => 'surat-types.storeField',
    'icon' => 'fa-cog',
    'path' => 'surat-online/surat-types/{typeId}/fields',
    'method' => 'post',
    'function' => 'storeField',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\SuratTypeController::class,
    'show_in_sidebar' => false,
]);

add_route('admin', [
    'title' => 'Update Field Surat',
    'name' => 'surat-types.updateField',
    'icon' => 'fa-cog',
    'path' => 'surat-online/surat-types/fields/{id}/update',
    'method' => 'post',
    'function' => 'updateField',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\SuratTypeController::class,
    'show_in_sidebar' => false,
]);

add_route('admin', [
    'title' => 'Pindah Posisi Field Surat',
    'name' => 'surat-types.moveField',
    'icon' => 'fa-cog',
    'path' => 'surat-online/surat-types/fields/{id}/move',
    'method' => 'post',
    'function' => 'moveField',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\SuratTypeController::class,
    'show_in_sidebar' => false,
]);

add_route('admin', [
    'title' => 'Hapus Field Surat',
    'name' => 'surat-types.destroyField',
    'icon' => 'fa-cog',
    'path' => 'surat-online/surat-types/fields/{id}/delete',
    'method' => 'post',
    'function' => 'destroyField',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\SuratTypeController::class,
    'show_in_sidebar' => false,
]);

add_route('admin', [
    'title' => 'Data Warga',
    'name' => 'admin.wargas',
    'icon' => 'fa-users',
    'path' => 'surat-online/wargas',
    'method' => 'resource',
    'function' => 'index',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\WargaController::class,
    'show_in_sidebar' => true,
]);

add_route('admin', [
    'title' => 'Blokir Warga',
    'name' => 'admin.wargas.toggleBlock',
    'icon' => 'fa-ban',
    'path' => 'surat-online/wargas/{id}/toggle-block',
    'method' => 'post',
    'function' => 'toggleBlock',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\WargaController::class,
    'show_in_sidebar' => false,
]);

add_route('admin', [
    'title' => 'Verifikasi Warga',
    'name' => 'admin.wargas.verify',
    'icon' => 'fa-check',
    'path' => 'surat-online/wargas/{id}/verify',
    'method' => 'post',
    'function' => 'verify',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\WargaController::class,
    'show_in_sidebar' => false,
]);

add_route('admin', [
    'title' => 'Aparat',
    'name' => 'admin.rws',
    'icon' => 'fa-map-signs',
    'path' => 'surat-online/rws',
    'method' => 'resource',
    'function' => 'index',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\RwController::class,
    'show_in_sidebar' => true,
]);

add_route('admin', [
    'title' => 'Data RT',
    'name' => 'admin.rts',
    'icon' => 'fa-map-marker-alt',
    'path' => 'surat-online/rts',
    'method' => 'resource',
    'function' => 'index',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\RtController::class,
    'show_in_sidebar' => false,
]);

add_route('admin', [
    'title' => 'Data Kepala Desa',
    'name' => 'admin.kades',
    'icon' => 'fa-user-tie',
    'path' => 'surat-online/kades',
    'method' => ['post', 'put', 'get'],
    'function' => 'index',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\KadesController::class,
    'show_in_sidebar' => false,
]);

add_route('admin', [
    'title' => 'Pengaturan Plugin',
    'name' => 'admin.surat.settings',
    'icon' => 'fa-cogs',
    'path' => 'surat-online/settings',
    'method' => 'get',
    'function' => 'index',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\SettingController::class,
    'show_in_sidebar' => true,
]);

add_route('admin', [
    'title' => 'Simpan Pengaturan Plugin',
    'name' => 'admin.surat.settings.store',
    'icon' => 'fa-cogs',
    'path' => 'surat-online/settings',
    'method' => 'post',
    'function' => 'store',
    'controller' => \App\Http\Controllers\Plugins\SuratOnline\Admin\SettingController::class,
    'show_in_sidebar' => false,
]);

// Custom domain route
add_route("public", [
    "name" => "portal.kades.sign_surat.custom",
    "path" => "kades/sign",


    "method" => "post",
    "function" => "kadesSignSurat",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\PortalDashboardController::class,
]);

// Warga Surat Request
// Main domain route
add_route("public", [
    "name" => "warga.form.get.main",
    "path" => "surat-online/form/{slug}",


    "method" => "get",
    "function" => "getForm",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\WargaSuratController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "warga.form.get.custom",
    "path" => "form/{slug}",


    "method" => "get",
    "function" => "getForm",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\WargaSuratController::class,
]);

// Main domain route
add_route("public", [
    "name" => "warga.form.store.main",
    "path" => "surat-online/form/{slug}",


    "method" => "post",
    "function" => "storeRequest",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\WargaSuratController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "warga.form.store.custom",
    "path" => "form/{slug}",


    "method" => "post",
    "function" => "storeRequest",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\WargaSuratController::class,
]);

// Main domain route
add_route("public", [
    "name" => "warga.surat.download.main",
    "path" => "surat-online/surat/{id}/download",


    "method" => "get",
    "function" => "downloadSurat",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\WargaSuratController::class,
]);

// Custom domain route
add_route("public", [
    "name" => "warga.surat.download.custom",
    "path" => "surat/{id}/download",


    "method" => "get",
    "function" => "downloadSurat",
    "controller" => \App\Http\Controllers\Plugins\SuratOnline\Frontend\WargaSuratController::class,
]);


