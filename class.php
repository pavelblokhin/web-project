<?php
class Point {
    public $x = 0;
    public $y = 0;
    private $colour;
    private $customers = 0;

    public function __construct($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }

    public function GetPosition() {
        return array($this->x, $this->y);
    }

    // manhattan distance
    public function Dist(Point $other) {
        $d_x = abs($this->x - $other->x);
        $d_y = abs($this->y - $other->y);
        return $d_x + $d_y;
    }
}


class Firm {
    public $money = 1000;
    public $cost = 50; // const cost for each unit of product

    // there are variables for game

    private $quantity = 0;
    private $price = 0;
    private $profit = 0;

    // constructor for firm
    public function __construct($money, $cost) {
        $this->money = $money;
        $this->cost = $cost;
        $this->quantity = 0;
        $this->price = 0;
        $this->profit = 0;
    }

    // functions to upgrade the firm

    public function UpGradeFunc($times) {
        $this->cost -= 3 * $times;
        $this->money -= 100 * $times;
    }

    // выбрать нужное количество товара
    public function Produce($cnt) {
        if ($cnt * $this->cost <= $this->money) {
            $this->quantity = $cnt;
            $this->money -= $cnt * $this->cost;
        } else {
            $this->quantity = floor($this->money / $this->cost);
            $this->money -= floor($this->money / $this->cost) * $this->cost;
        }
        
    }

    public function Buy($cnt) {
        if ($cnt <= $this->quantity) {
            $this->profit = $cnt * $this->price - $this->quantity * $this->cost;
            $this->quantity -= $cnt;
            $this->money += $cnt * $this->price;
        } else {
            $this->profit = $this->quantity * ($this->price - $this->cost);
            $this->money += $this->quantity * $this->price;
            $this->quantity = 0;
        }
    }

    public function SetPrice($price) {
        $this->price = $price;
    }

    public function GetQuantity() {
        return $this->quantity;
    }

    public function GetPrice() {
        return $this->price;
    }

    public function GetMoney() {
        return $this->money;
    }

    public function GetCost() {
        return $this->cost;
    }

    public function GetProfit() {
        return $this->profit;
    }
}
?>