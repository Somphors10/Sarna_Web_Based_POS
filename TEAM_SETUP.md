# WBPOS — Team Environment Setup Guide

**Document version:** 1.0  
**Project:** WBPOS (Multi-tenant Point of Sale)  
**Stack:** PHP 8.2 · CodeIgniter 4.6 · MySQL · Docker / XAMPP  

---

## 1. Purpose of this document

This guide explains what each team member must do after cloning or pulling the WBPOS project from Git, so everyone runs the **same application** with the **same database** and **same demo data**.

Follow **one** setup method:

- **Method A — Docker** (recommended; easiest and identical on all computers)
- **Method B — XAMPP** (matches local development on Windows with Apache + MySQL)

---

## 2. What is included in Git vs. what you must create locally

Git **includes** source code, migrations, SQL dump, Docker files, example environment files, and **payment QR images** under `public/images/payment/`.

Git **does not include** these — you must create them on your own machine:

| File / folder | Action required |
|---------------|-----------------|
| `.env` | Copy from `.env.example` (XAMPP setup) |
| `.env.docker` | Copy from `.env.docker.example` (Docker setup) |
| `vendor/` | Run `composer install` |
| `node_modules/` | Run `npm install` (XAMPP only) |
| `writable/cache/*` | Created automatically when the app runs |

**Never commit** `.env`, `.env.docker`, `vendor/`, or `node_modules/` to Git.

---

## 3. System requirements

### Method A — Docker

| Requirement | Version / notes |
|-------------|-----------------|
| Docker Desktop | Latest (Windows or Mac) |
| Git | Any recent version |
| Free disk space | ~2 GB for images and database |
| Port | **8080** must be free |

### Method B — XAMPP

| Requirement | Version / notes |
|-------------|-----------------|
| XAMPP | PHP **8.2+**, MySQL enabled |
| Composer | 2.x — https://getcomposer.org/ |
| Node.js + npm | For building CSS/JS assets |
| Git | Any recent version |
| Project path | `C:\xampp8\htdocs\opensourcepos` (or equivalent) |

---

## 4. Method A — Docker setup (recommended)

### 4.1 First-time setup

**Step 1 — Get the code**

```powershell
git clone <your-repository-url>
cd opensourcepos
```

If you already cloned before:

```powershell
git pull
```

**Step 2 — Create the Docker environment file**

```powershell
copy .env.docker.example .env.docker
```

You only do this once. Do not upload `.env.docker` to Git.

**Step 3 — Build and start containers**

```powershell
docker compose -f docker-compose.wbpos.yml up -d --build
```

- First run may take **10–15 minutes** (image build + database import).
- MySQL loads demo data from `app/Database/wbpos.sql` automatically.

**Step 4 — Open the application**

| Page | URL |
|------|-----|
| Landing page | http://localhost:8080/ |
| Shop login | http://localhost:8080/login |
| Super Admin login | http://localhost:8080/super-admin/login |

**Step 5 — Stop or restart (optional)**

```powershell
# Stop containers
docker compose -f docker-compose.wbpos.yml down

# Start again (no rebuild)
docker compose -f docker-compose.wbpos.yml up -d

# Rebuild after code changes
docker compose -f docker-compose.wbpos.yml up -d --build
```

### 4.2 After every `git pull` (Docker)

```powershell
git pull
docker compose -f docker-compose.wbpos.yml up -d --build
```

Docker now runs `npm run build` inside the image, so CSS/JS are included automatically.

Hard-refresh the browser with **Ctrl + F5** if styles look outdated.

---

## 5. Method B — XAMPP setup

### 5.1 First-time setup

**Step 1 — Get the code**

```powershell
git clone <your-repository-url>
cd opensourcepos
```

Or:

```powershell
git pull
```

**Step 2 — Place project in XAMPP**

- Put the folder at: `C:\xampp8\htdocs\opensourcepos`
- Open **XAMPP Control Panel**
- Start **Apache** and **MySQL**

**Step 3 — Create the environment file**

```powershell
copy .env.example .env
```

Default database settings in `.env`:

- Host: `localhost`
- Database: `wbpos`
- Username: `root`
- Password: *(empty)*
- Table prefix: `wbpos_`

Change these only if your MySQL setup is different.

**Step 4 — Create and import the database**

1. Open http://localhost/phpmyadmin  
2. Create a new database named **`wbpos`**  
3. Select `wbpos` → **Import**  
4. Choose file: `app/Database/wbpos.sql`  
5. Click **Go** and wait until import finishes  

**Step 5 — Install dependencies and build assets**

```powershell
composer install
npm install
npm run build
```

**Step 6 — Open the application**

| Page | URL |
|------|-----|
| Landing page | http://localhost/opensourcepos/public/ |
| Shop login | http://localhost/opensourcepos/public/login |
| Super Admin login | http://localhost/opensourcepos/public/super-admin/login |

### 5.2 After every `git pull` (XAMPP)

**Easy way — run the setup script:**

```powershell
git pull
powershell -ExecutionPolicy Bypass -File scripts/setup-after-pull.ps1
```

**Or run these commands manually:**

```powershell
git pull
composer install
npm install
npm run build
```

If teammates added database changes, run any new SQL files in:

`app/Database/Migrations/sqlscripts/`

Hard-refresh the browser with **Ctrl + F5** if styles look outdated.

---

## 6. Demo login accounts

After importing `wbpos.sql`, everyone uses the same accounts:

| Role | Username | Password |
|------|----------|----------|
| Shop admin | `admin` | `pointofsale` |
| Super Admin | `superadmin` | `ChangeMe123!` |

**Sample tenants in database:** `default`, `blue-ocean`

---

## 7. Verify your setup matches the team

Use this checklist after setup:

- [ ] Landing page loads without errors  
- [ ] Shop login works (`admin` / `pointofsale`)  
- [ ] Dashboard shows KPI cards and charts  
- [ ] Sales flow works: add item → **Receipt** mode → **Add Payment** → **Complete**  
- [ ] Super Admin login works  
- [ ] Subscription price shows **$20/month** on landing/register pages  

### Important demo notes

- Use **Receipt** mode in Sales (not Invoice — Invoice requires a customer).  
- A sale is only complete after **Add Payment** and then **Complete**.  
- Login has **no company code field** — usernames are globally unique.  

---

## 8. Troubleshooting

| Problem | Solution |
|---------|----------|
| **500 Internal Server Error** (Docker) | Run `docker compose -f docker-compose.wbpos.yml up -d --build` |
| **500 or blank page** (XAMPP) | Confirm `.env` exists, database is `wbpos`, prefix is `wbpos_`, Apache/MySQL are running |
| **Missing CSS / broken layout** | Run `powershell -ExecutionPolicy Bypass -File scripts/setup-after-pull.ps1` then Ctrl+F5. Check URL includes `/public/` on XAMPP: `http://localhost/opensourcepos/public/login` |
| **CSS files 404 in browser (F12 → Network)** | `public/resources/` is missing — run `npm install` then `npm run build`. Folder should have ~100+ files after build. |
| **QR code missing on register page** | 1) `git pull` 2) Check file exists: `public/images/payment/aba-khqr-code.png` 3) Docker: `docker compose -f docker-compose.wbpos.yml up -d` (images folder is mounted from your PC) 4) XAMPP: open `http://localhost/opensourcepos/public/images/payment/aba-khqr-code.png` — should show the image |
| **Class not found / PHP errors** | Run `composer install` |
| **Login fails / empty data** | Re-import `app/Database/wbpos.sql` |
| **Port 8080 already in use** (Docker) | Close the other app or change the port in `docker-compose.wbpos.yml` |
| **Dashboard shows $0 after sale** | Complete sale with Receipt mode + Add Payment + Complete |

### Reset Docker database (fresh start)

```powershell
docker compose -f docker-compose.wbpos.yml down -v
docker compose -f docker-compose.wbpos.yml up -d --build
```

This deletes the Docker MySQL volume and re-imports `wbpos.sql`.

---

## 9. Quick reference — commands

| Task | Docker | XAMPP |
|------|--------|-------|
| First env file | `copy .env.docker.example .env.docker` | `copy .env.example .env` |
| Start app | `docker compose -f docker-compose.wbpos.yml up -d --build` | Start Apache + MySQL in XAMPP |
| After git pull | `docker compose -f docker-compose.wbpos.yml up -d --build` | `powershell -ExecutionPolicy Bypass -File scripts/setup-after-pull.ps1` |
| Shop URL | http://localhost:8080/login | http://localhost/opensourcepos/public/login |

---

## 10. Contact

If setup still fails after following this document, share:

1. Which method you use (Docker or XAMPP)  
2. The exact error message or screenshot  
3. Output of the command that failed  

---

*End of document*
