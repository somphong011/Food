<?php
require_once 'db.php';

if (isset($_GET['delete_id'])) {
    $delete_id = filter_var($_GET['delete_id'], FILTER_VALIDATE_INT);
    if ($delete_id) {
        // ดึงชื่อไฟล์รูปมาลบออกจากโฟลเดอร์ uploads ก่อนลบข้อมูลใน DB
        $stmtImg = $pdo->prepare("SELECT image FROM foods WHERE id = ?");
        $stmtImg->execute([$delete_id]);
        $delFood = $stmtImg->fetch();
        if ($delFood && !empty($delFood['image']) && file_exists('uploads/' . $delFood['image'])) {
            unlink('uploads/' . $delFood['image']);
        }

        $stmt = $pdo->prepare("DELETE FROM foods WHERE id = ?");
        $stmt->execute([$delete_id]);
    }
    header("Location: index.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM foods ORDER BY id DESC");
$foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

$recipesByFood = [];
if (!empty($foods)) {
    $foodIds = array_column($foods, 'id');
    $placeholders = implode(',', array_fill(0, count($foodIds), '?'));
    $stmtRecipe = $pdo->prepare("SELECT * FROM recipes WHERE food_id IN ($placeholders)");
    $stmtRecipe->execute($foodIds);
    $allRecipes = $stmtRecipe->fetchAll(PDO::FETCH_ASSOC);
    foreach ($allRecipes as $recipe) {
        $recipesByFood[$recipe['food_id']][] = $recipe;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ระบบจัดการข้อมูลอาหารและสูตรอาหาร</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .food-thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-secondary">🍽️ รายการเมนูอาหารทั้งหมด</h2>
            <a href="manage.php" class="btn btn-primary">+ เพิ่มเมนูอาหารใหม่</a>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 15%" class="text-center">รูปภาพ</th>
                            <th style="width: 20%">ชื่ออาหาร (ไทย)</th>
                            <th style="width: 15%">หมวดหมู่</th>
                            <th style="width: 35%">วัตถุดิบและส่วนผสม</th>
                            <th style="width: 15%" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($foods)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">ยังไม่มีข้อมูลอาหารในระบบ</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($foods as $food): 
                                $recipes = $recipesByFood[$food['id']] ?? [];
                            ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if (!empty($food['image'])): ?>
                                            <img src="uploads/<?= htmlspecialchars($food['image']) ?>" class="food-thumbnail shadow-sm">
                                        <?php else: ?>
                                            <div class="bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center text-muted mx-auto" style="width: 80px; height: 80px; font-size: 0.8rem;">
                                                ไม่มีรูป
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars($food['name_th']) ?></strong></td>
                                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($food['category']) ?></span></td>
                                    <td>
                                        <?php if (!empty($recipes)): ?>
                                            <ul class="mb-0 ps-3">
                                                <?php foreach ($recipes as $r): ?>
                                                    <li>
                                                        <?= htmlspecialchars($r['recipe_name']) ?> 
                                                        <?= htmlspecialchars($r['quantity']) ?> 
                                                        <?= htmlspecialchars($r['unit_name']) ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <span class="text-muted small">ไม่มีข้อมูลวัตถุดิบ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="manage.php?id=<?= $food['id'] ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                                        <a href="index.php?delete_id=<?= $food['id'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบเมนูนี้? ข้อมูลวัตถุดิบและรูปภาพจะถูกลบไปด้วย');">ลบ</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>