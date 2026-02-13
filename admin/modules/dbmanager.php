<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
if (!isAdmin()) {
    header("Location: /admin/login.php");
    exit;
}

$table = $_GET['table'] ?? '';
$message = '';
$query_results = [];
$query_error = '';
$columns = [];

// Проверка существования таблицы sql_query_log
$check_table = $conn->query("SHOW TABLES LIKE 'sql_query_log'");
$log_table_exists = $check_table->num_rows > 0;

// Обработка операций с таблицами (удаление/сохранение)
if ($_POST['action'] ?? '' === 'delete' && $table && ($_POST['id'] ?? '')) {
    $pk = $conn->query("SHOW KEYS FROM `$table` WHERE Key_name='PRIMARY'")->fetch_assoc()['Column_name'] ?? 'id';
    $id = $conn->real_escape_string($_POST['id']);
    $sql = "DELETE FROM `$table` WHERE `$pk` = '$id'";
    if ($conn->query($sql)) {
        $message = '<div class="alert alert-warning alert-dismissible fade show"><strong>Успех!</strong> Строка удалена<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    } else {
        $message = '<div class="alert alert-danger alert-dismissible fade show"><strong>Ошибка!</strong> Не удалось удалить строку: ' . htmlspecialchars($conn->error) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

if ($_POST['action'] ?? '' === 'save' && $table) {
    $pk = $conn->query("SHOW KEYS FROM `$table` WHERE Key_name='PRIMARY'")->fetch_assoc()['Column_name'] ?? 'id';
    $id = $_POST['row_id'] ?? null;
    $updates = [];
    foreach ($_POST as $k => $v) {
        if (in_array($k, ['action', 'row_id', 'table'])) continue;
        $k = $conn->real_escape_string($k);
        $v = is_string($v) ? $conn->real_escape_string($v) : $v;
        $updates[] = "`$k` = " . (is_null($v) ? 'NULL' : "'$v'");
    }
    if ($id === 'new') {
        $sql = "INSERT INTO `$table` SET " . implode(', ', $updates);
    } else {
        $id = $conn->real_escape_string($id);
        $sql = "UPDATE `$table` SET " . implode(', ', $updates) . " WHERE `$pk` = '$id'";
    }
    if ($conn->query($sql)) {
        $message = '<div class="alert alert-success alert-dismissible fade show"><strong>Готово!</strong> Сохранено<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    } else {
        $message = '<div class="alert alert-danger alert-dismissible fade show"><strong>Ошибка!</strong> Не удалось сохранить: ' . htmlspecialchars($conn->error) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

// Обработка SQL-запросов из консоли
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sql']) && empty($table)) {
    $sql = trim($_POST['sql']);
    if (!empty($sql)) {
        // Подготовка к логированию
        $success = 1;
        $error_message = null;
        $user_id = $_SESSION['admin_id'] ?? null;

        try {
            $result = $conn->query($sql);
            if ($result) {
                if ($result === true) {
                    $message = '<div class="alert alert-success alert-dismissible fade show"><strong>Успех!</strong> Запрос выполнен. Затронуто строк: ' . $conn->affected_rows . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                } else {
                    $query_results = $result->fetch_all(MYSQLI_ASSOC);
                    $columns = array_keys($query_results[0] ?? []);
                    $message = '<div class="alert alert-success alert-dismissible fade show"><strong>Успех!</strong> Запрос выполнен. Найдено строк: ' . count($query_results) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                }
            } else {
                throw new Exception($conn->error);
            }
        } catch (Exception $e) {
            $success = 0;
            $error_message = $e->getMessage();
            $query_error = '<div class="alert alert-danger alert-dismissible fade show"><strong>Ошибка!</strong> ' . htmlspecialchars($error_message) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        }

        // Логирование запроса, если таблица существует
        if ($log_table_exists) {
            $stmt = $conn->prepare("INSERT INTO sql_query_log (query_text, success, error_message, user_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sisi", $sql, $success, $error_message, $user_id);
            $stmt->execute();
        } else {
            $message = '<div class="alert alert-warning alert-dismissible fade show"><strong>Предупреждение!</strong> Таблица sql_query_log не найдена. Логирование отключено.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        }
    } else {
        $message = '<div class="alert alert-warning alert-dismissible fade show"><strong>Предупреждение!</strong> Введите SQL-запрос.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

// Получение списка таблиц
$tables_res = $conn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
$tables = [];
while ($row = $tables_res->fetch_array()) {
    $tables[] = $row[0];
}
?>

<div class="container mt-4">
    <h1>Tortuga MySQL Commander</h1>
    <?php if (!empty($message)): ?>
        <div class="alert alert-dismissible fade show <?php echo strpos($message, 'Успех') !== false ? 'alert-success' : (strpos($message, 'Ошибка') !== false ? 'alert-danger' : 'alert-warning'); ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <div class="row g-4">
        <!-- Левая панель — таблицы -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-table"></i> Таблицы (<?= count($tables) ?>)
                </div>
                <div class="list-group list-group-flush overflow-auto" style="max-height: 70vh;">
                    <a href="?module=dbmanager" class="list-group-item list-group-item-action">
                        <i class="bi bi-terminal"></i> SQL Консоль
                    </a>
                    <?php foreach ($tables as $t):
                        $cnt = $conn->query("SELECT COUNT(*) FROM `$t`")->fetch_row()[0];
                    ?>
                        <a href="?module=dbmanager&table=<?= urlencode($t) ?>"
                           class="list-group-item list-group-item-action <?= $table === $t ? 'active' : '' ?>">
                            <strong><?= htmlspecialchars($t) ?></strong>
                            <span class="badge bg-secondary float-end"><?= $cnt ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <!-- Правая панель — данные или консоль -->
        <div class="col-lg-8">
            <div class="card h-100">
                <?php if ($table):
                    $pk = $conn->query("SHOW KEYS FROM `$table` WHERE Key_name='PRIMARY'")->fetch_assoc()['Column_name'] ?? 'id';
                    $cols = $conn->query("SHOW FULL COLUMNS FROM `$table`")->fetch_all(MYSQLI_ASSOC);
                    $rows = $conn->query("SELECT * FROM `$table` LIMIT 500")->fetch_all(MYSQLI_ASSOC);
                    $total = $conn->query("SELECT COUNT(*) FROM `$table`")->fetch_row()[0];
                ?>
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-table"></i> <?= htmlspecialchars($table) ?> — <?= $total ?> строк</span>
                        <small>Ctrl+S Сохранить • Ctrl+N Добавить • Ctrl+D Удалить</small>
                    </div>
                    <div class="card-body p-0 overflow-auto" style="max-height: 70vh;">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <?php foreach ($cols as $c): ?>
                                        <th><?= htmlspecialchars($c['Field']) ?> <?= $c['Key'] === 'PRI' ? '🔑' : '' ?></th>
                                    <?php endforeach; ?>
                                    <th width="150">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <form method="post">
                                            <input type="hidden" name="action" value="save">
                                            <input type="hidden" name="row_id" value="<?= htmlspecialchars($row[$pk]) ?>">
                                            <?php foreach ($cols as $c):
                                                $f = $c['Field'];
                                                $val = $row[$f] ?? '';
                                                if ($c['Key'] === 'PRI'): ?>
                                                    <td><code class="text-primary"><?= htmlspecialchars($val) ?></code></td>
                                                <?php else: ?>
                                                    <td><input name="<?= htmlspecialchars($f) ?>" value="<?= htmlspecialchars($val) ?>" class="form-control form-control-sm"></td>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <td>
                                                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check"></i></button>
                                                <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Удалить строку?')"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </form>
                                    </tr>
                                <?php endforeach; ?>
                                <!-- Новая строка -->
                                <tr class="table-light">
                                    <form method="post">
                                        <input type="hidden" name="action" value="save">
                                        <input type="hidden" name="row_id" value="new">
                                        <?php foreach ($cols as $c):
                                            if ($c['Extra'] === 'auto_increment'): ?>
                                                <td><em class="text-muted">auto</em></td>
                                            <?php else: ?>
                                                <td><input name="<?= htmlspecialchars($c['Field']) ?>" placeholder="<?= htmlspecialchars($c['Field']) ?>" class="form-control form-control-sm"></td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <td><button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i></button></td>
                                    </form>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="card-header"><i class="bi bi-terminal"></i> SQL Консоль</div>
                    <div class="card-body">
                        <form method="post">
                            <textarea name="sql" rows="12" class="form-control mb-3" placeholder="SELECT * FROM users LIMIT 50"><?php echo htmlspecialchars($_POST['sql'] ?? ''); ?></textarea>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-play"></i> Выполнить</button>
                        </form>
                        <?php if (!empty($query_error)): ?>
                            <?php echo $query_error; ?>
                        <?php endif; ?>
                        <?php if ($query_results): ?>
                            <div class="mt-4">
                                <h5>Результаты запроса (<?php echo count($query_results); ?> строк)</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <?php foreach ($columns as $col): ?>
                                                    <th><?php echo htmlspecialchars($col); ?></th>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($query_results as $row): ?>
                                                <tr>
                                                    <?php foreach ($row as $value): ?>
                                                        <td><?php echo htmlspecialchars($value ?? 'NULL'); ?></td>
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="footer mt-4 text-center">
        <small><i class="bi bi-turtle"></i> MySQL Commander — медленно, но с душой • 2025</small>
    </div>
</div>
<script>
    // Горячие клавиши
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            const saveForm = document.querySelector('form [value="save"]');
            if (saveForm) saveForm.closest('form').submit();
        }
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            const newForm = document.querySelector('form [value="new"]');
            if (newForm) newForm.closest('form').submit();
        }
        if (e.ctrlKey && e.key === 'd') {
            e.preventDefault();
            const deleteForm = document.querySelector('form [value="delete"]');
            if (deleteForm && confirm('Удалить строку?')) {
                deleteForm.closest('form').submit();
            }
        }
    });
</script>