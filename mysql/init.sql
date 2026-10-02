CREATE DATABASE IF NOT EXISTS recipe_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE recipe_db;

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    ingredients TEXT NOT NULL,
    instructions TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO recipes(name, category, ingredients, instructions) VALUES
(
    'Phở bò',
    'Món Việt',
    'Bánh phở, thịt bò, xương bò, hành, gừng, quế, hồi, nước mắm.',
    'Ninh xương lấy nước dùng. Rang thơm gia vị. Chần bánh phở, xếp thịt và chan nước dùng.'
),
(
    'Mì Ý sốt bò bằm',
    'Món Âu',
    'Mì spaghetti, thịt bò bằm, cà chua, hành tây, tỏi, dầu olive.',
    'Luộc mì. Xào thịt với hành tỏi, thêm cà chua. Trộn sốt với mì và dùng nóng.'
),
(
    'Gỏi cuốn',
    'Món Việt',
    'Bánh tráng, tôm, thịt, bún, xà lách, rau thơm.',
    'Nhúng bánh tráng, đặt nguyên liệu vào giữa rồi cuốn chặt. Dùng với nước chấm.'
);