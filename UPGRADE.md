
# Upgrade

> Pastikan database sudah di backup terlebih dahulu
> Pastikan file slims sudah di backup
> Tidak ada garansi dalam pemakaian software
> Gunakan dengan bijak, tidak untuk disalahgunakan
> Pembuat tidak bertanggung jawab atas segala apapun.

Tutorial upgrade diperuntukan untuk slims akasia.


### Installation

- Clone repository
- Copy dan replace file - file berikut ke dalam folder slims anda
**Note : plugin lain yang menggunakan file tersebut akan hilang**
```sh
admin/modules/circulation/submenu.php
admin/modules/loan_request/index.php
admin/modules/loan_request/loan_request.php
admin/modules/loan_request/submenu.php
admin/modules/loan_request/xlsoutput.php
direct.php
lib/contents/loan_request.inc.php
lib/contents/member.inc.php
lib/detail.inc.php
online_reserve.sql
template/core.style.css
```

- Import database

```sh
online_reserve.sql
```

- Test plugin yang baru dipasang

**Selamat mencoba dan semoga berhasil**