# Đề 29 - Website Công thức Nấu ăn / Recipe

## 1. Stack
- PHP 8.3 + PHP-FPM
- MySQL 8.4
- phpMyAdmin
- Nginx reverse proxy
- Prometheus + Grafana
- Loki + Promtail
- Docker Compose
- GitHub

## 2. Chạy dự án

```powershell
docker compose up -d --build
docker compose ps
```

Mở:
- Website: http://localhost:8080
- phpMyAdmin: http://localhost:8081
- Prometheus: http://localhost:9090
- Grafana: http://localhost:3000
- Loki: http://localhost:3100/ready

Grafana:
- user: admin
- password: admin123

## 3. Kiểm tra
```powershell
curl http://localhost:8080/health
curl http://localhost:8080/metrics
docker compose logs nginx
docker compose logs app
```

## 4. LogQL demo trong Grafana > Explore > Loki
```logql
{job="recipe_app"}
```

Lọc lỗi:
```logql
{job="recipe_app"} |= "error"
```

Log Nginx:
```logql
{job="nginx"}
```

## 5. Hardening dùng để báo cáo
- Nginx tắt server_tokens.
- Thêm X-Content-Type-Options, X-Frame-Options, Referrer-Policy.
- Chỉ public các cổng cần thiết cho demo.
- Database không expose port 3306 ra host.
- phpMyAdmin chỉ dùng cho quản trị, không phải frontend.
- App sử dụng prepared statements chống SQL injection.
- Mật khẩu thật nên đặt trong .env và không commit lên GitHub.
- Dữ liệu MySQL dùng named volume.
- Kiểm tra healthcheck trước khi app phụ thuộc DB.

## 6. Nội dung báo cáo đề xuất
1. Giới thiệu đề tài
2. Mục tiêu và yêu cầu
3. Kiến trúc hệ thống
4. Công nghệ sử dụng
5. Thiết kế CSDL
6. Docker Compose và triển khai
7. Nginx reverse proxy
8. Git/GitHub
9. Monitoring Prometheus + Grafana
10. Logging Loki + LogQL
11. Hardening
12. Kiểm thử
13. Kết quả và hình ảnh minh chứng
14. Kết luận
