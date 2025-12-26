<?php
session_start();

// Мультиязычность
$defaultLang = 'ru';
$availableLangs = ['ru', 'en', 'lt'];
$lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : $defaultLang);
if (in_array($lang, $availableLangs)) {
    $_SESSION['lang'] = $lang;
} else {
    $_SESSION['lang'] = $defaultLang;
}

$translations = [
    'ru' => include 'includes/languages/ru.php',
    'en' => include 'includes/languages/en.php',
    'lt' => include 'includes/languages/lt.php'
];
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations[$lang]['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #ff6f61, #ffd700); height: 100vh; display: flex; align-items: center; justify-content: center; }
        .install-container { background: rgba(255, 255, 255, 0.95); padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); max-width: 600px; width: 100%; }
        .btn-delete { background-color: #ff1e1e; border: 3px solid #cc0000; font-weight: bold; transition: all 0.3s; }
        .btn-delete:hover { background-color: #cc0000; box-shadow: 0 0 20px rgba(255, 30, 30, 0.8); transform: scale(1.05); }
        .btn-success { background-color: #00cc00; border: none; }
        .btn-success:hover { background-color: #00b300; box-shadow: 0 5px 15px rgba(0, 204, 0, 0.5); }
    </style>
</head>
<body>
    <div class="install-container">
        <h1 class="text-center mb-4"><?php echo $translations[$lang]['title']; ?></h1>
        <div class="text-center mb-4">
            <a href="?lang=ru" class="btn btn-link">Русский</a>
            <a href="?lang=lt" class="btn btn-link">Lietuvių</a>
            <a href="?lang=en" class="btn btn-link">English</a>
        </div>

        <?php
        if (file_exists('includes/config.php')) {
            echo '<p class="text-danger text-center fw-bold">' . $translations[$lang]['config_exists'] . '</p>';
            echo '<a href="?action=delete" class="btn btn-delete w-100 mt-3">' . $translations[$lang]['delete_config'] . '</a>';
        } else {
            if (isset($_POST['install'])) {
                $dbHost = $_POST['db_host'];
                $dbUser = $_POST['db_user'];
                $dbPass = $_POST['db_pass'];
                $dbName = $_POST['db_name'];

                if (!file_exists('includes')) mkdir('includes', 0755, true);
                $configContent = "<?php\n";
                $configContent .= "define('DB_HOST', '$dbHost');\n";
                $configContent .= "define('DB_USER', '$dbUser');\n";
                $configContent .= "define('DB_PASS', '$dbPass');\n";
                $configContent .= "define('DB_NAME', '$dbName');\n";
                file_put_contents('includes/config.php', $configContent);

                $conn = new mysqli($dbHost, $dbUser, $dbPass);
                if ($conn->connect_error) die("Ошибка подключения: " . $conn->connect_error);

                $conn->query("CREATE DATABASE IF NOT EXISTS $dbName");
                $conn->select_db($dbName);

                $queries = [
                    "CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, first_name VARCHAR(50), last_name VARCHAR(50), nickname VARCHAR(50), phone VARCHAR(20), photo VARCHAR(255), gallery TEXT, is_pro BOOLEAN DEFAULT 0, is_verified BOOLEAN DEFAULT 0, created_at DATETIME, last_active DATETIME, city VARCHAR(100))",
                    "CREATE TABLE tenders (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255), short_desc TEXT, full_desc TEXT, budget DECIMAL(15,2), owner_type ENUM('owner', 'agent', 'company'), start_date DATE, end_date DATE, category_id INT, city VARCHAR(100), user_id INT, status ENUM('published', 'moderation', 'closed') DEFAULT 'moderation', created_at DATETIME, updated_at DATETIME)",
                    "CREATE TABLE categories (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(100), description TEXT)",
                    "CREATE TABLE prices (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, category_id INT, title VARCHAR(100), price DECIMAL(10,2), quantity VARCHAR(50), custom_fields TEXT)",
                    "CREATE TABLE news (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255), meta_title VARCHAR(255), description TEXT, meta_description TEXT, content TEXT, images TEXT, files TEXT, is_on_main BOOLEAN DEFAULT 0, created_at DATETIME)",
                    "CREATE TABLE settings (id INT AUTO_INCREMENT PRIMARY KEY, logo_type ENUM('text', 'image'), logo_value VARCHAR(255), logo_size VARCHAR(50), logo_color VARCHAR(20), welcome_text TEXT, header_color VARCHAR(20), footer_color VARCHAR(20))",
                    "CREATE TABLE banners (id INT AUTO_INCREMENT PRIMARY KEY, position VARCHAR(50), size VARCHAR(50), image VARCHAR(255), link VARCHAR(255))",
                    "CREATE TABLE feedback (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), subject VARCHAR(255), message TEXT, file VARCHAR(255), created_at DATETIME)",
                    "CREATE TABLE cities (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100))"
                ];

                foreach ($queries as $query) $conn->query($query);

                $cities = ["Вильнюс", "Каунас", "Клайпеда", "Шяуляй", "Паневежис", "Алитус", "Мариямполе", "Мажейкяй", "Йонава", "Утена"];
                foreach ($cities as $city) $conn->query("INSERT INTO cities (name) VALUES ('$city')");

                $conn->query("INSERT INTO settings (logo_type, logo_value, header_color, footer_color) VALUES ('text', 'Tender CMS', '#ff6f61', '#ffd700')");
                $conn->close();

                echo '<p class="text-success text-center fw-bold">' . $translations[$lang]['success'] . '</p>';
                echo '<div class="d-flex justify-content-between mt-4">';
                echo '<a href="/index.php" class="btn btn-success w-50 me-2">' . $translations[$lang]['to_site'] . '</a>';
                echo '<a href="/admin/index.php" class="btn btn-success w-50 ms-2">' . $translations[$lang]['to_admin'] . '</a>';
                echo '</div>';
            } else {
                echo '<form method="POST" class="mt-4">';
                echo '<div class="mb-3"><label class="form-label">' . $translations[$lang]['host'] . '</label><input type="text" name="db_host" class="form-control" value="localhost" required></div>';
                echo '<div class="mb-3"><label class="form-label">' . $translations[$lang]['db_name'] . '</label><input type="text" name="db_name" class="form-control" required></div>';
                echo '<div class="mb-3"><label class="form-label">' . $translations[$lang]['user'] . '</label><input type="text" name="db_user" class="form-control" required></div>';
                echo '<div class="mb-3"><label class="form-label">' . $translations[$lang]['pass'] . '</label><input type="password" name="db_pass" class="form-control"></div>';
                echo '<button type="submit" name="install" class="btn btn-primary w-100">' . $translations[$lang]['install'] . '</button>';
                echo '</form>';
            }
        }

        if (isset($_GET['action']) && $_GET['action'] === 'delete') {
            if (file_exists('includes/config.php')) {
                unlink('includes/config.php');
                echo '<p class="text-success text-center fw-bold">' . $translations[$lang]['config_deleted'] . '</p>';
                echo '<a href="install.php" class="btn btn-success w-100 mt-3">Назад</a>';
            }
        }
        ?>
    </div>
</body>
</html>