<?php
declare(strict_types=1);

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME') . ";charset=utf8mb4";
        $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function logEvent(string $message): void {
    @file_put_contents('/var/log/recipe/app.log',
        date('c') . ' ' . $message . PHP_EOL, FILE_APPEND);
}

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/') ?: '/';

if ($path === '/metrics') {
    header('Content-Type: text/plain; version=0.0.4');
    $count = (int)db()->query("SELECT COUNT(*) FROM recipes")->fetchColumn();
    echo "# HELP recipe_total Number of recipes\n";
    echo "# TYPE recipe_total gauge\n";
    echo "recipe_total {$count}\n";
    echo "# HELP recipe_http_requests_total Total HTTP requests handled by PHP\n";
    echo "# TYPE recipe_http_requests_total counter\n";
    echo "recipe_http_requests_total 1\n";
    exit;
}

if ($path === '/health') {
    try { db()->query("SELECT 1"); echo "OK"; }
    catch (Throwable $e) { http_response_code(503); echo "DB ERROR"; }
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === '/add') {
        $stmt = db()->prepare("INSERT INTO recipes(name, category, ingredients, instructions) VALUES(?,?,?,?)");
        $stmt->execute([
            trim($_POST['name'] ?? ''),
            trim($_POST['category'] ?? ''),
            trim($_POST['ingredients'] ?? ''),
            trim($_POST['instructions'] ?? '')
        ]);
        logEvent("recipe_created name=" . str_replace(["\n","\r"], ' ', $_POST['name'] ?? ''));
        header('Location: /'); exit;
    }

    $category = trim($_GET['category'] ?? '');
    if ($category !== '') {
        $stmt = db()->prepare("SELECT * FROM recipes WHERE category=? ORDER BY created_at DESC");
        $stmt->execute([$category]);
        $recipes = $stmt->fetchAll();
    } else {
        $recipes = db()->query("SELECT * FROM recipes ORDER BY created_at DESC")->fetchAll();
    }

    $categories = db()->query("SELECT DISTINCT category FROM recipes ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
} catch (Throwable $e) {
    logEvent("error=" . $e->getMessage());
    http_response_code(500);
    echo "<h1>Lỗi hệ thống</h1><p>Không thể truy cập dữ liệu.</p>";
    exit;
}
?>
<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Recipe Hub</title>
<style>
body{font-family:Arial,sans-serif;background:#f7f7f7;margin:0;color:#222}
header{background:#222;color:#fff;padding:22px;text-align:center}
main{max-width:1100px;margin:25px auto;padding:0 16px}
.card{background:#fff;border-radius:12px;padding:18px;margin:14px 0;box-shadow:0 2px 8px #ddd}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px}
input,textarea,select{width:100%;box-sizing:border-box;padding:10px;margin:6px 0 12px;border:1px solid #ccc;border-radius:7px}
button{background:#222;color:#fff;border:0;padding:10px 18px;border-radius:7px;cursor:pointer}
a{color:#222;text-decoration:none;margin-right:12px}
.small{color:#666;font-size:13px}
</style>
</head>
<body>
<header><h1>🍳 Recipe Hub</h1><div>Website chia sẻ công thức nấu ăn</div></header>
<main>
<div class="card">
<h2>Thêm công thức</h2>
<form method="post" action="/add">
<input name="name" placeholder="Tên món ăn" required>
<input name="category" placeholder="Danh mục: Món Việt, Món Âu..." required>
<textarea name="ingredients" rows="4" placeholder="Nguyên liệu" required></textarea>
<textarea name="instructions" rows="5" placeholder="Cách làm" required></textarea>
<button>Thêm công thức</button>
</form>
</div>
<div class="card">
<strong>Lọc danh mục:</strong>
<a href="/">Tất cả</a>
<?php foreach ($categories as $c): ?><a href="?category=<?=urlencode($c)?>"><?=e($c)?></a><?php endforeach; ?>
</div>
<div class="grid">
<?php foreach ($recipes as $r): ?>
<article class="card">
<h2><?=e($r['name'])?></h2>
<div class="small">Danh mục: <?=e($r['category'])?> · <?=e($r['created_at'])?></div>
<h3>Nguyên liệu</h3><p><?=nl2br(e($r['ingredients']))?></p>
<h3>Cách làm</h3><p><?=nl2br(e($r['instructions']))?></p>
</article>
<?php endforeach; ?>
</div>
</main>
</body>
</html>
