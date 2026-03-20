<?php

class Product {

        private int $id_product;
        private string $name;
        private string $description;
        private float $price;
        private int $stock_quantity;
        private string $category;
    
        public function __construct(int $id_product, string $name, string $description, float $price, int $stock_quantity, string $category) {
            $this->id_product = $id_product;
            $this->name = $name;
            $this->description = $description;
            $this->price = $price;
            $this->stock_quantity = $stock_quantity;
            $this->category = $category;
        }

        public function __get($property) {
            return $this->$property;
        }

}

?>