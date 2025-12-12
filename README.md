# Semantic Web - Pencarian Judul Skripsi

Sistem pencarian judul skripsi berbasis web semantik menggunakan ontologi dan SPARQL untuk Universitas Bina Insan.

## 🚀 Teknologi Stack

### Backend
- **Laravel 11** - PHP Framework
- **MySQL** - Database
- **EasyRDF** - RDF/OWL Ontology Processing
- **SPARQL** - Semantic Query Language
- **Boyer-Moore Algorithm** - String Matching

### Frontend
- **React 18** - UI Library
- **Vite** - Build Tool
- **React Router** - Routing
- **Axios** - HTTP Client
- **React Icons** - Icon Library

### Ontology
- **Protégé** - Ontology Editor
- **RDF/OWL** - Ontology Format

## 📋 Fitur Utama

### Untuk Mahasiswa
- ✅ Pencarian semantik judul skripsi
- ✅ Filter berdasarkan topik dan tahun
- ✅ Hasil pencarian dengan semantic matching
- ✅ Download file skripsi (PDF/DOCX)

### Untuk Kaprodi
- ✅ Upload data skripsi dengan file
- ✅ Kelola data skripsi yang diupload
- ✅ Edit dan hapus skripsi

### Untuk Admin
- ✅ Manajemen user (CRUD)
- ✅ Upload dan kelola ontologi
- ✅ Kelola semua data skripsi
- ✅ Aktivasi ontologi

## 🛠️ Installation

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL
- Protégé (untuk membuat ontologi)

### Backend Setup

1. Navigate to backend directory:
```bash
cd backend-laravel
```

2. Install dependencies:
```bash
composer install
```

3. Configure environment:
```bash
# Edit .env file
DB_DATABASE=semantic_web_skripsi
DB_USERNAME=root
DB_PASSWORD=your_password
```

4. Create database:
```bash
mysql -u root -p
CREATE DATABASE semantic_web_skripsi;
exit;
```

5. Run migrations and seeders:
```bash
php artisan migrate
php artisan db:seed
```

6. Start Laravel server:
```bash
php artisan serve
```

Backend akan berjalan di `http://localhost:8000`

### Frontend Setup

1. Navigate to frontend directory:
```bash
cd frontend-react
```

2. Install dependencies:
```bash
npm install
```

3. Start development server:
```bash
npm run dev
```

Frontend akan berjalan di `http://localhost:3000`

## 👥 Default Users

Setelah menjalankan seeder, Anda dapat login dengan:

- **Admin**
  - Email: `admin@example.com`
  - Password: `password`

- **Kaprodi**
  - Email: `kaprodi@example.com`
  - Password: `password`

- **Mahasiswa**
  - Email: `mahasiswa@example.com`
  - Password: `password`

## 📁 Struktur Project

```
semantic-web-mobile/
├── backend-laravel/
│   ├── app/
│   │   ├── Http/Controllers/API/
│   │   ├── Models/
│   │   └── Services/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   │   └── api.php
│   └── storage/
│       └── app/
│           ├── skripsi/
│           └── ontology/
└── frontend-react/
    ├── src/
    │   ├── components/
    │   ├── pages/
    │   ├── services/
    │   ├── context/
    │   └── styles/
    └── public/
```

## 🔧 API Endpoints

### Authentication
- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `GET /api/me` - Get current user

### Search (Public)
- `GET /api/search?q=keyword&topik=&tahun=` - Semantic search
- `GET /api/search/suggestions?q=partial` - Search suggestions

### Skripsi
- `GET /api/skripsi/{id}` - Get skripsi detail
- `GET /api/skripsi/{id}/download` - Download file
- `POST /api/skripsi` - Upload skripsi (Kaprodi/Admin)
- `PUT /api/skripsi/{id}` - Update skripsi (Owner/Admin)
- `DELETE /api/skripsi/{id}` - Delete skripsi (Owner/Admin)

### Ontology (Admin)
- `GET /api/ontology/current` - Get active ontology
- `POST /api/ontology/upload` - Upload ontology
- `POST /api/ontology/{id}/activate` - Set as active
- `DELETE /api/ontology/{id}` - Delete ontology

### Users (Admin)
- `GET /api/users` - Get all users
- `POST /api/users` - Create user
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user

## 🎨 Design Theme

Sistem menggunakan **pink theme** dengan:
- Primary Color: `#E91E63` (Pink)
- Accent Color: `#FF4081`
- Background: `#FFF0F5`
- Responsive design untuk mobile, tablet, dan desktop

## 🧪 Testing

### Backend Testing
```bash
cd backend-laravel
php artisan test
```

### Black Box Testing
Lakukan testing manual untuk setiap user flow sesuai dengan use case diagram.

## 📝 Ontology

Ontology harus dibuat menggunakan Protégé dengan struktur:

### Classes
- Skripsi
- TopikPenelitian (dengan sub-classes)
- MetodePenelitian
- BidangIlmu

### Object Properties
- hasTopik
- hasMetode
- relatedTo
- subTopicOf

### Data Properties
- judul
- tahun
- penulis
- katakunci

File ontology (.owl) dapat diupload melalui admin panel.

## 🚀 Deployment

### Backend
1. Set `APP_ENV=production` di `.env`
2. Run `php artisan config:cache`
3. Run `php artisan route:cache`
4. Setup web server (Apache/Nginx)

### Frontend
1. Build production:
```bash
npm run build
```
2. Deploy `dist/` folder ke hosting

## 📄 License

MIT License

## 👨‍💻 Developer

Developed for Universitas Bina Insan - Semantic Web Project
