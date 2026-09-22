# TugasWeb-Pertemuan7-LoginRegister

Nama : Felipe Maranatha Lumbanraja <br>
NIM : 4253250014 <br>
Kelas : PSIK 25C <br>

Sistem login dan register sederhana pakai PHP native, data disimpan di file JSON (users.json), tanpa database.
 
## Isi Folder
 
- `functions.php` - fungsi-fungsi utama (baca/simpan data, session, sanitasi, remember me)
- `register.php` - halaman daftar akun
- `login.php` - halaman login
- `logout.php` - proses logout
- `dashboard.php` - halaman setelah login
- `edit_profile.php` - halaman edit profil
- `style.css` - styling
- `users.json` - tempat data user disimpan
## Fitur
 
- Register dengan validasi nama, email, password (min 6 karakter)
- Password di-hash pakai `password_hash()`
- Cek email yang sudah terdaftar
- Login pakai session PHP
- Dashboard tidak bisa diakses kalau belum login
- Logout menghapus session
- Input disanitasi biar aman dari XSS
Bonus yang saya tambahkan:
- Fitur "Ingat Saya" pakai cookie
- Halaman edit profil (bisa ganti nama, email, password)
- Tampilan CSS
## Cara Menjalankan (XAMPP)
 
1. Copy folder ini ke `htdocs` di folder instalasi XAMPP
2. Buka XAMPP Control Panel, start Apache saja 
3. Buka browser, akses `http://localhost/nama-folder/register.php`
4. Daftar akun baru, lalu coba login
## Percobaan yang saya lakukan
 
1. Daftar akun baru
2. Coba daftar pakai email yang sama - muncul pesan error
3. Login pakai akun yang baru dibuat
4. Coba akses dashboard tanpa login - otomatis diarahkan ke halaman login
5. Coba centang "Ingat Saya" pas login, tutup browser, buka lagi - masih tetap login
6. Edit profil, ganti password
7. Logout
