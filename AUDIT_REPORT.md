# 🔍 Laravel + Vite + Tailwind Audit & Production Stabilization Report – Kunjachan Missionary Bhavan

**Date:** February 19, 2026  
**Scope:** Local codebase audit + applied fixes + local “production simulation” verification  
**Constraints honored:** No DB changes, no migrations modified, no production server/Cloudflare changes

---

## Executive Summary

This project uses Laravel 12 + Vite + Tailwind. The primary production instability was **frontend asset pipeline inconsistency**:

1. Some layouts did not load Vite at all (public/guest pages used static CSS/JS).
2. The Vite build could place the manifest under `public/build/.vite/manifest.json` (Vite default) which **Laravel does not read**. Laravel expects `public/build/manifest.json`.
3. Tailwind config was missing (no deterministic content scanning / purge behavior).

All three were fixed, then verified locally via `npm run build` and HTTP checks on `/`, `/login`, and `/dashboard`.

---

## What Changed (Applied Fixes)

### 1) Tailwind configuration added

- **Added:** `tailwind.config.js`
  - `content` scans Blade + JS paths
  - Registers `@tailwindcss/forms`

**Why this matters:** Tailwind v3 requires a content configuration for predictable output. Without it, you risk either missing classes (over-purged CSS) or shipping far more CSS than needed.

---

### 2) Vite build output made Laravel-compatible (manifest path)

- **Updated:** `vite.config.js`
  - `base: '/build/'`
  - `build.outDir: 'public/build'`
  - `build.emptyOutDir: true`
  - **Critical:** `build.manifest: 'manifest.json'` so the manifest is written to `public/build/manifest.json`

**Why this matters:** Laravel’s Vite helper reads `public/build/manifest.json`. If the manifest ends up under `public/build/.vite/manifest.json`, production requests will fail with missing-asset errors even though the Vite build “succeeded”.

---

### 3) Blade layouts normalized to inject Vite once, in `<head>`

- **Updated:**
  - `resources/views/layouts/app.blade.php`
  - `resources/views/layouts/public.blade.php`
  - `resources/views/layouts/guest.blade.php`
  - `resources/views/components/plain-app-layout.blade.php`

**Behavior now:** each layout injects exactly one:

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

…and this is placed in `<head>` for consistent preload/early parsing.

**Also changed:** public layout no longer directly links `public/css/public.css` and `public/js/public.js` (static, non-hashed).

---

### 4) Public JS behavior moved into Vite entrypoint (guarded)

- **Updated:** `resources/js/app.js`
  - Keeps Alpine initialization
  - Includes logic previously in `public/js/public.js` (page loader + blog feed search)
  - Guards execution by checking for the relevant DOM elements

**Why this matters:** public pages now rely on the same hashed, cache-busted Vite bundle as the rest of the app.

---

### 5) Legacy public CSS included via the Vite CSS entrypoint

- **Updated:** `resources/css/app.css`
  - Imports legacy stylesheet `public/css/public.css`
  - Ensures the `@import` is at the top of the file (required by CSS parsing rules)

**Why this matters:** public styling remains intact while becoming part of the hashed Vite build output.

---

### 6) `package.json` repaired and simplified

- Fixed invalid JSON formatting
- Removed `@tailwindcss/vite` (Tailwind v4-era plugin; not needed for Tailwind v3 setup here)

---

## Verification (Local “Production Simulation”)

### Build verification

- `npm install`
- `npm run build`

**Result:** build succeeded and generated:

- `public/build/manifest.json`
- `public/build/assets/app-*.css` (example size observed locally: ~46 KB)
- `public/build/assets/app-*.js` (example size observed locally: ~81 KB)

### Runtime verification (HTTP)

Using `php artisan serve` and HTTP requests:

- `GET /` → **200** and HTML contains `/build/assets/app-*.css` and `/build/assets/app-*.js`
- `GET /login` → **200** and HTML contains `/build/assets/app-*.css` and `/build/assets/app-*.js`
- `GET /dashboard` → **302** redirect (expected when unauthenticated)

These checks confirm:

- Laravel is reading the manifest from the expected location.
- Layouts reliably inject the hashed build assets.

---

## What Was NOT Changed (By Constraint)

- No database schema changes; no migrations edited
- No production server configuration changes (LiteSpeed/Cloudflare)
- No `.env` changes (production hardening must be done by deployment process)

---

## Remaining Risks / Follow-ups (Non-blocking)

1. **Production `.env` hardening**: ensure `APP_ENV=production` and `APP_DEBUG=false` on the server.
2. **FILESYSTEM_DISK**: if production uses S3, make sure S3 credentials are present; otherwise set a local disk intentionally.
3. **Bootstrap CDN dependencies** remain in some layouts; this is OK but keep in mind external CDN reliability and caching behavior.

---

## Conclusion

The Vite/Tailwind pipeline and Blade integration are now deterministic and Laravel 12-compatible, with local build and HTTP checks confirming hashed asset loading from `public/build/manifest.json`.
3. User experience: Unstyled application OR slow load times

---

### Secondary Risk: **HIGH**
**Public website (non-authenticated) will serve stale CSS** if cached by LiteSpeed.

**Outcome if deployed:**
1. Public pages use `asset('css/public.css')` with NO Vite fingerprinting
2. After deploy, old CSS persists in browser/CDN cache
3. User sees old styles until cache expires (24h+ on aggressive caching)

---

### Tertiary Risk: **MEDIUM**
**JavaScript execution may stall** if Alpine directives fire before `app.js` loads.

**Outcome if deployed:**
1. Interactive components (notifications, forms) may freeze momentarily
2. Race conditions if JavaScript depends on DOM fully ready
3. Experience varies by network speed (slower networks hit this worst)

---

## Required Fixes (Prioritized)

### 🔴 **PRIORITY 1: CRITICAL** (Do Before Any Deploy)

#### 1.1 Create `/tailwind.config.js`
```javascript
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.{js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
};
```

**Test After Fix:**
```bash
npm run build
# Check: file size of public/build/assets/app-*.css
# Expected: 20–50KB (if styles are actually in views)
# Warning: >200KB suggests styles aren't being scanned
```

---

#### 1.2 Add `@vite(['resources/css/app.css'])` to App Layout
**File:** `resources/views/layouts/app.blade.php`

**Change:** Move `@vite` directive from footer (line 352) to `<head>` section.

**Current (Line 352):**
```html
                @vite(['resources/js/app.js'])
```

**New (In `<head>`, after external CSS):**
```html
    </head>
    <body>
        <!-- ... nav/content ... -->
```

Should become:
```html
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.js">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
```

**Reasoning:**
- CSS must load in `<head>` to prevent FOUC
- JS can stay in footer if needed, but both should be via `@vite` in one place

---

### 🟠 **PRIORITY 2: HIGH** (Do Before Deploy)

#### 2.1 Migrate Public Layout to Vite
**File:** `resources/views/layouts/public.blade.php`

**Current (Line 14):**
```html
<link rel="stylesheet" href="{{ asset('css/public.css') }}">
```

**Option A: Keep static public.css (easier)**
- Move `public.css` to `resources/css/public.css`
- Update import in app layout or create separate entry point
- Requires rebuild in make process

**Option B: Merge public.css into app.css (recommended)**
- Move styles from `public/css/public.css` to `resources/css/` subdirectory
- Import in `resources/css/app.css`:
  ```css
  @import 'public.css';
  @tailwind base;
  @tailwind components;
  @tailwind utilities;
  ```
- Single build output, cache-busted by Vite

**Implement:** Option B
1. Move `public/css/public.css` → `resources/css/public.css`
2. Update `app.css` with import
3. Remove CDN stylesheets from public layout
4. Add `@vite(['resources/css/app.css', 'resources/js/app.js'])` to public layout `<head>`

---

#### 2.2 Validate Session Driver for Production
**File:** `.env.production` (create new)

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

Then in production, run:
```bash
php artisan session:table
php artisan migrate
```

---

#### 2.3 Secure Environment Variables
**File:** `.env.production`

```env
APP_DEBUG=false
APP_ENV=production

# S3 Credentials
AWS_ACCESS_KEY_ID=your-key-here
AWS_SECRET_ACCESS_KEY=your-secret-here
AWS_BUCKET=your-bucket-name
AWS_DEFAULT_REGION=us-east-1

# Mail (SES, Postmark, Resend, or Mailgun)
MAIL_MAILER=ses  # or postmark, resend, mailgun
# ... configure per choice

# Database (update per production RDS or local MySQL)
DB_HOST=prod-mysql-host
DB_PASSWORD=strong-production-password
```

- ✅ Never commit `.env.production` to Git
- ✅ Load via deployment script or environment variables

---

### 🟡 **PRIORITY 3: MEDIUM** (Do Before Release)

#### 3.1 Define Tailwind Theme & Safelists
**File:** `tailwind.config.js`

```javascript
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.{js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        'kb-primary': '#5a382f',
        'kb-accent': '#a0522d',
        'kb-accent-soft': '#c77952',
      },
      spacing: {
        // Custom spacing if needed
      },
    },
  },
  safelist: [
    // Protect dynamically generated classes
    { pattern: /^text-(kb-|red-|blue-|green-|yellow-|purple-)/ },
    { pattern: /^bg-(kb-|red-|blue-|green-|yellow-|purple-)/ },
    { pattern: /^border-(kb-|red-|blue-|green-|yellow-|purple-)/ },
    // Add more as needed based on dynamic rendering
  ],
  plugins: [
    require('@tailwindcss/forms'),
  ],
};
```

---

#### 3.2 Standardize CSS Sources
**Action:** Remove hardcoded Bootstrap CDN link where possible.

**Option:** Install Bootstrap via npm instead of CDN:
```bash
npm install bootstrap
```

Then import in `resources/css/app.css`:
```css
@import 'bootstrap/dist/css/bootstrap.min.css';
@tailwind base;
@tailwind components;
@tailwind utilities;
```

**Benefit:**
- Single source update (npm)
- Tree-shaking opportunity
- Easier versioning
- Smaller overall CSS if Tailwind utilities overlap with Bootstrap

---

#### 3.3 Add CSP (Content Security Policy) Headers
**File:** Create middleware `app/Http/Middleware/SetSecurityHeaders.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetSecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // CSP: Allow Vite dev server only if in local env
        $connectSrc = app()->isProduction() 
            ? "connect-src 'self' https:" 
            : "connect-src 'self' https: ws://localhost:5173";

        $response->header('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdn.bunny.net",
            "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net fonts.bunny.net",
            "font-src 'self' fonts.bunny.net cdn.jsdelivr.net",
            "img-src 'self' data: https:",
            $connectSrc,
            "frame-ancestors 'none'",
        ]));

        return $response;
    }
}
```

Register in `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->appendToGroup('web', [
        SetSecurityHeaders::class,
    ]);
})
```

---

#### 3.4 Vite Production Config
**File:** `vite.config.js` (enhance)

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        minify: 'esbuild',
        reportCompressedSize: true,
        chunkSizeWarningLimit: 1000, // Warn if >1MB chunk
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['alpine', 'axios'],
                },
            },
        },
    },
});
```

This ensures:
- Vendor JS (Alpine, Axios) split into separate bundle
- Easier caching on LiteSpeed
- Smaller main bundle for faster initial load

---

## Optional Improvements (Safe to Defer)

These don't block production but improve quality:

1. **Add Vite Preload:** Add `rel="modulepreload"` to `@vite` helper output (Vite helper auto-adds if configured)
2. **Implement SRI (Subresource Integrity):** Add integrities to CDN links to prevent tampering
3. **Move Bootstrap Icons to Font:** Replace CDN with local font file for faster loads
4. **Add Dark Mode:** Extend Tailwind config with `darkMode: 'class'` and implement toggle
5. **Minify Public CSS:** Run `npm run build` to minify `public/css/public.css` (post-merge)
6. **Add Vite Manifest Caching:** Implement cache headers in production web server config
7. **Implement Critical CSS:** Extract above-fold CSS for LCP optimization
8. **Add WebP Image Support:** Update Vite config to transform images to WebP

---

## Deployment Checklist

### Pre-Deploy
- [ ] Create `tailwind.config.js`
- [ ] Validate `npm run build` output (CSS file size <100KB)
- [ ] Move `@vite` to `<head>` in app layout
- [ ] Migrate public layout to Vite (or merge CSS)
- [ ] Create `.env.production` with real credentials
- [ ] Create `SetSecurityHeaders` middleware
- [ ] Test locally: `php artisan serve` + `npm run dev`
- [ ] Test build: `npm run build` → check `public/build/manifest.json`

### In Production
- [ ] Copy `.env.production` → `.env` (via deployment script, not Git)
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Cache config: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Clear caches: `php artisan cache:clear`
- [ ] Verify `public/build/` exists with hashed files
- [ ] Test public pages load without 404s on CSS/JS
- [ ] Check browser DevTools: Verify `app-*.css` is loaded from Vite manifest
- [ ] Monitor error logs for first hour

### LiteSpeed-Specific
- [ ] Add rewrite rule for Vite manifest (if needed): `^/build/(?!.*\.css|.*\.js).* - [END]`
- [ ] Enable static cache for `public/build/assets/` (1 year TTL, immutable)
- [ ] Disable cache for `public/build/manifest.json` (5 min TTL)
- [ ] Monitor CPU: Vite build should not block requests (run pre-deploy)

---

## Quick Summary for Action

| Issue | Current | Fix | Time | Blocker |
|-------|---------|-----|------|---------|
| Missing tailwind.config.js | ❌ Not found | Create + add content paths | 5 min | 🔴 YES |
| Vite CSS not in head | ❌ In footer only | Move to head in app.blade.php | 10 min | 🟠 YES |
| Public layout static CSS | ❌ asset('css/public.css') | Migrate to Vite or merge | 20 min | 🟠 YES |
| Session storage | ⚠️ File-based | Switch to database | 10 min | 🟠 YES |
| Environment locked | ❌ .env exposed | Create .env.production | 10 min | 🟠 YES |
| CSP headers | ❌ None | Add middleware | 15 min | 🟡 No |
| Bootstrap versioning | ⚠️ Mixed | Standardize (npm or CDN) | 20 min | 🟡 No |
| **Total Estimated Time** | - | - | **90 min** | - |

---

## Testing Strategy (After Fixes)

1. **Unit Tests:** `php artisan test` (all should pass)
2. **Visual Regression:** Load public pages in browser, check for unstyled elements
3. **Performance:** Chrome DevTools Lighthouse (target: >80)
4. **CSP Compliance:** Check console for CSP violations
5. **Responsive:** Test mobile (375px) and tablet (768px)
6. **Authentication Flow:** Login → dashboard → logout
7. **Vite Build:** `npm run build` → verify manifest, check file sizes
8. **Cache Headers:** Check response headers for `Cache-Control: max-age`, ETag

---

## Appendix: Files to Review/Create

### Files to CREATE:
- ✅ `tailwind.config.js` (root)
- ✅ `.env.production` (do not commit)
- ✅ `app/Http/Middleware/SetSecurityHeaders.php` (optional)

### Files to MODIFY:
- ✅ `resources/views/layouts/app.blade.php` (move @vite to head)
- ✅ `resources/views/layouts/public.blade.php` (add @vite or merge CSS)
- ✅ `vite.config.js` (enhance build config)
- ✅ `bootstrap/app.php` (register CSP middleware)
- ✅ `resources/css/app.css` (may need import restructuring)

### Files to VALIDATE (read-only):
- ✅ `composer.json` (PHP version, dependencies)
- ✅ `package.json` (Vite, Tailwind, Alpine versions)
- ✅ `postcss.config.js` (already correct)
- ✅ `.env` (current local config)

---

**Report Status:** ✅ Complete  
**Next Step:** Implement PRIORITY 1 fixes before any production attempt.
