<?php
require_once 'db.php';
$id = $_GET['id'] ?? null;
$food = ['name_th' => '', 'category' => '', 'image' => ''];
$recipes = [];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM foods WHERE id = ?");
    $stmt->execute([$id]);
    $food = $stmt->fetch();
    
    if (!$food) {
        header("Location: index.php");
        exit;
    }
    
    $stmtR = $pdo->prepare("SELECT * FROM recipes WHERE food_id = ?");
    $stmtR->execute([$id]);
    $recipes = $stmtR->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_th = $_POST['name_th'];
    $category = $_POST['category'];
    $image = $food['image'] ?? null; // ใช้รูปเดิมเป็นค่าเริ่มต้นถ้าไม่ได้อัปโหลดใหม่
    
    // จัดการการอัปโหลดรูปภาพ
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        // ตั้งชื่อไฟล์ใหม่เพื่อป้องกันชื่อซ้ำกัน
        $new_name = time() . '_' . basename($_FILES['image']['name']);
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_name)) {
            $image = $new_name;
        }
    }
    
    if ($id) {
        $stmt = $pdo->prepare("UPDATE foods SET name_th = ?, category = ?, image = ? WHERE id = ?");
        $stmt->execute([$name_th, $category, $image, $id]);
        $pdo->prepare("DELETE FROM recipes WHERE food_id = ?")->execute([$id]);
        $food_id = $id;
    } else {
        $stmt = $pdo->prepare("INSERT INTO foods (name_th, category, image) VALUES (?, ?, ?)");
        $stmt->execute([$name_th, $category, $image]);
        $food_id = $pdo->lastInsertId();
    }
    
    if (isset($_POST['recipes']) && is_array($_POST['recipes'])) {
        $stmtRecipe = $pdo->prepare("INSERT INTO recipes (food_id, recipe_name, quantity, unit_name) VALUES (?, ?, ?, ?)");
        foreach ($_POST['recipes'] as $r) {
            if (!empty($r['recipe_name']) && !empty($r['quantity'])) {
                $stmtRecipe->execute([$food_id, $r['recipe_name'], $r['quantity'], $r['unit_name']]);
            }
        }
    }
    
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'แก้ไข' : 'เพิ่ม' ?>เมนูอาหาร</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 700px;">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0"><?= $id ? '✏️ แก้ไขข้อมูลเมนูอาหาร' : '➕ เพิ่มข้อมูลเมนูอาหารใหม่' ?></h4>
            </div>
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <h5 class="text-primary border-bottom pb-2">ข้อมูลอาหาร</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">รูปภาพอาหาร</label>
                        <input type="file" name="image" class="form-control" accept="image/png, image/jpeg, image/webp">
                        <?php if (!empty($food['image'])): ?>
                            <div class="mt-2">
                                <p class="text-muted small mb-1">รูปปัจจุบัน:</p>
                                <img src="uploads/<?= htmlspecialchars($food['image']) ?>" alt="Food Image" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ชื่ออาหาร (ภาษาไทย) <span class="text-danger">*</span></label>
                        <input type="text" name="name_th" class="form-control" value="<?= htmlspecialchars($food['name_th']) ?>" required placeholder="เช่น ผัดไทยกุ้งสด">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="">-- เลือกหมวดหมู่ --</option>
                            <option value="อาหารคาว" <?= $food['category'] == 'อาหารคาว' ? 'selected' : '' ?>>อาหารคาว</option>
                            <option value="อาหารหวาน" <?= $food['category'] == 'อาหารหวาน' ? 'selected' : '' ?>>อาหารหวาน</option>
                            <option value="เครื่องดื่ม" <?= $food['category'] == 'เครื่องดื่ม' ? 'selected' : '' ?>>เครื่องดื่ม</option>
                        </select>
                    </div>

                    <h5 class="text-primary border-bottom pb-2 d-flex justify-content-between align-items-center">
                        สูตรและวัตถุดิบ (Recipe)
                        <button type="button" class="btn btn-sm btn-outline-success" id="add-recipe-btn">+ เพิ่มแถววัตถุดิบ</button>
                    </h5>
                    
                    <div id="recipe-container">
                        <?php if (empty($recipes)): ?>
                            <div class="row g-2 mb-2 recipe-row">
                                <div class="col-5">
                                    <input type="text" name="recipes[0][recipe_name]" class="form-control" placeholder="ชื่อวัตถุดิบ เช่น เส้นเล็ก">
                                </div>
                                <div class="col-3">
                                    <input type="number" step="0.01" name="recipes[0][quantity]" class="form-control" placeholder="ปริมาณ เช่น 150">
                                </div>
                                <div class="col-3">
                                    <input type="text" name="recipes[0][unit_name]" class="form-control" placeholder="หน่วย เช่น กรัม">
                                </div>
                                <div class="col-1 text-end">
                                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.recipe-row').remove();">X</button>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recipes as $index => $r): ?>
                                <div class="row g-2 mb-2 recipe-row">
                                    <div class="col-5">
                                        <input type="text" name="recipes[<?= $index ?>][recipe_name]" class="form-control" value="<?= htmlspecialchars($r['recipe_name']) ?>" required>
                                    </div>
                                    <div class="col-3">
                                        <input type="number" step="0.01" name="recipes[<?= $index ?>][quantity]" class="form-control" value="<?= $r['quantity'] ?>" required>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" name="recipes[<?= $index ?>][unit_name]" class="form-control" value="<?= htmlspecialchars($r['unit_name']) ?>" required>
                                    </div>
                                    <div class="col-1 text-end">
                                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.recipe-row').remove();">X</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="index.php" class="btn btn-secondary">ยกเลิก</a>
                        <button type="submit" class="btn btn-success">💾 บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let recipeIndex = <?= max(count($recipes), 1) ?>;
        document.getElementById('add-recipe-btn').addEventListener('click', function() {
            const container = document.getElementById('recipe-container');
            const newRow = document.createElement('div');
            newRow.className = 'row g-2 mb-2 recipe-row';
            newRow.innerHTML = `
                <div class="col-5">
                    <input type="text" name="recipes[${recipeIndex}][recipe_name]" class="form-control" placeholder="ชื่อวัตถุดิบ" required>
                </div>
                <div class="col-3">
                    <input type="number" step="0.01" name="recipes[${recipeIndex}][quantity]" class="form-control" placeholder="ปริมาณ" required>
                </div>
                <div class="col-3">
                    <input type="text" name="recipes[${recipeIndex}][unit_name]" class="form-control" placeholder="หน่วย" required>
                </div>
                <div class="col-1 text-end">
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.recipe-row').remove();">X</button>
                </div>
            `;
            container.appendChild(newRow);
            recipeIndex++;
        });
    </script>
</body>
</html>