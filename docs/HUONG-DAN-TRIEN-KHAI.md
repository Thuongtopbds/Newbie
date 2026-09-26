# Triển khai trang chủ TOPBDS trên WordPress + Flatsome

Tài liệu này đi kèm child theme `flatsome-child/` và các đoạn nội dung UX Builder trong `docs/ux-builder/`.

Ảnh chụp bản chạy thử: `docs/preview/`. Ảnh dự án trong đó được cắt từ mockup, chỉ để minh hoạ.

---

## 1. Cách chia việc: UX Blocks hay code riêng

Nguyên tắc chia như sau:

- **Nội dung tĩnh** mà người quản trị tự sửa bằng tay (chữ, ảnh nền, nút) thì làm bằng **Flatsome UX Builder / UX Blocks**.
- **Khối lấy dữ liệu từ WordPress** (dự án, loại hình, khu vực, tin tức) thì dùng **shortcode của child theme**. Dữ liệu tự cập nhật khi bạn đăng dự án hoặc bài viết, không phải sửa trang chủ.

Các shortcode này đã được đưa vào UX Builder, nằm trong nhóm **TOPBDS**. Bạn vẫn kéo thả và chỉnh tuỳ chọn như phần tử gốc của Flatsome.

| # | Phần trên mockup | Làm bằng | Chi tiết |
|---|---|---|---|
| 1 | Header & menu | **Flatsome Header Builder** + shortcode `[tp_contact_buttons]` | Logo, menu, sticky, mobile: dùng Header Builder. Nút "Chat Zalo" và số điện thoại: đặt shortcode vào phần tử **HTML** của header. Mega menu cho mục "Dự án": **UX Block** `mega-menu.txt`, các cột link lấy tự động bằng `[tp_mega_links]` (ảnh: `docs/preview/mega-menu.png`). |
| 2 | Hero + tìm kiếm | **UX Builder** (Section, Row, Col, Text) + `[tp_hero_search]` + `[tp_trust_item]` | Ảnh nền, dòng chữ cam, H1 và mô tả: sửa trực tiếp trong UX Builder. Ô tìm kiếm là code riêng vì phải tìm trong post type Dự án. Hiệu ứng kính mờ: CSS riêng, gắn qua class `tp-glass`. |
| 3 | Dự án nổi bật | `[tp_projects filter="featured"]` | Lấy các dự án được tick "Hiện ở mục Dự án nổi bật". |
| 4 | Khám phá theo loại hình | `[tp_terms taxonomy="loai_hinh"]` | Ảnh và số dự án tự lấy từ mục Loại hình. |
| 5 | Dự án mới cập nhật | `[tp_projects filter="latest"]` | Sắp theo ngày sửa gần nhất. |
| 6 | Dự án đang mở bán | `[tp_projects filter="selling"]` | Lấy dự án có Trạng thái "Đang mở bán". Nhãn "Hot" bật trong trang sửa dự án. |
| 7 | Tìm kiếm theo khu vực | `[tp_terms taxonomy="khu_vuc" more="Các tỉnh khác"]` | Ô cuối tự đếm số dự án thuộc các khu vực chưa được hiện. |
| 8 | Tin tức thị trường | `[tp_news]` | Bố cục 1 bài lớn + danh sách. Phần tử Blog Posts gốc của Flatsome không dựng được bố cục này nếu không sửa CSS khá nhiều, nên dùng code riêng. |
| 9 | CTA tư vấn | **UX Builder** (Row class `tp-cta`) + `[tp_contact_buttons style="cta"]` | Chữ sửa trong UX Builder. Số điện thoại và link Zalo lấy từ cài đặt chung. |
| 10 | Footer | **UX Block** `footer.txt` + Contact Form 7 | Theme Options → Footer: chọn UX Block này. Dòng bản quyền đặt ở phần "Absolute Footer". |

Phần bổ sung (không có trong mockup trang chủ nhưng cần để web chạy đủ):
- **Trang chi tiết dự án.** Làm hoàn toàn bằng code (xem mục 3). Người quản trị chỉ nhập dữ liệu và viết nội dung bài, không cần dựng lại bố cục cho từng dự án.

- **Trang danh sách dự án.** `/du-an/`, `/loai-hinh/...`, `/khu-vuc/...`, `/trang-thai/...` và kết quả tìm kiếm dùng chung một template, có bộ lọc Từ khoá, Loại hình, Khu vực và phân trang.
- **Thanh "Chat Zalo / Gọi" cố định ở cuối màn hình mobile.** Bật/tắt trong Tuỳ biến.

---

## 2. Trong child theme có gì

```
flatsome-child/
├── style.css               khai báo child theme (Template: flatsome)
├── functions.php           nạp các file trong inc/
├── inc/
│   ├── setup.php           nạp CSS/JS, kích thước ảnh, Tuỳ biến "TOPBDS – Liên hệ", thanh liên hệ mobile
│   ├── post-types.php      post type Dự án + Loại hình, Khu vực, Trạng thái (tạo sẵn các mục mặc định)
│   ├── meta.php            ô "Thông tin hiển thị trên thẻ dự án" + ảnh đại diện cho Loại hình/Khu vực
│   ├── template-tags.php   HTML thẻ dự án, ô loại hình/khu vực, bộ icon SVG
│   ├── shortcodes.php      các shortcode tp_*
│   ├── ux-builder.php      đưa shortcode vào UX Builder (nhóm TOPBDS)
│   ├── search.php          dùng template danh sách dự án cho archive/taxonomy/tìm kiếm
│   └── single.php          hàm cho trang chi tiết: bảng đặc điểm, mục lục, breadcrumb, dự án liên quan, thẻ tư vấn viên
├── single-du_an.php        trang chi tiết dự án
├── templates/archive-du-an.php
└── assets/
    ├── css/topbds.css      toàn bộ CSS riêng (màu chỉnh ở :root)
    └── js/
        ├── topbds.js                  nút trái tim, thư viện ảnh + xem phóng to, mục lục đánh dấu mục đang đọc
        ├── admin-project-gallery.js   chọn nhiều ảnh cho "Thư viện ảnh" trong trang sửa dự án
        └── admin-term-image.js        chọn ảnh cho Loại hình / Khu vực
```

Không cần cài ACF: các trường thông tin dùng meta box có sẵn của WordPress.

### Thông tin của một dự án

| Trường | Nhập ở đâu | Hiển thị |
|---|---|---|
| Tên, ảnh đại diện, nội dung | Trình soạn thảo dự án | Tiêu đề, ảnh thẻ |
| Giá hiển thị | Ô "Thông tin hiển thị trên thẻ dự án" | VD: "Từ 27 triệu/m²" |
| Vị trí ngắn | như trên | Để trống thì lấy tên Khu vực |
| Sản phẩm | như trên | Để trống thì lấy các Loại hình |
| Nổi bật / Hot | 2 ô tick | Mục "Dự án nổi bật" / nhãn đỏ "Hot" |
| Loại hình, Khu vực, Trạng thái | Hộp bên phải | Lọc, đếm số dự án, nhãn "Đang mở bán" |
| Địa chỉ đầy đủ, Chủ đầu tư, Quy mô, Diện tích, Phòng ngủ, Pháp lý, Bàn giao | Nhóm "Trang chi tiết" | Bảng "Đặc điểm dự án". Diện tích, Phòng ngủ, Pháp lý còn hiện thành ô thông số dưới tiêu đề. Dòng nào để trống thì ẩn |
| Thư viện ảnh | nút "Chọn ảnh" | Ảnh đại diện là ảnh đầu, các ảnh này là dãy ảnh nhỏ; bấm ảnh lớn để xem phóng to |
| Vị trí trên bản đồ | ô chữ | Bản đồ Google ở cuối bài. Để trống thì ẩn |

### Tham số shortcode

| Shortcode | Tham số chính |
|---|---|
| `[tp_heading]` | `title`, `sub`, `link`, `link_text`, `tag` (h2/h3/h1), `light` |
| `[tp_hero_search]` | `placeholder`, `button`, `chips` (slug Loại hình, cách nhau dấu phẩy) |
| `[tp_projects]` | `filter` (featured / latest / selling / all), `count`, `columns`, `badge`, `button`, `loai_hinh`, `khu_vuc`, `trang_thai` |
| `[tp_terms]` | `taxonomy` (loai_hinh / khu_vuc), `include`, `count`, `columns`, `style` (tall / short), `arrow`, `more` |
| `[tp_news]` | `count`, `category`, `excerpt` |
| `[tp_mega_links]` | `taxonomy` (loai_hinh / khu_vuc / trang_thai), `count` (0 = tất cả), `hide_empty`, `more`, `more_link` |
| `[tp_contact_buttons]` | `style` (header / cta), `zalo_text`, `call_text` |
| `[tp_trust_item]` | `icon`, `title`, `text` |
| `[tp_icon]` | `name`, `size`, `badge` |

---

## 3. Trang chi tiết dự án

Trang được thiết kế theo cùng phong cách với trang chủ: hero ảnh nền tối, khung kính mờ, điểm nhấn màu cam, tiêu đề mục có vạch cam, thẻ dự án giống trang chủ. Ảnh chụp: `docs/preview/chi-tiet-du-an-*.jpg`.

| Vùng | Nội dung |
|---|---|
| **Hero** | Ảnh đại diện làm nền, phủ lớp tối như hero trang chủ. Bên trái: breadcrumb, nhãn trạng thái, nút Loại hình, **H1**, địa chỉ, các nút "Nhận bảng giá", "Chat Zalo", "Xem N ảnh", nút lưu. Bên phải: khung kính mờ gồm **Giá bán** (chữ cam lớn), Diện tích, Phòng ngủ, Pháp lý, Bàn giao |
| **Thanh mục lục** | Dính dưới header khi cuộn. Gồm "Tổng quan" và các tiêu đề **H2** trong bài (tự tạo), tự đánh dấu mục đang đọc. Bên phải có nút "Nhận báo giá" |
| **Lưới ảnh** | Các ảnh trong "Thư viện ảnh" xếp kiểu khảm: 1 ảnh lớn + 4 ảnh nhỏ, ảnh cuối ghi "+N ảnh" nếu còn. Bấm để xem phóng to, chuyển ảnh bằng phím ← → |
| **Tổng quan dự án** | Các ô có icon: Vị trí, Chủ đầu tư, Loại hình, Quy mô, Diện tích, Phòng ngủ, Pháp lý, Bàn giao. Ô trống tự ẩn |
| **Nội dung bài** | Viết bằng trình soạn thảo. Mỗi phần lớn (Mặt bằng, Vị trí, Tiện ích, Bảng giá…) nên đặt là **Tiêu đề H2** để lên thanh mục lục |
| **Bản đồ** | Google Maps theo ô "Vị trí trên bản đồ" |
| **Cột phải: thẻ "Nhận báo giá"** | Dính theo khi cuộn. Gồm giá bán, form Contact Form 7 (chưa cài form thì hiện nút Zalo + Gọi), tư vấn viên, hotline |
| **Dự án liên quan** | 4 thẻ dự án cùng loại hình hoặc khu vực |
| **Mobile** | Lưới ảnh thành dãy vuốt ngang, thẻ báo giá chuyển xuống cuối bài. Thanh dưới đáy có 3 nút: Chat Zalo, Gọi, Báo giá |

Trang không cần UX Builder: bố cục áp dụng tự động cho mọi dự án, người quản trị chỉ nhập thông tin và viết bài.

**Cài đặt cho trang chi tiết** (Giao diện → Tuỳ biến → TOPBDS – Liên hệ):
- Tên, ảnh và lời chào của tư vấn viên.
- **Form nhận báo giá:** tạo một form Contact Form 7 rồi dán shortcode của nó (VD: `[contact-form-7 id="123" title="Nhận báo giá"]`) vào ô này. Nội dung form gợi ý:
  ```
  [text* ho-ten placeholder "Họ và tên"]
  [tel* so-dien-thoai placeholder "Số điện thoại"]
  [submit "Nhận bảng giá ngay"]
  ```
  Trong tab **Mail** của form, thêm dòng `Dự án: [_post_title] – [_post_url]` để biết khách đăng ký từ dự án nào.

---

## 4. Các bước triển khai

### Bước 1 – Cài theme và plugin

> **Phiên bản Flatsome:** dùng **3.20.11** (bản vá bảo mật XSS so với 3.20.9). Trước khi cập nhật theme gốc trên site đang chạy, hãy sao lưu site. Child theme không cần sửa khi cập nhật trong dòng 3.20.
1. Cài theme gốc **Flatsome** (bản có bản quyền). Đưa thư mục `flatsome-child` vào `wp-content/themes/`, hoặc nén thành `.zip` rồi tải lên ở Giao diện → Giao diện → Thêm mới. Sau đó **kích hoạt TOPBDS Flatsome Child**.
2. Cài các plugin: **Rank Math SEO**, **LiteSpeed Cache**, **Contact Form 7**.
3. Vào **Cài đặt → Đường dẫn tĩnh**, chọn "Tên bài viết" rồi bấm **Lưu**, để các đường dẫn `/du-an/`, `/loai-hinh/`… hoạt động.

Khi kích hoạt, theme tự tạo sẵn các mục sau:
- Loại hình: Căn hộ, Biệt thự, Liền kề, Nhà phố, Đất nền, Khu đô thị, Nhà vườn, Shophouse
- Khu vực: Hà Nội, TP. Hồ Chí Minh, Hải Phòng, Hưng Yên, Bắc Ninh
- Trạng thái: Đang mở bán, Sắp mở bán, Đã bàn giao

### Bước 2 – Flatsome Theme Options
- **Style → Colors:** Primary `#F26B21`, Secondary `#0F1E33`.
- **Style → Typography:** font **Be Vietnam Pro** cho cả tiêu đề lẫn nội dung (đủ dấu tiếng Việt). Cỡ chữ nội dung 15px.
- **Layout:** độ rộng container 1200px.
- **Header:**
  - Logo bên trái, menu chính ở giữa.
  - Bên phải thêm phần tử **HTML 1** với nội dung `[tp_contact_buttons]`.
  - Bật Sticky header.
  - Mobile: logo + nút menu. Thanh Zalo/Gọi dưới đáy đã có sẵn trong child theme.
- **Footer:** chọn UX Block "Footer" ở Bước 5. Mục **Absolute Footer** ghi `© 2025 TOPBDS.VN. All rights reserved.` kèm link Chính sách bảo mật và Điều khoản sử dụng.

### Bước 3 – Cài đặt liên hệ
Vào **Giao diện → Tuỳ biến → TOPBDS – Liên hệ**, nhập Hotline và link Zalo. Nút ở header, khối CTA và thanh mobile đều lấy từ đây.

### Bước 4 – Nhập dữ liệu
1. Vào **Dự án → Loại hình** và **Dự án → Khu vực**, bấm "Chọn ảnh" cho từng mục. Nên dùng ảnh ngang, tối thiểu 520×400px.
2. **Dự án → Thêm dự án**:
   - Điền tên, ảnh đại diện (tối thiểu 640×420px), giá, vị trí, sản phẩm.
   - Chọn Loại hình, Khu vực, Trạng thái.
   - Tick "Nổi bật" với 4–8 dự án muốn đưa lên đầu trang chủ.
3. Viết bài tin tức (Bài viết). Tạo chuyên mục Thị trường, Phân tích, Pháp lý, Quy hoạch, Đầu tư.

### Bước 5 – Dựng trang chủ, footer, mega menu
1. **Trang chủ:**
   - Tạo trang "Trang chủ" và chọn template **"Page - Transparent Header"** để header trong suốt nằm đè lên ảnh hero.
   - Chuyển trình soạn thảo sang chế độ **Code/Văn bản**, dán toàn bộ nội dung `docs/ux-builder/trang-chu.txt`.
   - Thay `ID_ANH_HERO` bằng ID ảnh hero trong Thư viện (hoặc mở UX Builder, bấm vào Section Hero và chọn ảnh).
   - Mở **UX Builder** để chỉnh tiếp bằng kéo thả.
   - Vào **Cài đặt → Đọc**, chọn "Trang tĩnh", trang chủ là "Trang chủ".
2. **Footer:**
   - Vào **UX Blocks → Thêm mới**, đặt tên "Footer", dán `docs/ux-builder/footer.txt`.
   - Thay `ID_LOGO_TRANG` và `ID_FORM`, sửa link mạng xã hội.
   - Chọn Block này ở Theme Options → Footer.
3. **Form đăng ký nhận tin (Contact Form 7):** tạo form với nội dung:
   ```
   [email* email-dang-ky placeholder "Nhập email của bạn"]
   [submit "Đăng ký"]
   ```
4. **Mega menu cho mục "Dự án":**
   - Vào **UX Blocks → Thêm mới**, đặt tên "Mega menu Dự án", dán `docs/ux-builder/mega-menu.txt` rồi lưu.
   - Bảng gồm 4 cột: **Loại hình**, **Khu vực** (6 tỉnh/thành nhiều dự án nhất), **Trạng thái**, và thẻ **1 dự án nổi bật**.
   - Các link và số dự án tự cập nhật; mục chưa có dự án nào tự ẩn. Không cần sửa block khi thêm dự án hay loại hình mới.
   - Vào **Giao diện → Menu**, mở mục "Dự án" trong menu chính. Trong phần cài đặt Flatsome của mục menu:
     - **Design:** chọn **Container width** (dropdown rộng bằng khung nội dung; từ Flatsome 3.15.4 trở lên độ rộng được tự đặt).
     - **UX Block:** chọn block "Mega menu Dự án".
     - Kiểu mở: **Hover** (rê chuột) hoặc **Click**. Nên chọn Hover trên máy tính.
   - Mục "Dự án" không cần menu con. Các mục khác (Tin tức, Giới thiệu…) vẫn dùng menu con bình thường.
   - Trên điện thoại, Flatsome dùng menu trượt riêng, có thể không hiện đầy đủ block mega menu. Hãy mở thử trên điện thoại. Nếu thiếu, tạo một menu riêng cho điện thoại (mục "Dự án" có menu con: Căn hộ, Biệt thự, Liền kề, Hà Nội…) rồi gán vào vị trí menu mobile trong **Giao diện → Menu → Quản lý vị trí**.

### Bước 6 – SEO và tốc độ
- **Thứ bậc tiêu đề đúng như đề xuất:**
  - H1 duy nhất ở hero.
  - Mỗi mục là một **H2** (`[tp_heading]`).
  - Tên dự án và tên bài là H3.
  - Trang danh sách dự án có H1 riêng, tên dự án là H2.
- **Rank Math:**
  - Bật Sitemap cho Dự án, Loại hình, Khu vực.
  - Đặt schema cho Dự án (gợi ý: Product hoặc Place).
  - Bật Breadcrumbs.
- **Liên kết nội bộ:** các nút loại hình, ô khu vực và "Xem tất cả" đều trỏ tới trang danh sách tương ứng.
- **LiteSpeed Cache:** bật cache trang, chuyển ảnh sang WebP, bật lazy-load ảnh. Loại trừ ảnh hero khỏi lazy-load vì đây là ảnh lớn nhất màn hình đầu.
- Ảnh trong thẻ dự án đã có sẵn kích thước cắt riêng (`tp-card`, `tp-tile`, `tp-news`, `tp-thumb`). Với ảnh tải lên từ trước khi cài theme, chạy plugin **Regenerate Thumbnails** một lần.

---

## 5. Đã kiểm tra những gì

Child theme được chạy trên WordPress mới nhất (PHP 8.4) với dữ liệu mẫu (12 dự án, 4 bài viết):

- Trang chủ, trang chi tiết dự án, `/du-an/`, trang Loại hình / Khu vực / Trạng thái và tìm kiếm (có từ khoá, có lọc, không có kết quả) đều hiển thị đúng, không có cảnh báo PHP.
- Bản đồ Google không tải được trong môi trường chạy thử (bị chặn mạng) nên chưa xem được bản đồ thật.
- Không có trang nào bị cuộn ngang ở màn hình máy tính 1440px và điện thoại 390px.
- Lưu thông tin dự án, ảnh Loại hình và đăng ký 8 phần tử UX Builder đều hoạt động.

**Chưa kiểm tra được với Flatsome thật** (bạn dùng 3.20.11) vì đây là theme trả phí. Bản chạy thử dùng một theme giả lập các phần tử Section/Row/Col của Flatsome. Khi cài lên site thật, cần xem lại:
- Header trong suốt và nút ở header
- Tên các tuỳ chọn trong Theme Options (có thể khác chút tuỳ phiên bản)
- Tuỳ chọn Block cho menu
- Khoảng cách giữa các Section (chỉnh bằng Padding của Section trong UX Builder)

---

## 6. Cần bạn cung cấp hoặc quyết định

1. **Logo:** SVG hoặc PNG nền trong, gồm bản màu và bản trắng (cho footer), cộng favicon.
2. **Ảnh thật có bản quyền:**
   - Ảnh hero, ngang ≥ 1920px
   - Ảnh cho từng Loại hình và Khu vực
   - Ảnh đại diện từng dự án
   - Ảnh bài viết
3. **Danh sách dự án thật:** giá, vị trí, sản phẩm, trạng thái, dự án nào nổi bật hoặc "Hot".
4. ~~Trang chi tiết dự án~~ — đã chốt: mọi dự án dùng chung tư vấn viên và hotline (cài trong Tuỳ biến); dự án sẽ được nhập mới, không chuyển dữ liệu từ site cũ.
5. **Thông tin liên hệ:**
   - Hotline
   - Zalo: link Zalo OA hay số cá nhân?
   - Email, địa chỉ
   - Link Facebook, YouTube, TikTok
6. **Form đăng ký nhận tin:** gửi về email nào? Có cần đẩy sang Google Sheet hoặc CRM không?
7. **Menu chính thức:** các mục con của "Giới thiệu", "Liên hệ". Mega menu: đã chốt dùng cho mục "Dự án" (xem Bước 5).
8. **Nút trái tim trên thẻ dự án:** hiện chỉ lưu trong trình duyệt của khách, chưa có trang "Dự án đã lưu". Giữ, làm thêm trang đó, hay bỏ?
9. **Nội dung cam kết trong hero** ("Hỗ trợ 24/7"…) có đúng với dịch vụ thực tế không?
10. **Kỹ thuật:** hosting (có phải LiteSpeed không), tên miền. Phiên bản Flatsome: 3.20.11.
