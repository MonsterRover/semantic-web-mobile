# 🚀 Quick Start Guide

## Langkah-langkah Cepat untuk Menjalankan Aplikasi

### 1️⃣ Setup Database (5 menit)

```bash
# Buka MySQL
mysql -u root -p

# Buat database
CREATE DATABASE semantic_web_skripsi;
exit;
```

### 2️⃣ Setup Backend Laravel (5 menit)

```bash
# Masuk ke folder backend
cd backend-laravel

# Install dependencies (jika belum)
composer install

# Edit .env file - pastikan database settings benar:
# DB_DATABASE=semantic_web_skripsi
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Generate application key (jika belum)
php artisan key:generate

# Run migrations dan seeders
php artisan migrate
php artisan db:seed

# Start Laravel server
php artisan serve
```

✅ Backend sekarang berjalan di: **http://localhost:8000**

### 3️⃣ Setup Frontend React (3 menit)

Buka terminal baru:

```bash
# Masuk ke folder frontend
cd frontend-react

# Install dependencies (jika belum)
npm install

# Start development server
npm run dev
```

✅ Frontend sekarang berjalan di: **http://localhost:3000**

### 4️⃣ Login & Test (2 menit)

1. Buka browser: **http://localhost:3000**
2. Klik tombol **Login**
3. Gunakan salah satu akun default:

**Admin:**
- Email: `admin@example.com`
- Password: `password`

**Kaprodi:**
- Email: `kaprodi@example.com`
- Password: `password`

**Mahasiswa:**
- Email: `mahasiswa@example.com`
- Password: `password`

---

## 🎯 Fitur yang Bisa Dicoba

### Sebagai Mahasiswa
1. **Pencarian Semantik**
   - Masuk ke halaman utama
   - Coba search: "machine learning"
   - Lihat hasil dengan semantic matching

2. **Filter Pencarian**
   - Klik "Show Filters"
   - Filter by topik atau tahun
   - Lihat hasil yang terfilter

### Sebagai Kaprodi
1. **Upload Skripsi**
   - Login sebagai kaprodi
   - Klik menu "Upload Skripsi"
   - Isi form dan upload file PDF/DOCX
   - Submit dan lihat success message

2. **Lihat Upload Saya**
   - Akses daftar skripsi yang sudah diupload
   - Edit atau hapus skripsi

### Sebagai Admin
1. **Kelola User**
   - Login sebagai admin
   - Klik menu "Users"
   - Tambah user baru
   - Edit atau hapus user

2. **Kelola Ontology**
   - Klik menu "Ontology"
   - Upload file ontology (.owl)
   - Aktivasi ontology

---

## ⚠️ Troubleshooting

### Backend tidak bisa start
```bash
# Pastikan port 8000 tidak digunakan
# Atau gunakan port lain:
php artisan serve --port=8001
```

### Frontend tidak bisa start
```bash
# Pastikan port 3000 tidak digunakan
# Atau edit vite.config.js untuk ganti port
```

### Database connection error
```bash
# Cek .env file
# Pastikan DB_DATABASE, DB_USERNAME, DB_PASSWORD benar
# Test koneksi:
php artisan migrate:status
```

### CORS error
```bash
# Pastikan backend berjalan di port 8000
# Pastikan frontend proxy di vite.config.js benar
```

---

## 📁 Struktur File Penting

```
semantic-web-mobile/
├── backend-laravel/
│   ├── .env                    # Database config
│   ├── routes/api.php          # API routes
│   ├── app/Services/           # Business logic
│   └── storage/app/            # File uploads
│
└── frontend-react/
    ├── vite.config.js          # Dev server config
    ├── src/App.jsx             # Main app
    ├── src/pages/              # Page components
    └── src/services/           # API calls
```

---

## 🎨 Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Kaprodi | kaprodi@example.com | password |
| Mahasiswa | mahasiswa@example.com | password |

---

## 📝 Next Steps

1. ✅ **Buat Ontology di Protégé**
   - Download Protégé
   - Buat ontology sesuai struktur di README
   - Export ke .owl
   - Upload via admin panel

2. ✅ **Upload Data Dummy**
   - Login sebagai Kaprodi
   - Upload beberapa skripsi
   - Test semantic search

3. ✅ **Testing**
   - Test semua fitur
   - Test responsive design
   - Test di berbagai browser

---

## 🆘 Butuh Bantuan?

- Lihat **README.md** untuk dokumentasi lengkap
- Lihat **walkthrough.md** untuk detail implementasi
- Lihat **IMPLEMENTATION_SUMMARY.md** untuk overview

---

**Selamat Mencoba! 🎉**
