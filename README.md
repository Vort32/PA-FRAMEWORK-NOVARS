# Muhammad Arianda Saputra

# PA-FRAMEWORK-NOVARS
PA FRAMEWORK 2025
![alt text](ss-novars/logo.png)

# 🏥 Hospital Operation Management System – Laravel Web App
Sistem manajemen operasi rumah sakit modern yang dibangun menggunakan Laravel, TailwindCSS.
Website ini menyediakan alur lengkap dari pelaporan penyakit pasien, pemeriksaan dokter, penjadwalan operasi oleh admin, hingga laporan selesai yang bisa dilihat pasien.

## 🚀 Bahasa Yang Dipake

### Backend  
![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

### Frontend  
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)


## 📌 Deskripsi Project
Hospital Operation Management System adalah aplikasi web yang digunakan untuk mengelola proses operasi rumah sakit secara terstruktur.

Sistem memiliki 4 role: 
- Pasien → Mengirim laporan penyakit 
- Dokter → Memeriksa laporan dan mengisi laporan operasi 
- Admin → Menjadwalkan operasi 
- Staff → Mengelola status ruangan operasi

---

## cara install

- git clone https://github.com/Vort32/PA-FRAMEWORK-NOVARS.git
- cd NovaRs

- composer install
- npm install
- npm run dev

- cp .env.example .env
- php artisan key:generate

- php artisan migrate
- php artisan serve

---

## 🔥 Fitur Utama

### 🧑‍⚕️ Pasien Features
- Registrasi & Login pasien
- Dashboard pasien
- Mengirim laporan penyakit
- Melihat status pemeriksaan dokter
- Melihat jadwal operasi yang telah ditentukan admin
- Mengakses laporan hasil operasi yang diisi dokter
- Riwayat laporan & operasi pasien

### 👨‍⚕️ Dokter Features
- Dashboard dokter
- Melihat laporan penyakit dari pasien
- Approve / Reject laporan penyakit
- Mengisi hasil pemeriksaan awal
- Mengisi laporan hasil operasi setelah tindakan
- Melihat jadwal operasi yang telah dibuat admin
- Riwayat operasi yang pernah ditangani

### 🧑‍💼 Admin Features
- Dashboard admin
- Menjadwalkan operasi berdasarkan laporan yang disetujui dokter
- Mengatur dokter yang menangani operasi
- Mengatur tanggal & waktu operasi
- Monitoring semua laporan, jadwal, dan status operasi
- Manajemen data pasien & dokter

### 🏥 Staff Ruangan Features
- Dashboard staff ruangan
- Mengubah status ruangan operasi
- Monitoring ruangan: Available, In Use, Cleaning, Maintenance
- Menandai ruangan siap sebelum operasi

### 🏨 Operasional & Workflow Features
- Alur otomatis dari pasien → dokter → admin → staff → dokter → pasien
- Status laporan & operasi realtime
- Notifikasi status antar role
- Dashboard monitoring lengkap

---


## 🖼️ Dokumentasi (Screenshots)

### Login   
![alt text](ss-novars/login.png) 

### Register (pasien)  
![alt text](ss-novars/register.png)  

### Dasboard (admin)  
![alt text](ss-novars/admin-dashboard.png) 

### admin (liat pasien)  
![alt text](ss-novars/admin-pasien.png) 

### admin (CRUD dokter) 
![alt text](ss-novars/admin-doctor.png) 

### admin (CRUD staff)  
![alt text](ss-novars/admin-staff.png) 

### admin (CRUD ruangan)  
![alt text](ss-novars/admin-ruangan.png) 

### admin (CRUD alat dan import data alat) 
![alt text](ss-novars/admin-alat.png) 

### admin (CRUD Penyakit)  
![alt text](ss-novars/admin-penyakit.png) 

### admin (menjadwalkan operasi)  
![alt text](ss-novars/admin-operasi.png)  

### Dashboard (Dokter) 
![alt text](ss-novars/dokter-dashboard.png) 

### dokter (melihat operasi dan rujukan)  
![alt text](ss-novars/dokter-operasi.png)  

### dokter (membuat laporan)  
![alt text](ss-novars/dokter-laporan.png)  

### Dashboard (staff)  
![alt text](ss-novars/staff-dashboard.png)  

### dashbaord (pasien)  
![alt text](ss-novars/pasein-dashboard.png)  

### pasien (laporan jadwal operasi)  
![alt text](ss-novars/pasien-laporan.png)  

### pasien (rujukan Penyakit untuk di operasi) 
![alt text](ss-novars/pasein-rujukan_penyakit_ke_dokter.png)  

### dokter (konfirmasi Rujukan)  
![alt text](ss-novars/dokter-konfirmasi_rujukan_pasien.png) 

### admin (konfirmasi rujukan dari dokter dan membuatkan jadwal)  
![alt text](ss-novars/admin_konfirmasi_operasi_pasien.png) 

### pasien (pasien melihat jadwal operasi)  
![alt text](ss-novars/pasein-tampilan_pasein_saat_dijadwalkan_operasi.png) 

### dokter (mengambil jadwal operasi) 
![alt text](ss-novars/dokter-dokter_konfirmasi_operasi.png) 

### admin (admin konfirmasi kegiatan operasi) 
![alt text](ss-novars/admin-admin_konfirmasi_kegiatan_operasi.png) 

### pasien (menampilkan jadwal operasi pasien)
![alt text](ss-novars/pasien-operasi_pasien_dijadwalkan.png) 

### dokter (membuat laporan tentang kegiatan operasi tadi berjalan sukses atau tidak) 
![alt text](ss-novars/dokter-update_operasi_pasien.png)

### pasien (pasien melihat laporan yang telah di buat dokter)  
![alt text](ss-novars/pasien-laporan_operasi_pasien.png)

### pasien (isi laporan operasi pasien tadi)  
![alt text](ss-novars/pasien-isi_laporan_operasi_pasien.png)

---
