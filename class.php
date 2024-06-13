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

    private $guantity = 0;
    private $price = 0;
    private $total_costs = 0;
    private $position;

    // constructor for firm
    public function __construct($colour, $money, $cost, $employees) {
        $this->money = $money;
        $this->cost = $cost;
        $this->guantity = 0;
        $this->price = 0;
    }

    // functions to upgrade the firm

    public function UpGradeFunc($price) {
        $this->cost -= 1; // доработать коэф!
        $this->money -= $price;
        $this->total_costs += $price;
    }


    // functions for the game

    public function CountCosts() {
        $this->total_costs += $this->guantity * $this->cost;
    }

    public function Produce($cnt) {
        $this->guantity = $cnt;
    }
}
?>