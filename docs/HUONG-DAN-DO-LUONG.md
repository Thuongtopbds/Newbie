# Đo lường: Google Analytics và chuyển đổi Google Ads

## 1. Thẻ Google

Thẻ `G-FKB5PQQHK5` gửi dữ liệu cho cả Google Analytics (topbds.vn) và Google Ads (ThuongTOPBDS, `AW-17139374069`). Chỉ gắn thẻ này **một lần**, không dán thêm mã `AW-…` riêng.

Thẻ nằm ở file `wp-content/mu-plugins/topbds-google-tag.php` trên hosting, không nằm trong theme: đổi theme, cập nhật WordPress hay Flatsome đều không mất. Quản trị viên đang đăng nhập không bị đếm (muốn tự kiểm tra thì mở tab ẩn danh).

## 2. Sự kiện liên hệ (theme từ v1.0.44)

Theme tự gửi các sự kiện sau qua thẻ Google:

| Sự kiện | Khi nào | Tham số |
|---|---|---|
| `click_call` | Bấm số hotline (mọi link `tel:`) | `link_location`, `phone`, `project_name` |
| `click_zalo` | Bấm link Zalo (`zalo.me`) | `link_location`, `link_url`, `project_name` |
| `generate_lead` | Gửi form Contact Form 7 thành công | `form_name` (`bao_gia`, `dang_ky_footer`, hoặc `form_<id>`), `form_id`, `link_location`, `project_name` |

`link_location` cho biết khách bấm ở đâu: `header`, `dau_trang_du_an`, `the_bao_gia`, `banner_hai_ben`, `thanh_duoi_mobile`, `footer`, `noi_dung`.
`project_name` là tên dự án (H1), chỉ có khi khách đang ở trang chi tiết dự án.

## 3. Đánh dấu chuyển đổi

**Google Analytics** → Quản trị → Hiển thị dữ liệu → **Sự kiện chính** (Key events) → **Sự kiện chính mới** → gõ lần lượt `click_call`, `click_zalo`, `generate_lead` → Lưu.

**Google Ads** → Mục tiêu → **Lượt chuyển đổi** → **Tóm tắt** → **+ Hành động chuyển đổi mới** → **Nhập** → **Google Analytics 4 – Web** → tích 3 sự kiện trên → Nhập và tiếp tục.

Gợi ý giá trị trong Google Ads: `generate_lead` và `click_call` để **Chính** (Primary), `click_zalo` cũng Chính nếu khách chủ yếu nhắn Zalo. Sự kiện thường xuất hiện trong danh sách nhập sau 24–48 giờ kể từ lượt bấm đầu tiên.

## 4. Xem báo cáo

Analytics → Báo cáo → Tương tác → **Sự kiện**, bấm vào tên sự kiện. Muốn xem theo dự án hoặc vị trí bấm thì vào Quản trị → **Định nghĩa tuỳ chỉnh** → **Tạo phương diện tuỳ chỉnh** (phạm vi Sự kiện) cho `project_name`, `link_location`, `form_name`.
