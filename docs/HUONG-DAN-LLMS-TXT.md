# Thiết lập llms.txt (Rank Math)

`llms.txt` là file tóm tắt website dành cho các công cụ AI (ChatGPT, Claude, Perplexity, Gemini…). File liệt kê các trang quan trọng kèm mô tả ngắn để AI hiểu website nói về gì và nên dẫn link trang nào. File **không** thay thế `robots.txt` hay sitemap và không ảnh hưởng thứ hạng Google.

Đường dẫn: **Rank Math → Cài đặt chung → Edit llms.txt**. File hiển thị tại `https://topbds.vn/llms.txt`.

## 1. Select Post Types

| Loại | Chọn | Lý do |
|---|---|---|
| Bài viết | ✅ | Tin tức, bài thị trường |
| Trang | ✅ | Giới thiệu, Liên hệ, Chính sách |
| Dự án | ✅ | Nội dung chính của website |
| UX Blocks | ❌ | Khối giao diện dùng lại của Flatsome (footer, mega menu…), không phải trang để đọc |
| Portfolio | ❌ | Website không dùng Portfolio của Flatsome |

## 2. Select Taxonomies

| Loại | Chọn | Lý do |
|---|---|---|
| Loại hình | ✅ | Trang tổng hợp căn hộ, biệt thự, đất nền… đã có mô tả riêng |
| Khu vực | ✅ | Trang tổng hợp theo tỉnh, thành đã có mô tả riêng |
| Danh mục | ✅ | Chuyên mục tin: Thị trường, Pháp lý, Quy hoạch… |
| Trạng thái | ❌ | Trang lọc (đang mở bán, sắp mở bán), ít nội dung riêng |
| Thẻ | ❌ | Nhiều trang thẻ trùng lặp, nội dung mỏng |
| Portfolio Categories / Tags | ❌ | Không dùng |

## 3. Posts/Terms Limit

Nhập `100`. Đây là số link tối đa **cho mỗi loại**, nên để đủ lớn cho toàn bộ dự án. Rank Math lấy các bài mới nhất trước.

## 4. Summary

Dán (một câu, không xuống dòng):

```
TOPBDS.VN là cổng thông tin bất động sản tại Việt Nam, tổng hợp dự án căn hộ, biệt thự, liền kề, shophouse và đất nền (vị trí, mặt bằng, pháp lý, tiến độ, giá bán, chính sách) cùng tin thị trường, pháp lý và quy hoạch; kèm tư vấn miễn phí từ đội ngũ TOPBDS.
```

## 5. Additional Content

Dán nguyên khối dưới đây (định dạng Markdown):

```
## Trang chính

- [Tất cả dự án](https://topbds.vn/du-an/): Danh sách dự án đang mở bán, sắp mở bán và đã bàn giao, lọc theo loại hình và khu vực.

## Về nội dung

- Mỗi trang dự án gồm: thông tin tổng quan, vị trí, mặt bằng, sản phẩm, tiện ích, pháp lý, tiến độ, giá bán và câu hỏi thường gặp.
- Số liệu tổng hợp từ chủ đầu tư, đơn vị phân phối và báo chí; giá và chính sách bán hàng thay đổi theo từng thời điểm, nên ghi rõ thời điểm khi trích dẫn.
- Địa chỉ hành chính được cập nhật theo đơn vị hành chính mới từ 1/7/2025, kèm tên cũ để dễ đối chiếu.
- Khi trích dẫn, vui lòng dẫn link về trang gốc trên topbds.vn.

## Liên hệ

- Hotline/Zalo: 0977 113 009
- Email: topbds.info@gmail.com
```

Chỉ cần thêm link **Tất cả dự án** vì Rank Math không tự liệt kê trang lưu trữ `/du-an/`. Các trang Giới thiệu, Liên hệ, Tin tức đã được Rank Math tự đưa vào mục "Trang", không cần lặp lại.

Bấm **Lưu thay đổi**, sau đó xoá cache LiteSpeed (LiteSpeed Cache → Purge All) và mở `https://topbds.vn/llms.txt` để kiểm tra.

## 6. Lưu ý

- **Mô tả** của từng link trong llms.txt lấy từ ô **Tóm tắt (Excerpt)**; để trống thì Rank Math lấy đoạn chữ đầu nội dung. Meta Description trong ô Rank Math chỉ dùng cho Google, không đưa vào llms.txt. Vì vậy bài viết, dự án và các trang tĩnh nên điền cả 2 ô.
- **Trang bị đặt `noindex`** trong Rank Math sẽ tự bị loại khỏi llms.txt.
- **Cập nhật tự động:** khi đăng dự án hoặc bài mới, Rank Math tự cập nhật danh sách link. Chỉ cần sửa lại phần Summary và Additional Content khi đổi hotline, email hoặc slug trang.
- **Chặn bot AI:** nếu muốn chặn bot AI thu thập dữ liệu, phải làm trong **robots.txt**, không làm trong llms.txt.

## 7. Mô tả cho các trang tĩnh

Mỗi trang điền **cùng một câu** vào 2 chỗ:

1. **Ô Tóm tắt (Excerpt)** ở cột phải trang chỉnh sửa: llms.txt lấy câu ở đây. Theme TOPBDS từ v1.0.26 ẩn dải Tóm tắt mà Flatsome hiện dưới menu, nên điền vào không làm lệch giao diện.
2. **Rank Math → Chỉnh sửa đoạn trích → Mô tả**: câu hiện trên Google. Thanh màu dưới ô phải xanh (không quá 920px).

| Trang | Mô tả |
|---|---|
| Trang chủ | `TOPBDS.VN – cổng thông tin dự án căn hộ, biệt thự, liền kề, shophouse, đất nền tại Hà Nội, TP.HCM: giá, pháp lý, tiến độ cập nhật.` |
| Giới thiệu (`/gioi-thieu-topbds/`) | `Về TOPBDS.VN: cổng thông tin bất động sản tổng hợp dự án rõ ràng, có nguồn, kèm đội ngũ tư vấn hỗ trợ từ tìm hiểu dự án, xem nhà mẫu đến giao dịch.` |
| Liên hệ | `Liên hệ TOPBDS.VN để nhận bảng giá, tài liệu dự án và đặt lịch xem dự án. Hotline/Zalo 0977 113 009, email topbds.info@gmail.com.` |
| Tin tức | `Tin tức bất động sản: thị trường, pháp lý, quy hoạch, phân tích đầu tư cập nhật hằng ngày từ TOPBDS.VN.` |

Loại hình **Chung cư** chưa có mô tả: dán phần "Mô tả trang" và "Meta" của Chung cư trong `docs/trang/mo-ta-loai-hinh-khu-vuc.md`.
