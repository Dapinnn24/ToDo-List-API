# ToDo List API — Laravel

Sistem manajemen tugas berbasis REST API yang dibangun menggunakan Laravel dengan Laravel Sanctum untuk autentikasi, Form Request untuk validasi input, dan API Resource untuk konsistensi format response JSON.

---

## Alur Sistem

Secara umum, alur request dalam sistem ini adalah sebagai berikut:

```
Client Request
   ↓
Route (api.php)
   ↓
Controller
   ↓
Form Request (validasi input)
   ↓
Model (interaksi database)
   ↓
Resource (format response JSON)
   ↓
Response { success, message, data }
```

---

## Penjelasan Alur Task

Ketika user melakukan request ke endpoint task:

1. Request masuk dan divalidasi oleh **Form Request**
2. Middleware `auth:sanctum` memverifikasi token user
3. Controller mengambil data **hanya milik user yang sedang login** (`user_id`)
4. Data dikembalikan melalui **TaskResource** dalam format JSON yang konsisten

### Aturan Keamanan

- Setiap task hanya bisa diakses oleh user pemiliknya
- User lain yang mencoba mengakses task milik user lain akan mendapat response `404`
- Endpoint public `/api/public/tasks` dapat diakses tanpa token

---

## Pendekatan Arsitektur

| Layer | File | Tanggung Jawab |
|---|---|---|
| Controller | `AuthController`, `TaskController`, `PublicTaskController` | Menangani request dan response |
| Form Request | `StoreTaskRequest`, `UpdateTaskRequest` | Validasi input |
| Model | `User`, `Task` | Interaksi database dan relasi |
| Resource | `TaskResource` | Format output JSON yang konsisten |

---

## Teknologi yang Digunakan

- **Laravel** (PHP) sebagai backend framework
- **MySQL** sebagai database
- **Laravel Sanctum** untuk autentikasi berbasis token
- **REST API** dengan format JSON

---

## Setup

```bash
# 1. Install dependencies
composer install

# 2. Copy env dan generate key
cp .env.example .env
php artisan key:generate

# 3. Konfigurasi database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todo_api
DB_USERNAME=root
DB_PASSWORD=

# 4. Jalankan migrasi
php artisan migrate

# 5. Aktifkan route API di bootstrap/app.php
# Tambahkan: api: __DIR__.'/../routes/api.php'

# 6. Jalankan server
php artisan serve
```

---

## Daftar Endpoint

### Guest (tidak perlu token)

| Method | URI | Deskripsi |
|---|---|---|
| `POST` | `/api/register` | Registrasi user baru |
| `POST` | `/api/login` | Login dan dapatkan token |
| `GET` | `/api/public/tasks` | Daftar task yang bersifat publik |

### Authenticated (wajib Bearer Token)

| Method | URI | Deskripsi |
|---|---|---|
| `POST` | `/api/logout` | Logout dan hapus token |
| `GET` | `/api/tasks` | Daftar task milik user login |
| `GET` | `/api/tasks?status=pending` | Filter task berdasarkan status |
| `POST` | `/api/tasks` | Buat task baru |
| `GET` | `/api/tasks/{id}` | Detail task milik user |
| `PUT/PATCH` | `/api/tasks/{id}` | Update task |
| `DELETE` | `/api/tasks/{id}` | Hapus task |
| `PATCH` | `/api/tasks/{id}/done` | Tandai task selesai |

---

## Format Response

Semua response menggunakan format yang konsisten:

```json
{
    "success": true,
    "message": "Pesan deskriptif",
    "data": { }
}
```

### Contoh Response Sukses

```json
{
    "success": true,
    "message": "Task berhasil dibuat.",
    "data": {
        "id": 1,
        "title": "Tugas Projek PKL",
        "description": "Membuat Todo List API menggunakan Laravel dan Sanctum",
        "status": "pending",
        "due_date": "2026-05-01",
        "is_public": true,
        "created_at": "2026-04-02 05:08:21",
        "updated_at": "2026-04-02 05:08:21"
    }
}
```

### Contoh Response Error

```json
{
    "success": false,
    "message": "Task tidak ditemukan.",
    "data": null
}
```

---

## Cara Pengujian (Postman)

### Langkah 1 — Jalankan server

```bash
php artisan serve
```

Base URL: `http://127.0.0.1:8000`

---

### Langkah 2 — Register

```
POST /api/register

Body:
{
    "name": "Davin",
    "email": "davin@mail.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

✅ Copy nilai `token` dari response.

---

### Langkah 3 — Set Header di Postman

Untuk semua request yang butuh autentikasi, tambahkan header berikut:

```
Authorization : Bearer {token_kamu}
Accept        : application/json
Content-Type  : application/json
```

---

### Langkah 4 — Login

```
POST /api/login

Body:
{
    "email": "davin@mail.com",
    "password": "password123"
}
```

---

### Langkah 5 — Buat Task

```
POST /api/tasks

Body:
{
    "title": "Tugas Projek PKL",
    "description": "Membuat Todo List API menggunakan Laravel dan Sanctum",
    "due_date": "2026-05-01",
    "is_public": true
}
```

| Kondisi | Status | Pesan |
|---|---|---|
| Berhasil | `201 Created` | Data task |
| Tidak ada judul | `422 Unprocessable` | "Judul task wajib diisi." |
| Due date masa lalu | `422 Unprocessable` | "Due date tidak boleh di masa lalu." |

---

### Langkah 6 — List Semua Task

```
GET /api/tasks
```

Filter berdasarkan status:

```
GET /api/tasks?status=pending
GET /api/tasks?status=done
```

---

### Langkah 7 — Detail Task

```
GET /api/tasks/{id}
```

---

### Langkah 8 — Update Task

```
PUT /api/tasks/{id}

Body:
{
    "title": "Judul Baru",
    "status": "done"
}
```

---

### Langkah 9 — Tandai Task Selesai

```
PATCH /api/tasks/{id}/done
```

✅ Status task akan otomatis berubah menjadi `done`.

---

### Langkah 10 — Hapus Task

```
DELETE /api/tasks/{id}
```

| Kondisi | Status | Pesan |
|---|---|---|
| Berhasil dihapus | `200 OK` | "Task berhasil dihapus." |
| Task tidak ditemukan / milik user lain | `404 Not Found` | "Task tidak ditemukan." |

---

### Langkah 11 — Guest Public Endpoint (Tanpa Token)

```
GET /api/public/tasks
```

✅ Tidak perlu token — menampilkan semua task dengan `is_public: true`.

---

## Skenario Pengujian

| Skenario | Langkah | Expected |
|---|---|---|
| Akses task milik user lain | Login user B → akses task user A | `404 Not Found` |
| Filter status | `GET /api/tasks?status=pending` | Hanya task pending |
| Akses tanpa token | Request ke `/api/tasks` tanpa header | `401 Unauthenticated` |
| Validasi gagal | POST tanpa `title` | `422 Unprocessable` |
| Guest endpoint | `GET /api/public/tasks` tanpa token | `200 OK` |

---

## Validasi Input

### Store Task (`POST /api/tasks`)

| Field | Aturan |
|---|---|
| `title` | Wajib, string, maksimal 255 karakter |
| `description` | Opsional, string |
| `status` | Opsional, hanya `pending` atau `done` |
| `due_date` | Opsional, format tanggal, tidak boleh masa lalu |
| `is_public` | Opsional, boolean |

### Update Task (`PUT/PATCH /api/tasks/{id}`)

Semua field bersifat opsional (`sometimes`), tapi kalau dikirim harus valid sesuai aturan di atas.

---

## File Map

```
app/
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php               ← POST /api/register, login, logout
│   │   ├── TaskController.php               ← CRUD /api/tasks
│   │   └── PublicTaskController.php         ← GET /api/public/tasks
│   ├── Requests/
│   │   ├── StoreTaskRequest.php             ← Validasi pembuatan task
│   │   └── UpdateTaskRequest.php            ← Validasi update task
│   └── Resources/
│       └── TaskResource.php                 ← Format JSON response task
├── Models/
│   ├── User.php                             ← HasApiTokens, relasi tasks()
│   └── Task.php                             ← fillable, casts, relasi user()

database/migrations/
├── ..._create_users_table.php
├── ..._create_personal_access_tokens_table.php
└── ..._create_tasks_table.php

routes/
└── api.php                                  ← Semua endpoint API

bootstrap/
└── app.php                                  ← Registrasi route API + JSON exception handler
```

---

## Penggunaan AI dalam Pengembangan

Dalam proses pembuatan project ini, saya menggunakan bantuan AI (Claude) sebagai alat bantu.

AI digunakan untuk:

- Mengeksplorasi struktur kode yang baik dan rapi
- Memberikan referensi terkait best practice Laravel
- Membantu memahami konsep Sanctum, Form Request, dan Resource
- Membantu debugging error yang muncul selama development

Namun, project ini tidak sepenuhnya dibuat oleh AI:

- Proses setup environment dilakukan secara mandiri
- Debugging dan penyesuaian dilakukan sendiri
- AI digunakan sebagai pendukung untuk mempercepat proses belajar, bukan sebagai pengganti pemahaman

---

## Hal yang Dipelajari

Dari project ini, saya belajar:

- Membangun REST API dengan Laravel dari awal
- Mengimplementasikan autentikasi token menggunakan Sanctum
- Menyusun validasi input menggunakan Form Request
- Memformat response JSON menggunakan API Resource
- Menerapkan keamanan data — setiap user hanya bisa akses data miliknya sendiri
- Cara menangani error dan routing di Laravel 11
