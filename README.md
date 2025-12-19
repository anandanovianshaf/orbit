<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

## Orbit Webservice (API) + PWA

Orbit sekarang punya:

1) **Webservice JSON (API)** untuk konsumsi client/PWA.
2) **PWA** (manifest + service worker) dengan mode offline sederhana.

### API Endpoints

Base URL:

- `/api/v1`

Endpoints:

- `GET /api/v1/posts?per_page=10`
- `GET /api/v1/posts/{slug}`
- `GET /api/v1/search?q=keyword&per_page=10`

Catatan:

- Ini masih **read-only**. Kalau nanti kamu mau endpoint private (create/edit/delete) via API, paling rapi pakai **Laravel Sanctum**.

### PWA Install

1) Jalankan app (Laravel) seperti biasa.
2) Buka di Chrome/Edge.
3) Klik tombol **Install** di address bar (atau menu browser → Install app).

### Test Offline Mode (yang kamu minta)

> Penting: **Service Worker hanya jalan di secure context** → **HTTPS** atau **http://localhost**.
> Kalau kamu akses via `http://orbit.test` (Not secure), Chrome **tidak akan mendaftarkan service worker** sehingga tab “Service workers” terlihat kosong.

#### Via Chrome DevTools (desktop)

1) Buka DevTools → tab **Application**
2) Masuk **Service Workers**
3) Pastikan service worker ter-register (scope `/`).
4) Centang **Offline**
5) Refresh halaman atau klik navigasi.

Expected:

- Kalau halaman pernah kebuka, harusnya masih bisa tampil dari cache.
- Kalau belum pernah kebuka / request gagal, akan diarahkan ke halaman **`/offline`**.

#### Kalau kamu pakai `orbit.test` (HTTP)

Pilih salah satu:

1) Jalankan app di `http://localhost` (paling gampang untuk test SW)
2) Aktifkan HTTPS untuk domain local kamu (misalnya via Herd/Valet + trusted certificate)

#### Via Mobile

1) Install PWA dulu.
2) Aktifkan airplane mode / matikan data.
3) Buka PWA.

### File penting PWA

- `public/manifest.webmanifest`
- `public/sw.js`
- `resources/views/offline.blade.php`

### Icon PWA

Saat ini icon PWA masih placeholder. Kamu bisa ganti file berikut dengan icon asli (PNG):

- `public/pwa/icon-192.png`
- `public/pwa/icon-512.png`
- `public/pwa/icon-maskable-512.png`

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
