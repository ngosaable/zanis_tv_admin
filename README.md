# Zanis TV — Admin Panel & Streaming API

Zanis TV is a TV streaming platform built on **Laravel 10 (PHP 8.1+)**. This repository holds three pieces:

| Piece | Where | What it does |
|---|---|---|
| **Admin panel** | `/admin/...` (Blade views in `resources/views/admin`) | Staff upload videos, add live channels (HLS links), manage categories, home sliders, ads, users and roles. |
| **Public JSON API** | `/api/...` (`routes/api.php`) | Feeds the Flutter mobile/TV app and the public website. |
| **Public website** | `/` → `public/welcome.html` + `public/js/welcome.js` | Landing page that lists live channels and programmes and plays them in the browser with hls.js. |

### How video streaming works

1. An admin uploads a video (big files are sent in chunks: `videos/chunk-upload` → `videos/merge-chunks`).
2. The file is saved to `storage/app/public/videos/uploads/`.
3. A **queued job** (`app/Jobs/ProcessVideoJob.php`) runs **FFmpeg** (`app/Services/VideoProcessingService.php`) to make an **HLS** stream (`playlist.m3u8` + `.ts` segments) in `storage/app/public/videos/hls/<uuid>/`. "Adaptive" makes 720p/480p/360p plus a master playlist.
4. The API returns the `.m3u8` URL and the app or website plays it.

Live channels don't need FFmpeg. You paste an existing `.m3u8` stream URL into the admin panel.

---

## 1. What to install (Windows)

| Tool | Why | Get it |
|---|---|---|
| **XAMPP** (PHP 8.1+ with MySQL/MariaDB) | PHP + database | https://www.apachefriends.org |
| **Composer** | Installs the Laravel packages (`vendor/`) | https://getcomposer.org/download/ |
| **Node.js LTS** | Builds the admin CSS/JS (Tailwind + Vite) | https://nodejs.org |
| **FFmpeg** | Converts uploaded videos to HLS | https://www.gyan.dev/ffmpeg/builds/ (unzip to `C:\ffmpeg`) |

To check that everything is installed, open a **new** Command Prompt and run:

```bat
php -v
composer -V
node -v
ffmpeg -version
```

If `php` isn't found, add `C:\xampp\php` to your Windows **PATH**. If `ffmpeg` isn't found, either add `C:\ffmpeg\bin` to PATH or set the full path in `.env` (`FFMPEG_BINARY` / `FFPROBE_BINARY`).

### PHP settings for large uploads

Open `C:\xampp\php\php.ini`, set these values and restart Apache:

```ini
upload_max_filesize = 512M
post_max_size = 512M
max_execution_time = 600
memory_limit = 512M
```

Also check that these lines are **not** commented out (no `;` in front): `extension=pdo_mysql`, `extension=mbstring`, `extension=openssl`, `extension=fileinfo`, `extension=zip`, `extension=gd`.

---

## 2. First-time setup

1. Open the **XAMPP Control Panel** and **Start** Apache and MySQL.
2. Go to http://localhost/phpmyadmin, create a new database named **`zanis_tv`** and pick collation `utf8mb4_unicode_ci`.
3. Open a Command Prompt in the project folder:
   ```bat
   cd C:\xampp\htdocs\zanis_tv_admin
   ```
4. Create your `.env` from the template, then open it in Notepad:
   ```bat
   copy .env.example .env
   notepad .env
   ```
   Check the `DB_*` lines (XAMPP's defaults are user `root` with an empty password) and change `ADMIN_EMAIL` / `ADMIN_PASSWORD` to whatever you want your first login to be. `APP_KEY` is filled in automatically in the next step.
5. Run the setup script:
   ```bat
   setup.bat
   ```
   If you'd rather run the steps yourself, these are the same commands:
   ```bat
   composer install
   npm install
   npm run build
   php artisan migrate
   php artisan db:seed
   php artisan storage:link
   ```

## 3. Running it (every time)

You need **two** Command Prompt windows, both open in the project folder:

```bat
:: Window 1 – the web server
php artisan serve
```

```bat
:: Window 2 – background worker that converts uploaded videos to HLS
php artisan queue:work --timeout=3600
```

Then open:

- Public site: http://127.0.0.1:8000
- Admin login: http://127.0.0.1:8000/login, using the `ADMIN_EMAIL` / `ADMIN_PASSWORD` from `.env`
- API test: http://127.0.0.1:8000/api/live-channels

> If you're editing the admin CSS/JS, run `npm run dev` in a third window to get hot reload. Otherwise `npm run build` once is enough.

### Optional: serve through XAMPP Apache instead of `artisan serve`

Point the Apache document root (or a VirtualHost) at `C:\xampp\htdocs\zanis_tv_admin\public`, then set `APP_URL` in `.env` to the address you use. The site has to run from the **domain root** because `welcome.js` and the image URLs depend on it. `http://localhost/zanis_tv_admin/public` will not work correctly.

---

## 4. Suggested first steps in the admin panel

1. **Categories** → check the seeded ones (Movies, Series, Documentaries, News, Sports, Kids).
2. **Live Channels** → add a channel with an HLS URL. For testing you can use `https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8`.
3. **Videos** → upload a short MP4 with "Convert to HLS" ticked. Keep the `queue:work` window open and watch the progress bar.
4. **Sliders / Ads** → add home-screen banners for the app.
5. Open http://127.0.0.1:8000 to see the content on the public site.

---

## 5. API reference (for the Flutter app)

Base URL: `http://<your-pc-ip>:8000/api`. From an Android emulator, use `http://10.0.2.2:8000/api`. To reach the API from a phone, start the server with `php artisan serve --host=0.0.0.0` and set `APP_URL` to `http://<your-pc-ip>:8000`.

| Method | Endpoint | Returns |
|---|---|---|
| GET | `/categories` | Active categories |
| GET | `/movies` (`?category_id=`) · alias `/videos` | All published videos |
| GET | `/movies/{id}` · alias `/videos/{id}` | One video |
| GET | `/movies/category/{categoryId}` | Category + its HLS-ready videos |
| GET | `/videos/latest?limit=10` | Latest HLS-ready videos |
| GET | `/live-channels` · alias `/channels` | Live channels |
| GET | `/sliders` | Home-screen sliders |
| GET | `/ads` · `/ads/{position}` | Active ads |
| POST | `/login` (`email`, `password`) | `{ token, user }` (Sanctum token) |
| GET | `/user` *(Bearer token)* | Current user |
| POST | `/logout` *(Bearer token)* | Revokes tokens |

Image and video fields such as `poster` and `logo` are paths relative to `<APP_URL>/storage/`.

---

## 6. Roles

| Role | Admin panel access |
|---|---|
| `super_admin` | Yes |
| `content_manager` | Yes |
| `subscriber`, `premium_subscriber` | No. They're viewers, and anyone who registers at `/register` gets `subscriber`. |

If you forget the admin password, run `php artisan app:reset-admin-password`. It resets the **first** user's password to `admin123`, so change it right after you log in.

---

## 7. Troubleshooting

| Problem | Fix |
|---|---|
| `SQLSTATE[HY000] [2002]` / connection refused | MySQL isn't running in XAMPP, or `DB_*` in `.env` is wrong. |
| `Unknown database 'zanis_tv'` | Create it in phpMyAdmin (step 2.2). |
| `Vite manifest not found` | Run `npm install && npm run build`. |
| Images/posters show broken | Run `php artisan storage:link`, and make sure `APP_URL` matches the address in your browser. |
| Video stuck on "pending" | `php artisan queue:work` isn't running. |
| Video "failed" | Check `storage/logs/laravel.log`. Usually FFmpeg isn't found, so set `FFMPEG_BINARY` / `FFPROBE_BINARY` in `.env`. |
| Upload fails for big files | Raise the `php.ini` limits (section 1) and restart Apache / `artisan serve`. |
| Changed `.env` and nothing happened | `php artisan optimize:clear` |

---

## 8. Not built yet (models exist, but no database tables or screens)

`SubscriptionPlan`, `Payment`, `WatchHistory`, `VideoRating`, user favourites, and `Playlist`/`PlaylistItem` (these tables have no columns yet). The next steps toward a full subscription streaming service are migrations, admin screens and API endpoints for these.
