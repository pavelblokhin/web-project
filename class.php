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

class Field {
    private $width; // Ширина поля
    private $height; // Высота пол
    private $cells; // массив клеток

    public function __construct($width, $height) {
        $this->width = $width;
        $this->height = $height;
        for ($i = 0; $i < $height; $i++) {
            for ($j = 0; $j < $width; $j++) {
                $this->cells[$i][$j] = new Point($i, $j);
            }
        }
    }

    public function GetPos($i, $j) {
        return $this->cells[$i][$j];
    }
}

class Firm {
    public $colour; // choose the team (read or blue)
    public $money = 1000;
    public $cost = 50; // const cost for each unit of product
    public $employees = 1; // to up max units of product

    // there are variables for game

    private $inovation = 0; // to reduce cost of transport for customers
    private $inov_price = 50;
    private $guantity = 0;
    private $price = 0;
    private $wage = 0;
    private $total_costs = 0;
    private $position;

    // constructor for firm
    public function __construct($colour, $money, $cost, $employees) {
        $this->colour = $colour;
        $this->money = $money;
        $this->cost = $cost;
        $this->employees = $employees;
        $this->inovation = 0;
        $this->inov_price = 50;
        $this->guantity = 0;
        $this->price = 0;
        $this->wage = 0;
        $this->position = new Point(0, 0);
    }

    // functions to upgrade the firm

    public function UpGradeFunc($price) {
        $this->cost -= 1; // доработать коэф!
        $this->money -= $price;
        $this->total_costs += $price;
    }

    public function ToEmploy($cnt) {
        $this->employees += $cnt;
    }

    public function Inovation() {
        $this->inovation += 1; // доработать цены!
        $this->money - $this->inov_price;
        $this->total_costs += $this->inov_price;
    }

    // functions for the game

    public function CountCosts() {
        $this->total_costs += $this->guantity * $this->cost + $this->employees * $this->wage;
    }

    public function Produce($cnt) {
        $this->guantity = $cnt;
    }
}
?>