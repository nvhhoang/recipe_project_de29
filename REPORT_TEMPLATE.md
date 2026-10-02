# BÁO CÁO ĐỀ TÀI 29 - WEBSITE CÔNG THỨC NẤU ĂN

## 1. Thông tin
- Môn: Triển khai và Quản trị Hệ thống Phần mềm
- Đề tài: Website Công thức Nấu ăn / Recipe
- Sinh viên: ................................
- Lớp: ................................
- GitHub: ................................

## 2. Mục tiêu
Xây dựng website chia sẻ công thức nấu ăn, có dữ liệu MySQL, reverse proxy Nginx,
quản trị CSDL bằng phpMyAdmin, giám sát bằng Prometheus/Grafana và tập trung log bằng Loki.

## 3. Kiến trúc
Người dùng -> Nginx:8080 -> PHP-FPM -> MySQL
                         -> Prometheus -> Grafana
                         -> Promtail -> Loki -> Grafana

## 4. CSDL
Bảng `recipes` gồm:
- id
- name
- category
- ingredients
- instructions
- created_at

## 5. Triển khai
Chạy:
`docker compose up -d --build`

Ảnh minh chứng cần chụp:
- docker compose ps
- website
- phpMyAdmin
- Grafana
- Prometheus targets
- Grafana Explore với LogQL
- hardening header bằng DevTools/curl

## 6. Monitoring
Prometheus lấy metric từ `/metrics`.
Grafana hiển thị số lượng công thức và metric HTTP.

## 7. Logging
Promtail đọc `app.log` và log Nginx rồi gửi về Loki.
Ví dụ LogQL:
`{job="recipe_app"}`
`{job="recipe_app"} |= "error"`

## 8. Hardening
- Ẩn version Nginx.
- Security headers.
- Không expose MySQL.
- Prepared statements.
- Giới hạn request body.
- Không commit secret.

## 9. Kiểm thử
- Thêm công thức mới.
- Lọc theo danh mục.
- Kiểm tra `/health`.
- Kiểm tra `/metrics`.
- Kiểm tra log sau khi tạo món ăn.
- Kiểm tra Grafana và Loki.

## 10. Kết luận
Hệ thống đáp ứng các thành phần triển khai, CSDL, reverse proxy, monitoring,
logging và hardening theo yêu cầu đề tài.
