<?php
// คลาสอาหารหลัก
class Food {
    public $name; 
    public $type; 
    public $price;

    public function __construct($name, $type,$price) {
        $this->name =$name;
        $this->type =$type; 
        $this->price =$price;
    }
}

// คลาสสูตรอาหาร
class Recipe {
    public $ingredients; 

    public function __construct($ingredients) {
        $this->ingredients =$ingredients;
    }
}

// คลาสสำหรับเมนูแนะนำ (ตัดตัวแปร price ออกอย่างสมบูรณ์)
class SuggestedFood {
    public $name;
    public $type;

    public function __construct($name,$type) {
        $this->name =$name;
        $this->type =$type;
    }
}
?>