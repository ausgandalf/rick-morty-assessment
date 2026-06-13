# Rick & Morty Encyclopedia

A Laravel + Vue encyclopedia for browsing Rick and Morty characters. Search and filter the multiverse, view character details with episodes, and monitor API health — all powered by the [Rick and Morty API](https://rickandmortyapi.com/).

## Features

- **Character list** — paginated grid with search and filters (name, status, species, gender, type)
- **Character details** — profile, origin, location, and episode list
- **Server status indicator** — live health check in the header (polls every minute)
- **Loading & error states** — spinner while searching, staggered card animations, friendly error messages
- **Response caching** — API responses cached server-side to reduce external calls

## Tech stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8.1+, Laravel 10 |
| Frontend | Vue 3, Inertia.js, Tailwind CSS |
| Build tool | Vite 5 |
| External API | [rickandmortyapi.com](https://rickandmortyapi.com/api) |

---

## Prerequisites

Install the following before you begin:

- **PHP** `>= 8.1` with extensions: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
- **Composer** — [getcomposer.org](https://getcomposer.org/)
- **Node.js** `>= 18` and **npm** — [nodejs.org](https://nodejs.org/)
- **Git**

> **Note:** A database is **not required** to browse characters. The encyclopedia uses the Rick and Morty API and file-based cache/sessions. MySQL is only needed if you want to use the included Laravel Breeze auth routes (`/login`, `/register`, etc.).

---

## Local setup

### 1. Clone the repository

```bash
git clone https://github.com/ausgandalf/rick-morty-assessment
cd rick-morty-assessment
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install JavaScript dependencies

```bash
npm install
```

### 4. Configure environment

Copy the example environment file and generate an application key:

```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and confirm these values (defaults are fine for local development):

```env
APP_NAME="Rick & Morty Encyclopedia"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

RICKANDMORTY_BASE_URL=https://rickandmortyapi.com/api
RICKANDMORTY_CACHE_TTL=600
```

| Variable | Description | Default |
|----------|-------------|---------|
| `RICKANDMORTY_BASE_URL` | Rick and Morty API base URL | `https://rickandmortyapi.com/api` |
| `RICKANDMORTY_CACHE_TTL` | Cache lifetime in seconds | `600` (10 minutes) |

> Make sure `RICKANDMORTY_BASE_URL` ends with `/api` — a typo here will break all character requests and show the server status as offline.

### 5. Prepare storage directories

Laravel needs writable storage and cache folders:

```bash
php artisan storage:link
```

On Linux/macOS, if you hit permission errors:

```bash
chmod -R 775 storage bootstrap/cache
```

---

## Running the application

You need **two terminal processes** running at the same time during development.

### Terminal 1 — Laravel backend

```bash
php artisan serve
```

The app will be available at **http://127.0.0.1:8000**.

To use a different port:

```bash
php artisan serve --port=8001
```

If you change the port, update `APP_URL` in `.env` to match (e.g. `http://127.0.0.1:8001`).

### Terminal 2 — Vite frontend (hot reload)

```bash
npm run dev
```

Vite compiles Vue components and enables hot module replacement. Keep this running while you work on the frontend.

### Open the app

Visit [http://127.0.0.1:8000](http://127.0.0.1:8000) in your browser.

| Route | Description |
|-------|-------------|
| `/` | Character list with search & filters |
| `/characters/{id}` | Character detail page |
| `/api/status` | JSON endpoint for API health check |

---

## Production build

For a production-like local test (no Vite dev server):

```bash
npm run build
php artisan serve
```

Assets are compiled into `public/build/`. You only need the Laravel server in this mode.

---

## Project structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── CharacterController.php   # List & detail pages
│   │   └── StatusController.php      # API health endpoint
│   └── Requests/
│       └── CharacterIndexRequest.php # Filter validation
├── Services/RickAndMorty/
│   ├── RickAndMortyApiClient.php     # HTTP client + caching
│   └── CharacterService.php          # Business logic
resources/js/
├── Layouts/
│   └── EncyclopediaLayout.vue        # App shell + status light
└── Pages/Characters/
    ├── Index.vue                     # List, filters, pagination
    └── Details.vue                   # Character profile
config/
└── rickandmorty.php                  # API URL, cache TTL, timeout
routes/
├── web.php                           # Character routes
└── api.php                           # Status route
```

---

## How it works

1. **Browser** loads an Inertia page served by Laravel.
2. **CharacterController** validates filters and calls **CharacterService**.
3. **RickAndMortyApiClient** fetches data from the external API, caching successful responses.
4. **Vue pages** render the data with loading states, error handling, and animations.

The header **Server Status** light calls `GET /api/status` every 60 seconds to check whether the Rick and Morty API is reachable.

---

## Troubleshooting

### Server status shows offline / no characters load

- Verify `RICKANDMORTY_BASE_URL` in `.env` is exactly `https://rickandmortyapi.com/api`
- After changing `.env`, restart the Laravel server
- Clear config cache if you previously ran `config:cache`:

```bash
php artisan config:clear
```

### Blank page or missing styles

- Ensure `npm run dev` is running (development), or run `npm run build` (production)
- If you stopped `npm run dev` but the page is still blank, delete the stale hot file so Laravel uses compiled assets:

```bash
rm public/hot
npm run build
```

- On Windows, use `del public\\hot` instead of `rm`
- Hard-refresh the browser (`Ctrl+Shift+R` / `Cmd+Shift+R`)

### `Vite manifest not found` or blank white page

This happens when Laravel cannot load frontend assets. Common causes:

1. **`npm run dev` is not running** and **`public/build/` does not exist** — run `npm run build`
2. **Stale `public/hot` file** — left over after stopping Vite; Laravel still tries the dev server. Delete it:

```bash
rm public/hot
```

3. **IPv6 dev server URL on Windows** — if scripts point to `[::1]:5173`, use `127.0.0.1` in `vite.config.js` (already configured) and restart `npm run dev`

### Permission errors on `storage/` or `bootstrap/cache/`

```bash
chmod -R 775 storage bootstrap/cache
```

### Stale data after API changes

Clear the application cache:

```bash
php artisan cache:clear
```

### Port already in use

```bash
php artisan serve --port=8001
```

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

