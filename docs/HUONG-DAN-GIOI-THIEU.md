# Trang Giới thiệu cá nhân – Đinh Thưởng

Mẫu dán sẵn: `docs/ux-builder/gioi-thieu-dinh-thuong.txt`. CSS có sẵn trong theme từ bản 1.0.51 (class `tp-about-*`).

## 1. Dán vào trang

1. **Trang → Tất cả trang → Giới thiệu TOPBDS → Edit with UX Builder.**
2. Bấm biểu tượng **</>** (mở mã) ở góc trên, xoá hết nội dung cũ, dán toàn bộ file `gioi-thieu-dinh-thuong.txt`, bấm **Apply**.
3. Ảnh chứng chỉ: bấm vào khối ảnh có chữ `ID_ANH_CHUNG_CHI` → chọn ảnh trong Thư viện (nhớ che số CCCD trước khi tải lên).
4. Ảnh chân dung đang dùng ảnh tư vấn viên 500×500. Muốn ảnh nét hơn: tải ảnh mới (khoảng 800×800, khuôn mặt ở giữa) lên Thư viện, mở mã **</>**, thay đường dẫn trong `<img src="…">` ở khối Đầu trang.
5. **Update** → LiteSpeed Cache → **Purge All**.

## 2. Chỗ cần tự điền

Tất cả chỗ trong `[ngoặc vuông]`:

| Khối | Cần điền |
|---|---|
| Câu chuyện | Lý do bắt đầu làm môi giới năm 2019 (1–2 câu) |
| Hành nghề minh bạch | Số chứng chỉ, nơi cấp, ngày cấp; tên pháp lý đầy đủ của Đất Xanh Miền Bắc |
| Dự án tiêu biểu | Khu vực của HUD Mê Linh, Eurowindow Twin Parks; 1–2 câu vai trò và kết quả mỗi dự án |
| Khu vực am hiểu | Loại hình chuyên sâu ở Quảng Ninh, Phú Quốc, Đà Nẵng |
| Khách hàng nói gì | 2 lời nhận xét thật, có sự đồng ý của khách. Chưa có thì xoá cả Section "Khách hàng nói gì" |
| Ngoài công việc | 1–2 câu đời thường. Chưa có thì xoá cả Section "Ngoài công việc" |

Khi dự án tiêu biểu có trang trên TOPBDS, gắn link vào tên dự án: `<h3><a href="/du-an/ten-du-an/">Tên dự án</a></h3>`.

## 3. SEO (Rank Math, ở cuối trang sửa)

- **Tiêu đề:** `Đinh Thưởng – Môi giới bất động sản có chứng chỉ hành nghề | TOPBDS`
- **Mô tả:** `Đinh Thưởng, Giám đốc dự án tại Đất Xanh Miền Bắc, có 150 giao dịch thành công từ năm 2019. Tư vấn biệt thự và khu đô thị tại Hà Nội, Quảng Ninh, Phú Quốc, Đà Nẵng.`
- Trước khi đăng tên và logo Đất Xanh Miền Bắc, hỏi lại công ty xem có được phép dùng trên website cá nhân không.
