# Hướng dẫn upload website lên hosting (nhadep.click)

Làm lần lượt từ trên xuống. Mỗi bước có dòng **Kiểm tra** để biết đã làm đúng trước khi sang bước sau.

## Chuẩn bị

| Cần có | Lấy ở đâu |
|---|---|
| Tài khoản quản lý hosting (cPanel / DirectAdmin) | Email nhà cung cấp hosting gửi khi mua |
| Tên miền `nhadep.click` đã trỏ về hosting | Trang quản lý tên miền (nhà cung cấp tên miền) |
| File theme gốc **Flatsome 3.20.11** (`flatsome.zip`) | ThemeForest → Downloads → Flatsome → **Installable WordPress file only** |
| File child theme **`topbds-flatsome-child.zip`** | Repo GitHub, thư mục `dist/` (bấm vào file → nút **Download raw file**) |
| Thư mục `brand/` (logo, favicon, ảnh hero) | Repo GitHub, thư mục `brand/` |

> Lưu ý: file tải từ ThemeForest bằng nút "All files & documentation" là file tổng, **không** tải thẳng lên WordPress được. Phải chọn "Installable WordPress file only".

---

## Bước 1 – Cài WordPress trên hosting

1. Đăng nhập trang quản lý hosting.
2. Tìm mục **Softaculous** / **WordPress Manager** / **Cài đặt ứng dụng** → chọn **WordPress** → **Install**.
3. Điền:
   - Giao thức: **https://**
   - Tên miền: **nhadep.click**
   - Thư mục (In Directory): **để trống** (để web chạy ở trang gốc, không phải `nhadep.click/wp`)
   - Tên site: `TOPBDS.VN`
   - Tài khoản quản trị: tên đăng nhập **không** dùng `admin`, mật khẩu mạnh, email `thuongdkdx@gmail.com`
   - Ngôn ngữ: **Tiếng Việt**
4. Bấm **Install**, chờ 1–2 phút.

**Kiểm tra:** mở `https://nhadep.click/wp-admin`, đăng nhập được vào trang quản trị.

> Nếu trình duyệt báo "không an toàn" (không có ổ khoá): vào hosting → mục **SSL/TLS** hoặc **Let's Encrypt** → cấp chứng chỉ cho `nhadep.click` và `www.nhadep.click`, chờ vài phút rồi thử lại.

---

## Bước 2 – Chặn Google trong lúc dựng

WordPress → **Cài đặt → Đọc** → tick **"Ngăn chặn các công cụ tìm kiếm đánh chỉ mục trang web này"** → **Lưu thay đổi**.

---

## Bước 3 – Cài theme gốc Flatsome

1. **Giao diện → Giao diện → Thêm mới → Tải giao diện lên**.
2. Chọn file `flatsome.zip` → **Cài đặt ngay**.
3. Khi cài xong **KHÔNG bấm "Kích hoạt"** (sẽ kích hoạt child theme ở bước sau). Nếu Flatsome mở trình hướng dẫn cài đặt (Setup Wizard), bấm bỏ qua.
4. Đăng ký bản quyền để nhận cập nhật: **Flatsome → Theme Registration**, dán mã mua hàng (Purchase code) từ ThemeForest.

**Kiểm tra:** trong **Giao diện → Giao diện** thấy ô "Flatsome".

---

## Bước 4 – Cài child theme TOPBDS

1. **Giao diện → Giao diện → Thêm mới → Tải giao diện lên**.
2. Chọn file `topbds-flatsome-child.zip` → **Cài đặt ngay** → **Kích hoạt**.
3. Vào **Cài đặt → Đường dẫn tĩnh** → chọn **Tên bài viết** → **Lưu thay đổi** (bắt buộc, để các link `/du-an/`, `/loai-hinh/`… hoạt động).

**Kiểm tra:**
- **Giao diện → Giao diện**: ô **"TOPBDS Flatsome Child"** đang ở trạng thái *Đang dùng*.
- Menu trái có mục **Dự án** (biểu tượng toà nhà), bên trong có **Loại hình**, **Khu vực**, **Trạng thái** đã có sẵn các mục (Căn hộ, Biệt thự, Hà Nội…).
- Mở `https://nhadep.click/du-an/` không bị lỗi 404.

### Nếu tải lên bị lỗi

| Thông báo | Cách xử lý |
|---|---|
| "The link you followed has expired" hoặc "vượt quá giới hạn tải lên" | File vượt giới hạn upload của hosting. Dùng cách tải bằng **Quản lý file** bên dưới. |
| "The package could not be installed. The theme is missing the style.css stylesheet" | Bạn đang tải file sai (file tổng ThemeForest, hoặc file "Download ZIP" của cả repo GitHub). Dùng đúng file ở phần Chuẩn bị. |
| "The parent theme could not be found… install the parent theme, flatsome" (không tìm thấy theme cha) | Chưa cài theme gốc Flatsome, quay lại Bước 3. |

**Tải bằng Quản lý file (File Manager) của hosting:**
1. Hosting → **File Manager** → mở thư mục `public_html/wp-content/themes/`.
2. **Upload** file zip (làm cho `flatsome.zip` trước, rồi `topbds-flatsome-child.zip`).
3. Chuột phải file zip → **Extract** (Giải nén) ngay tại thư mục đó → xoá file zip.
4. Kết quả phải là hai thư mục: `wp-content/themes/flatsome/` và `wp-content/themes/flatsome-child/` (mở `flatsome-child` thấy ngay file `style.css`, không bị lồng thêm một thư mục nữa).
5. Quay lại WordPress → **Giao diện → Giao diện** → kích hoạt "TOPBDS Flatsome Child", rồi lưu Đường dẫn tĩnh như trên.

---

## Bước 5 – Cài plugin

**Plugin → Cài mới**, tìm và **Cài đặt → Kích hoạt** lần lượt:

| Plugin | Để làm gì |
|---|---|
| **Rank Math SEO** | SEO, sitemap, breadcrumb |
| **LiteSpeed Cache** | Tăng tốc (hosting LiteSpeed) – cài đặt theo Bước 7 trong `HUONG-DAN-TRIEN-KHAI.md` |
| **Contact Form 7** | Form đăng ký nhận tin, form nhận báo giá |
| **WP Mail SMTP** | Gửi mail form không bị vào Spam |
| **Safe SVG** *(tuỳ chọn)* | Cho phép tải logo dạng SVG |

> Flatsome sẽ hiện thông báo gợi ý cài **Nextend Social Login, WooCommerce, YITH WooCommerce Wishlist**. **Không cần cài**: đây là gợi ý chung cho web bán hàng. Web TOPBDS không có giỏ hàng, không có đăng nhập cho khách. Bấm **Dismiss this notice** để ẩn thông báo.

---

## Bước 6 – Làm tiếp phần nội dung

Theme đã chạy. Phần còn lại làm theo **`docs/HUONG-DAN-TRIEN-KHAI.md`**, theo thứ tự:

1. **Bước 1b** – logo, favicon, ảnh hero (thư mục `brand/`)
2. **Bước 2** – Flatsome Theme Options (màu, font Be Vietnam Pro, header)
3. **Bước 3** – Giao diện → Tuỳ biến → **TOPBDS – Liên hệ** (hotline, Zalo, tư vấn viên, form báo giá)
4. **Bước 4** – nhập Loại hình, Khu vực (kèm ảnh) và các dự án (kích thước ảnh: `docs/HUONG-DAN-ANH.md`)
5. **Bước 5** – dán nội dung trang chủ, footer, mega menu từ `docs/ux-builder/`
6. **Bước 6, 7** – SEO và LiteSpeed Cache

---

## Cập nhật theme về sau

Khi có bản child theme mới:
- **Cách 1:** Giao diện → Giao diện → Thêm mới → Tải lên file zip mới → WordPress báo giao diện đã tồn tại → chọn nút **thay thế bằng bản vừa tải lên** (Replace current with uploaded).
- **Cách 2:** File Manager → xoá thư mục `wp-content/themes/flatsome-child/` cũ → tải và giải nén file zip mới.

Dữ liệu (dự án, ảnh, cài đặt Tuỳ biến, UX Block) nằm trong cơ sở dữ liệu nên **không mất** khi thay file theme. Sau khi cập nhật, bấm **LiteSpeed Cache → Toolbox → Purge All**.
