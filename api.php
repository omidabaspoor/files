<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

// --- تنظیمات دیتابیس ---
// $host = 'localhost';
// $db   = 'rematch'; // نام دیتابیس
// $user = 'root';    // یوزر دیتابیس
// $pass = '';        // رمز دیتابیس
// $charset = 'utf8mb4';


$host = 'localhost';
$db   = 'rematch'; // نام دیتابیس
$user = 'root';    // یوزر دیتابیس
$pass = '';        // رمز دیتابیس
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // ایجاد جداول ضروری اگر وجود نداشته باشند
    $pdo->exec("CREATE TABLE IF NOT EXISTS vip_members (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    first_name VARCHAR(100), 
    last_name VARCHAR(100), 
    phone VARCHAR(20), 
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

    $pdo->exec("CREATE TABLE IF NOT EXISTS site_stats (id INT PRIMARY KEY DEFAULT 1, total_views BIGINT DEFAULT 0)");
    $pdo->exec("INSERT IGNORE INTO site_stats (id, total_views) VALUES (1, 0)");
    
    // اضافه کردن ستون sort_order به جدول categories اگر وجود ندارد (برای ورژن‌های قدیمی)
    try {
        $pdo->query("SELECT sort_order FROM categories LIMIT 1");
    } catch (Exception $e) {
        $pdo->exec("ALTER TABLE categories ADD COLUMN sort_order INT DEFAULT 0");
    }

} catch (\PDOException $e) {
    echo json_encode(['status'=>'error', 'message'=>'Database Error: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? '';
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

// =========================================================
// 1. لاگین
// =========================================================
if ($action == 'login') {
    if($data['username'] == 'hassanrematch' && $data['password'] == 'hassan12345678910') {
        echo json_encode(['status'=>'success']);
    } else {
        echo json_encode(['status'=>'error', 'message'=>'نام کاربری یا رمز عبور اشتباه است']);
    }

// =========================================================
// 2. دریافت اطلاعات (اصلاح شده با ترتیب sort_order)
// =========================================================
} elseif ($action == 'get_data') {
    // محصولات
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
    
    // دسته‌ها: اول بر اساس sort_order (صعودی)، سپس id
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC, id ASC");
    $categories = $stmt->fetchAll();
    
    echo json_encode(['products' => $products, 'categories' => $categories]);

// =========================================================
// 3. ذخیره/ویرایش محصول
// =========================================================
} elseif ($action == 'save_product') {
    $name = $_POST['name'];
    $price = str_replace(',', '', $_POST['price']); 
    $disc = $_POST['discount'];
    $cat = $_POST['category']; 
    $desc = $_POST['description'];
    $id = $_POST['id'] ?? null;
    
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        if (!file_exists('image')) { mkdir('image', 0777, true); }
        $target = 'image/' . time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $imagePath = $target;
    }
    
    if ($id) {
        $sql = "UPDATE products SET name=?, price=?, discount=?, category_slug=?, description=?";
        $params = [$name, $price, $disc, $cat, $desc];
        if($imagePath) { $sql .= ", image=?"; $params[] = $imagePath; }
        $sql .= " WHERE id=?"; $params[] = $id;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } else {
        $img = $imagePath ? $imagePath : 'image/cafe.png';
        $stmt = $pdo->prepare("INSERT INTO products (name, price, discount, category_slug, description, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $price, $disc, $cat, $desc, $img]);
    }
    echo json_encode(['status'=>'success']);

// =========================================================
// 4. حذف محصول
// =========================================================
} elseif ($action == 'delete_product') {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
    $stmt->execute([$data['id']]);
    echo json_encode(['status'=>'success']);

// =========================================================
// 5. تخفیف کلی
// =========================================================
} elseif ($action == 'bulk_discount') {
    $stmt = $pdo->prepare("UPDATE products SET discount = ?");
    $stmt->execute([$data['percent']]);
    echo json_encode(['status'=>'success']);

// =========================================================
// 6. مدیریت دسته‌ها (افزودن، حذف، تغییر ترتیب)
// =========================================================
} elseif ($action == 'add_category') {
    $name = $data['name'];
    $slug = str_replace(' ', '-', $name);
    // پیش فرض ترتیب 0
    $stmt = $pdo->prepare("INSERT INTO categories (name, slug, sort_order) VALUES (?, ?, 0)");
    $stmt->execute([$name, $slug]);
    echo json_encode(['status'=>'success']);

} elseif ($action == 'delete_category') {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
    $stmt->execute([$data['id']]);
    echo json_encode(['status'=>'success']);

} elseif ($action == 'update_category_order') {
    $stmt = $pdo->prepare("UPDATE categories SET sort_order = ? WHERE id = ?");
    $stmt->execute([$data['order'], $data['id']]);
    echo json_encode(['status'=>'success']);

// =========================================================
// 7. ثبت سفارش
// =========================================================
} elseif ($action == 'create_order') {
    $items = $data['items'];
    $total = $data['total'];
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO orders (total_price, items_count, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$total, count($items)]);
        $orderId = $pdo->lastInsertId();
        
        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_name, quantity, price_each) VALUES (?, ?, ?, ?)");
        foreach($items as $item) {
            $stmtItem->execute([$orderId, $item['name'], $item['qty'], $item['price']]);
        }
        
        $pdo->commit();
        echo json_encode(['status'=>'success']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status'=>'error', 'message'=>$e->getMessage()]);
    }

// =========================================================
// 8. دریافت لیست سفارشات
// =========================================================
} elseif ($action == 'get_orders') {
    $pending = $pdo->query("SELECT * FROM orders WHERE status = 'pending' ORDER BY created_at ASC")->fetchAll();
    
    foreach($pending as &$ord) {
        $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$ord['id']]);
        $ord['items'] = $stmt->fetchAll();
    }
    echo json_encode(['pending' => $pending]);

// =========================================================
// 9. تغییر وضعیت سفارش
// =========================================================
} elseif ($action == 'complete_order') {
    $stmt = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
    $stmt->execute([$data['id']]);
    echo json_encode(['status'=>'success']);

} elseif ($action == 'cancel_order') {
    $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
    $stmt->execute([$data['id']]);
    echo json_encode(['status'=>'success']);

// =========================================================
// گزارش جامع تاریخچه سفارشات (برای بخش جدید پنل ادمین)
// =========================================================
} elseif ($action == 'get_orders_report') {
    $status = $_GET['status'] ?? 'all';
    $period = $_GET['period'] ?? 'all';
    $from_date = $_GET['from_date'] ?? '';
    $to_date = $_GET['to_date'] ?? '';
    
    $whereClauses = [];
    $params = [];
    
    if ($status !== 'all' && in_array($status, ['pending', 'completed', 'cancelled'])) {
        $whereClauses[] = "status = ?";
        $params[] = $status;
    }
    
    if (!empty($from_date)) {
        $whereClauses[] = "DATE(created_at) >= ?";
        $params[] = $from_date;
    }
    if (!empty($to_date)) {
        $whereClauses[] = "DATE(created_at) <= ?";
        $params[] = $to_date;
    }
    
    if (empty($from_date) && empty($to_date)) {
        if ($period === 'today') {
            $whereClauses[] = "DATE(created_at) = CURDATE()";
        } elseif ($period === 'week') {
            $whereClauses[] = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } elseif ($period === 'month') {
            $whereClauses[] = "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }
    }
    
    $whereSql = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";
    
    $sql = "SELECT * FROM orders $whereSql ORDER BY id DESC LIMIT 500";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
    
    foreach($orders as &$ord) {
        $stmtItem = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmtItem->execute([$ord['id']]);
        $ord['items'] = $stmtItem->fetchAll();
    }
    echo json_encode(['orders' => $orders]);

// =========================================================
// 10. آمار و ارقام
// =========================================================
} elseif ($action == 'get_stats') {
    $today = $pdo->query("SELECT COUNT(*) as count, COALESCE(SUM(total_price),0) as total FROM orders WHERE DATE(created_at) = CURDATE() AND status='completed'")->fetch();
    $week = $pdo->query("SELECT COUNT(*) as count, COALESCE(SUM(total_price),0) as total FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND status='completed'")->fetch();
    $month = $pdo->query("SELECT COUNT(*) as count, COALESCE(SUM(total_price),0) as total FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND status='completed'")->fetch();
    $views = $pdo->query("SELECT total_views FROM site_stats WHERE id = 1")->fetchColumn();
    
    echo json_encode(['today' => $today, 'week' => $week, 'month' => $month, 'views' => $views]);

} elseif ($action == 'get_product_stats') {
    $sql = "SELECT product_name as name, SUM(quantity) as qty, SUM(quantity * price_each) as total_rev 
            FROM order_items 
            JOIN orders ON orders.id = order_items.order_id 
            WHERE orders.status = 'completed' 
            GROUP BY product_name 
            ORDER BY qty DESC LIMIT 20";
    $stats = $pdo->query($sql)->fetchAll();
    echo json_encode($stats);

} elseif ($action == 'inc_visit') {
    $pdo->exec("UPDATE site_stats SET total_views = total_views + 1 WHERE id = 1");
    echo json_encode(['status'=>'success']);

} elseif ($action == 'inc_view') {
    $slug = $data['slug'] ?? '';
    if ($slug) {
        $stmt = $pdo->prepare("UPDATE categories SET views = views + 1 WHERE slug = ?");
        $stmt->execute([$slug]);
    }
    echo json_encode(['status'=>'success']);

// =========================================================
// COFFEE LINES API ENDPOINTS
// =========================================================

// Get Coffee Lines (for frontend)
} elseif ($action == 'get_coffee_lines') {
    $stmt = $pdo->query("SELECT * FROM coffee_lines WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
    $lines = $stmt->fetchAll();
    echo json_encode(['lines' => $lines]);

// Get All Coffee Lines (for admin)
} elseif ($action == 'get_all_coffee_lines') {
    $stmt = $pdo->query("SELECT * FROM coffee_lines ORDER BY sort_order ASC, id ASC");
    $lines = $stmt->fetchAll();
    echo json_encode(['lines' => $lines]);

// Save Coffee Line (Add/Edit)
} elseif ($action == 'save_coffee_line') {
    $name = $data['name'] ?? '';
    $slug = $data['slug'] ?? str_replace(' ', '-', $name);
    $blend_ratio = $data['blend_ratio'] ?? '';
    $beans_type = $data['beans_type'] ?? '';
    $origin = $data['origin'] ?? '';
    $process = $data['process'] ?? '';
    $roast_level = $data['roast_level'] ?? '';
    $flavor_notes = $data['flavor_notes'] ?? '';
    $description = $data['description'] ?? '';
    $short_desc = $data['short_desc'] ?? '';
    $is_active = $data['is_active'] ?? 1;
    $id = $data['id'] ?? null;
    
    if ($id) {
        $stmt = $pdo->prepare("UPDATE coffee_lines SET name=?, slug=?, blend_ratio=?, beans_type=?, origin=?, process=?, roast_level=?, flavor_notes=?, description=?, short_desc=?, is_active=? WHERE id=?");
        $stmt->execute([$name, $slug, $blend_ratio, $beans_type, $origin, $process, $roast_level, $flavor_notes, $description, $short_desc, $is_active, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO coffee_lines (name, slug, blend_ratio, beans_type, origin, process, roast_level, flavor_notes, description, short_desc, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $blend_ratio, $beans_type, $origin, $process, $roast_level, $flavor_notes, $description, $short_desc, $is_active]);
    }
    echo json_encode(['status'=>'success']);

// Delete Coffee Line
} elseif ($action == 'delete_coffee_line') {
    $stmt = $pdo->prepare("DELETE FROM coffee_lines WHERE id=?");
    $stmt->execute([$data['id']]);
    echo json_encode(['status'=>'success']);

// Update Coffee Line Order
} elseif ($action == 'update_coffee_line_order') {
    $stmt = $pdo->prepare("UPDATE coffee_lines SET sort_order = ? WHERE id = ?");
    $stmt->execute([$data['order'], $data['id']]);
    echo json_encode(['status'=>'success']);
}









if (isset($_GET['action']) && $_GET['action'] == 'join_vip_club') {
    // پاک کردن هرگونه خروجی اضافی قبل از ارسال JSON
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        // دریافت اطلاعات فرم
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        $firstName = trim($data['first_name'] ?? '');
        $lastName = trim($data['last_name'] ?? '');
        $phone = trim($data['phone'] ?? '');

        // اعتبارسنجی
        if (empty($firstName) || empty($lastName) || empty($phone)) {
            echo json_encode(['success' => false, 'message' => 'لطفاً تمامی فیلدها را پر کنید.']);
            exit;
        }

        // بررسی شماره تکراری
        $stmt = $pdo->prepare("SELECT id FROM vip_members WHERE phone = ?");
        $stmt->execute([$phone]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'این شماره موبایل قبلاً ثبت شده است.']);
            exit;
        }

        // ثبت در دیتابیس
        $stmt = $pdo->prepare("INSERT INTO vip_members (first_name, last_name, phone) VALUES (?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $phone]);

        echo json_encode(['success' => true, 'message' => 'عضویت شما با موفقیت ثبت شد.']);

    } catch (\Throwable $e) {
        // مدیریت دقیق خطاها (مثل نبودن جدول در دیتابیس)
        echo json_encode(['success' => false, 'message' => 'خطای سرور: ' . $e->getMessage()]);
    }
    exit;
}



// هندل کردن ثبت نام VIP
if ($action === 'join_vip_club') {
    $firstName = trim($data['first_name'] ?? '');
    $lastName  = trim($data['last_name'] ?? '');
    $phone     = trim($data['phone'] ?? '');

    if (empty($firstName) || empty($lastName) || empty($phone)) {
        echo json_encode(['success' => false, 'message' => 'لطفاً تمامی فیلدها را پر کنید.']);
        exit;
    }

    try {
        // بررسی شماره تکراری
        $stmt = $pdo->prepare("SELECT id FROM vip_members WHERE phone = ?");
        $stmt->execute([$phone]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'این شماره موبایل قبلاً ثبت شده است.']);
            exit;
        }

        // ثبت عضو جدید
        $stmt = $pdo->prepare("INSERT INTO vip_members (first_name, last_name, phone) VALUES (?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $phone]);
        
        echo json_encode(['success' => true, 'message' => 'عضویت شما با موفقیت ثبت شد.']);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'خطای دیتابیس: ' . $e->getMessage()]);
        exit;
    }
}









if ($action === 'get_vip_members') {
    try {
        $stmt = $pdo->query("SELECT * FROM vip_members ORDER BY id DESC");
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'members' => $members]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}



?>
