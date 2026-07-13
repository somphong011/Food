<?php
require_once 'data.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายการเมนูอาหาร สีสันสดใส แยกหมวดหมู่</title>
    <style>
        body { font-family: sans-serif; background-color: #fefaf4; color: #2d3748; padding: 40px; margin: 0; }
        .wrapper { max-width: 1200px; margin: 0 auto; }
        
        h1 { text-align: center; color: #ff5a5f; font-size: 2.4rem; margin-bottom: 5px; font-weight: 800; }
        .main-subtitle { text-align: center; color: #718096; margin-bottom: 40px; font-size: 1.1rem; }
        
        .section-title { font-size: 1.8rem; color: #ff9f1c; font-weight: bold; margin: 50px 0 25px 0; text-align: center; border-bottom: 2px dashed #ff9f1c33; padding-bottom: 10px; }
        
        /* 6 เมนูหลัก (3 คอลัมน์) */
        .premium-grid { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 25px; 
            margin-bottom: 40px;
        }
        .card-premium { 
            background-color: #ffffff; 
            border-radius: 16px; 
            padding: 25px; 
            border-top: 6px solid #ff5a5f; 
            box-shadow: 0 8px 20px rgba(255, 90, 95, 0.08); 
            transition: transform 0.2s;
        }
        .card-premium:hover { transform: translateY(-5px); }
        .card-premium .num { color: #ff5a5f; font-weight: bold; margin-bottom: 8px; font-size: 0.9rem; }
        .card-premium .title { font-size: 1.3rem; color: #1a202c; font-weight: bold; display: block; margin-bottom: 5px; }
        .card-premium .price-tag { color: #e63946; font-weight: bold; font-size: 1.1rem; display: block; margin-bottom: 8px; }
        .card-premium .badge { background-color: #fff0f0; color: #ff5a5f; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; display: inline-block; font-weight: bold; }
        .ing { margin-top: 15px; padding-top: 15px; border-top: 1px dashed #fed7d7; line-height: 1.7; color: #4a5568; font-size: 0.95rem; }

        /* --- [ ปรับปรุง: ดึงแนวตั้งให้สูงเท่ากันด้วย stretch ] --- */
        .infographic-container {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 20px;
            align-items: stretch; /* บังคับให้ทุกกล่องยืดสูงเท่ากับกล่องที่สูงที่สุดอัตโนมัติ */
            background-color: #fff5eb;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .menu-column-box {
            background-color: #ffffff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
            border: 1px solid #ffe6cc;
            display: flex;
            flex-direction: column; /* จัดโครงสร้างภายในเป็นแนวตั้งเพื่อควบคุมของด้านใน */
        }

        /* หัวตาราง */
        .column-header {
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 25px;
            text-align: center;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .header-rice { background-color: #b81d24; }
        .header-noodle { background-color: #d97706; }
        .header-soup { background-color: #dc2626; }

        /* รายการอาหาร */
        .compact-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .compact-list.split-two {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 20px;
        }

        .compact-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 10px;
            border-bottom: 1px dashed #f0f0f0;
            font-size: 0.9rem;
            transition: background-color 0.15s;
        }
        .compact-item:hover { background-color: #fffaf0; }
        .compact-item:last-child { border-bottom: none; }

        .menu-index {
            color: #718096;
            font-weight: bold;
            margin-right: 8px;
            font-size: 0.85rem;
            display: inline-block;
            width: 20px;
        }
        .menu-name {
            color: #2d3748;
            font-weight: 500;
        }
        .menu-price {
            color: #e53e3e;
            font-size: 0.8rem;
            font-weight: bold;
            background-color: #fff5f5;
            padding: 2px 6px;
            border-radius: 6px;
        }

        @media (max-width: 992px) {
            .infographic-container { grid-template-columns: 1fr; }
            .compact-list.split-two { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    
    <h1>🎨 คลังสูตรอาหารและเมนูแยกหมวดหมู่</h1>
    <p class="main-subtitle">(6 เมนูหลักสูตรพิเศษจัดเต็ม แถวละ 3 จาน / 100 เมนูแนะนำจัดดีไซน์ตามรูปแบบจริง)</p>

    <div class="premium-grid">
        <?php foreach ($premiumMenuList as $index => $item) { ?>
            <div class="card-premium">
                <div class="num">⭐ PREMIUM DISH NO. <?php echo ($index + 1); ?></div>
                <span class="title"><?php echo $item['food']->name; ?></span>
                <span class="price-tag">💰 ราคา: <?php echo $item['food']->price; ?> บาท</span>
                <span class="badge">ประเภท: <?php echo $item['food']->type; ?></span>
                <div class="ing">
                    <strong style="color: #ff5a5f;">📍 วัตถุดิบเด็ด:</strong><br>
                    <?php echo $item['recipe']->ingredients; ?>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="section-title">✨ เมนูแนะนำเพิ่มเติมแยกตามประเภท ✨</div>

    <div class="infographic-container">
        
        <?php if (isset($categorizedSuggested['ข้าวจานเดียว'])): ?>
        <div class="menu-column-box">
            <div class="column-header header-rice">🍳 ข้าวจานเดียว</div>
            <ul class="compact-list split-two">
                <?php 
                $riceCount = 1;
                foreach ($categorizedSuggested['ข้าวจานเดียว'] as $item) { ?>
                    <li class="compact-item">
                        <div>
                            <span class="menu-index"><?php echo $riceCount++; ?></span>
                            <span class="menu-name"><?php echo $item->name; ?></span>
                        </div>
                        <span class="menu-price"><?php echo $item->price; ?>.-</span>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (isset($categorizedSuggested['เมนูเส้น'])): ?>
        <div class="menu-column-box">
            <div class="column-header header-noodle">🍜 เมนูเส้น</div>
            <ul class="compact-list">
                <?php 
                $noodleCount = 51;
                foreach ($categorizedSuggested['เมนูเส้น'] as $item) { ?>
                    <li class="compact-item">
                        <div>
                            <span class="menu-index"><?php echo $noodleCount++; ?></span>
                            <span class="menu-name"><?php echo $item->name; ?></span>
                        </div>
                        <span class="menu-price"><?php echo $item->price; ?>.-</span>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (isset($categorizedSuggested['ต้ม แกง และอื่นๆ'])): ?>
        <div class="menu-column-box">
            <div class="column-header header-soup">🥘 ต้ม แกง และอื่นๆ</div>
            <ul class="compact-list">
                <?php 
                $soupCount = 77;
                foreach ($categorizedSuggested['ต้ม แกง และอื่นๆ'] as $item) { ?>
                    <li class="compact-item">
                        <div>
                            <span class="menu-index"><?php echo $soupCount++; ?></span>
                            <span class="menu-name"><?php echo $item->name; ?></span>
                        </div>
                        <span class="menu-price"><?php echo $item->price; ?>.-</span>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <?php endif; ?>

    </div>

</div>
</body>
</html>