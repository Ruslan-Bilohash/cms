<?php
require_once '/public_html/includes/db.php';
$settings = include '/public_html/uploads/site_settings.php';
if ($settings['backup']['auto_backup'] && $settings['backup']['frequency'] === $argv[1]) {
    $backup_dir = '/public_html/backups/';
    $filename = create_backup($conn, $backup_dir); // Полный бэкап
    $backups = glob($backup_dir . '*.sql');
    if (count($backups) > $settings['backup']['max_backups']) {
        array_map('unlink', array_slice($backups, 0, count($backups) - $settings['backup']['max_backups']));
    }
}

function create_backup($conn, $backup_dir, $tables = []) {
    $filename = $backup_dir . 'backup_' . date('Ymd_His') . '.sql';
    $sql = "-- Бэкап базы данных  " . date('Y-m-d H:i:s') . "\n\n";
    $result = $conn->query("SHOW TABLES");
    $all_tables = [];
    while ($row = $result->fetch_array(MYSQLI_NUM)) {
        $all_tables[] = $row[0];
    }
    $tables_to_backup = empty($tables) ? $all_tables : array_intersect($tables, $all_tables);
    foreach ($tables_to_backup as $table) {
        $sql .= "-- Таблица: $table\n";
        $sql .= "DROP TABLE IF EXISTS `$table`;\n";
        $create_table = $conn->query("SHOW CREATE TABLE `$table`");
        $row = $create_table->fetch_assoc();
        $sql .= $row['Create Table'] . ";\n\n";
        $rows = $conn->query("SELECT * FROM `$table`");
        while ($row = $rows->fetch_assoc()) {
            $values = array_map(fn($v) => $conn->real_escape_string($v === null ? 'NULL' : $v), $row);
            $sql .= "INSERT INTO `$table` VALUES ('" . implode("','", $values) . "');\n";
        }
        $sql .= "\n";
    }
    file_put_contents($filename, $sql);
    return $filename;
}
?>
