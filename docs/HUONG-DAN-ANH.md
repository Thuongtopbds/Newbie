# Hướng dẫn kích thước ảnh – TOPBDS.VN

Bảng này dựa trên đúng các khung ảnh mà theme đang dùng. Chỉ cần tải ảnh **đủ lớn và đúng tỉ lệ**. WordPress tự cắt ra các bản nhỏ hơn cho từng vị trí, bạn không phải tự cắt nhiều cỡ.

## Bảng kích thước

| # | Loại ảnh | Tải ở đâu | Kích thước nên tải | Tỉ lệ | Dung lượng tối đa | Theme dùng ở |
|---|---|---|---|---|---|---|
| 1 | **Ảnh hero trang chủ** | Thư viện → chọn làm ảnh nền Section "Hero" trong UX Builder | **1920 × 1080** | 16:9 | 400 KB | Nền hero, tràn toàn màn hình |
| 2 | **Ảnh đại diện dự án** | Sửa dự án → khung "Ảnh đại diện" (cột phải) | **1920 × 1080** | 16:9 | 400 KB | Nền hero trang dự án, thẻ dự án (cắt 640 × 420), mega menu, xem phóng to |
| 3 | **Thư viện ảnh dự án** | Sửa dự án → "Thông tin dự án" → Thư viện ảnh | **1600 × 1067** (ảnh ngang) | 3:2 | 350 KB/ảnh | Lưới ảnh khảm, xem phóng to |
| 4 | **Ảnh trong nội dung dự án / bài viết** (mặt bằng, tiện ích, bảng giá…) | Nút "Thêm media" trong trình soạn thảo | Rộng **1600**, cao tuỳ ảnh | Tuỳ ý | 350 KB | Hiển thị rộng tối đa ~800px, bấm xem lớn |
| 5 | **Ảnh Loại hình** (Căn hộ, Biệt thự…) | Dự án → Loại hình → sửa mục → "Chọn ảnh" | **1040 × 800** | 13:10 | 200 KB | Ô "Khám phá theo loại hình" (cắt 520 × 400) |
| 6 | **Ảnh Khu vực** (Hà Nội, TP. HCM…) | Dự án → Khu vực → sửa mục → "Chọn ảnh" | **1040 × 800** | 13:10 | 200 KB | Ô "Tìm kiếm theo khu vực" (cắt 520 × 400) |
| 7 | **Ảnh đại diện bài viết** (tin tức) | Sửa bài → "Ảnh đại diện" | **1200 × 675** | 16:9 | 250 KB | Tin lớn trang chủ (cắt 880 × 500), ảnh nhỏ danh sách (cắt 240 × 160) |
| 8 | **Ảnh tư vấn viên** | Giao diện → Tuỳ biến → TOPBDS – Liên hệ → Ảnh tư vấn viên | **400 × 400** | 1:1 (vuông) | 80 KB | Ảnh tròn trong thẻ "Nhận báo giá" |
| 9 | **Ảnh chia sẻ mạng xã hội** (Facebook, Zalo) | Rank Math → tab Social của trang/dự án | **1200 × 630** | 1.91:1 | 300 KB | Ảnh hiện khi dán link lên Facebook/Zalo |
| 10 | **Logo** | Theme Options → Header → Logo & Site Identity | Có sẵn: `brand/logo-topbds.png` (608 × 124) | ~5:1 | — | Header, footer |
| 11 | **Favicon** | Giao diện → Tuỳ biến → Thông tin website → Biểu tượng trang web | Có sẵn: `brand/icon-topbds-512.png` (512 × 512) | 1:1 | — | Tab trình duyệt, biểu tượng khi lưu ra màn hình điện thoại |

> Kích thước "nên tải" đã gấp đôi kích thước hiển thị, để ảnh nét trên điện thoại và màn hình độ phân giải cao. Tải ảnh nhỏ hơn thì ảnh sẽ bị mờ; tải lớn hơn nhiều thì chỉ làm nặng site mà không nét hơn.

## Vùng an toàn: ảnh bị cắt thế nào

Theme cắt ảnh **từ giữa ra**. Hãy đặt chủ thể (toà nhà, mặt tiền) **ở giữa khung**, tránh sát mép.

- **Ảnh đại diện dự án (#2):** đây là ảnh bị cắt nhiều nhất vì dùng cho nhiều khung khác nhau.
  - Ở hero trang dự án trên máy tính, khung rất dẹt (khoảng 3:1), chỉ giữ lại **dải giữa, khoảng một nửa đến 2/3 chiều cao ảnh**; phần trên và dưới bị cắt. Ảnh chụp toà nhà từ xa (flycam), trời phía trên, là đẹp nhất.
  - Ở thẻ dự án, khung 3:2 cắt bớt hai bên.
  - Vì vậy: toà nhà ở chính giữa, không đặt chữ, logo hay số liệu lên ảnh.
- **Ảnh Loại hình / Khu vực (#5, #6):** khoảng 1/3 dưới của ô bị phủ lớp tối để hiện chữ tên mục và số dự án. Chủ thể nên nằm ở nửa trên hoặc giữa ảnh.
- **Ảnh hero trang chủ (#1):** nửa trái ảnh bị phủ tối để hiện tiêu đề và ô tìm kiếm, góc phải có khung "cam kết". Phần đẹp nhất của ảnh nên nằm **ở giữa**, như ảnh toà nhà ban đêm hiện tại.
- **Thư viện ảnh (#3):** ảnh đầu tiên hiện lớn nhất trong lưới khảm, nên chọn ảnh đẹp nhất. Mặt bằng hay bảng giá có nhiều chữ thì **đặt trong nội dung bài (#4)**, không đặt vào thư viện, vì trong lưới khảm ảnh bị cắt.

## Định dạng và nén ảnh

- **Định dạng:** JPG hoặc WebP cho ảnh chụp. PNG chỉ dùng cho ảnh có nền trong suốt (logo) hoặc ảnh chụp màn hình có nhiều chữ.
- **Nén trước khi tải lên:** dùng https://squoosh.app hoặc https://tinypng.com, chất lượng khoảng 75–82%. Mắt thường gần như không thấy khác, nhưng dung lượng giảm 3–5 lần.
- Nếu dùng **LiteSpeed Cache**, bật "Image Optimization" để tự chuyển sang WebP.
- **Tên file** viết không dấu, nối bằng gạch ngang, có từ khoá, tốt cho SEO ảnh. VD: `chung-cu-van-bay-van-don-phoi-canh.jpg`, không đặt `IMG_2034.jpg`.
- **Văn bản thay thế (Alt text):** điền trong Thư viện khi tải ảnh, mô tả ngắn nội dung ảnh. VD: "Phối cảnh chung cư Vân Bay nhìn từ vịnh Bái Tử Long".

## Ảnh đã tải từ trước

Ảnh tải lên **trước khi** kích hoạt child theme sẽ chưa có các bản cắt `tp-card`, `tp-tile`, `tp-news`, `tp-thumb`. Cài plugin **Regenerate Thumbnails**, chạy một lần cho toàn bộ ảnh, rồi có thể gỡ plugin.
