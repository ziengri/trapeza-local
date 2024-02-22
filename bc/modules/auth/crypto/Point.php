<?php

/*
 * This class is where the elliptic curve arithmetic takes place.
 *
 * The important methods are:
 *      - add: adds two points according to ec arithmetic
 *      - double: doubles a point on the ec field mod p
 *      - mul: uses double and add to achieve multiplication
 *
 * The rest of the methods are there for supporting the ones above.
 */

class Point {

    public $curve;
    public $x;
    public $y;
    public $order;
    public static $infinity = 'infinity';

    public function __construct(CurveFp $curve, $x, $y, $order = null) {
        $this->curve = $curve;
        $this->x = $x;
        $this->y = $y;
        $this->order = $order;


        if (isset($this->curve) && ($this->curve instanceof CurveFp)) {
            if (!$this->curve->contains($this->x, $this->y)) {
                throw new ErrorException("Curve".print_r($this->curve, true)." does not contain point ( ".$x." , ".$y." )");
            }

            if ($this->order != null) {

                if (self::cmp(self::mul($order, $this), self::$infinity) != 0) {
                    throw new ErrorException("SELF * ORDER MUST EQUAL INFINITY.");
                }
            }
        }
    }

    public static function cmp($p1, $p2) {
        if (extension_loaded('gmp')) {
            if (!($p1 instanceof Point)) {
                if (($p2 instanceof Point)) return 1;
                if (!($p2 instanceof Point)) return 0;
            }

            if (!($p2 instanceof Point)) {
                if (($p1 instanceof Point)) return 1;
                if (!($p1 instanceof Point)) return 0;
            }

            if (gmp_cmp($p1->x, $p2->x) == 0 && gmp_cmp($p1->y, $p2->y) == 0 && CurveFp::cmp($p1->curve, $p2->curve)) {
                return 0;
            } else {
                return 1;
            }
        } else {
            throw new ErrorException("Please install GMP");
        }
    }

    public static function add($p1, $p2) {

        if (self::cmp($p2, self::$infinity) == 0 && ($p1 instanceof Point)) {
            return $p1;
        }
        if (self::cmp($p1, self::$infinity) == 0 && ($p2 instanceof Point)) {
            return $p2;
        }

        if (self::cmp($p1, self::$infinity) == 0 && self::cmp($p2, self::$infinity) == 0) {
            return self::$infinity;
        }

        if (extension_loaded('gmp')) {


            if (CurveFp::cmp($p1->curve, $p2->curve) == 0) {
                if (gmp_Utils::gmp_mod2(gmp_cmp($p1->x, $p2->x), $p1->curve->getPrime()) == 0) {
                    if (gmp_Utils::gmp_mod2(gmp_add($p1->y, $p2->y), $p1->curve->getPrime()) == 0) {
                        return self::$infinity;
                    } else {
                        return self::double($p1);
                    }
                }

                $p = $p1->curve->getPrime();

//                    $l = gmp_strval(gmp_mul(gmp_sub($p2->y, $p1->y), NumberTheory::inverse_mod(gmp_sub($p2->x, $p1->x), $p)));
                $l = gmp_strval(gmp_mul(gmp_sub($p2->y, $p1->y), gmp_strval(gmp_invert(gmp_sub($p2->x, $p1->x), $p))));


                $x3 = gmp_strval(gmp_Utils::gmp_mod2(gmp_sub(gmp_sub(gmp_pow($l, 2), $p1->x), $p2->x), $p));


                $y3 = gmp_strval(gmp_Utils::gmp_mod2(gmp_sub(gmp_mul($l, gmp_sub($p1->x, $x3)), $p1->y), $p));


                $p3 = new Point($p1->curve, $x3, $y3);


                return $p3;
            } else {
                throw new ErrorException("The Elliptic Curves do not match.");
            }
        } else {
            throw new ErrorException("Please install GMP");
        }
    }

    public static function mul($x2, Point $p1) {
        if (extension_loaded('gmp')) {
            $e = $x2;

            if (self::cmp($p1, self::$infinity) == 0) {
                return self::$infinity;
            }
            if ($p1->order != null) {
                $e = gmp_strval(gmp_Utils::gmp_mod2($e, $p1->order));
            }
            if (gmp_cmp($e, 0) == 0) {

                return self::$infinity;
            }

            $e = gmp_strval($e);

            if (gmp_cmp($e, 0) > 0) {

                $e3 = gmp_mul(3, $e);

                $negative_self = new Point($p1->curve, $p1->x, gmp_strval(gmp_sub(0, $p1->y)), $p1->order);
                $i = gmp_div(self::leftmost_bit($e3), 2);

                $result = $p1;

                while (gmp_cmp($i, 1) > 0) {

                    $result = self::double($result);

                    if (gmp_cmp(gmp_and($e3, $i), 0) != 0 && gmp_cmp(gmp_and($e, $i), 0) == 0) {

                        $result = self::add($result, $p1);
                    }
                    if (gmp_cmp(gmp_and($e3, $i), 0) == 0 && gmp_cmp(gmp_and($e, $i), 0) != 0) {
                        $result = self::add($result, $negative_self);
                    }

                    $i = gmp_strval(gmp_div($i, 2));
                }
                return $result;
            }
        } else {
            throw new ErrorException("Please install GMP");
        }
    }

    public static function leftmost_bit($x) {
        if (extension_loaded('gmp')) {
            if (gmp_cmp($x, 0) > 0) {
                $result = 1;
                while (gmp_cmp($result, $x) < 0 || gmp_cmp($result, $x) == 0) {
                    $result = gmp_mul(2, $result);
                }
                return gmp_strval(gmp_div($result, 2));
            }
        } else {
            throw new ErrorException("Please install GMP");
        }
    }

    public static function rmul(Point $x1, $m) {
        return self::mul($m, $x1);
    }

    public function __toString() {
        if (!($this instanceof Point) && $this == self::$infinity)
                return self::$infinity;
        return "(".$this->x.",".$this->y.")";
    }

    public static function double(Point $p1) {


        if (extension_loaded('gmp')) {

            $p = $p1->curve->getPrime();
            $a = $p1->curve->getA();

//                $inverse = NumberTheory::inverse_mod(gmp_strval(gmp_mul(2, $p1->y)), $p);
            $inverse = gmp_strval(gmp_invert(gmp_strval(gmp_mul(2, $p1->y)), $p));

            $three_x2 = gmp_mul(3, gmp_pow($p1->x, 2));

            $l = gmp_strval(gmp_Utils::gmp_mod2(gmp_mul(gmp_add($three_x2, $a), $inverse), $p));

            $x3 = gmp_strval(gmp_Utils::gmp_mod2(gmp_sub(gmp_pow($l, 2), gmp_mul(2, $p1->x)), $p));

            $y3 = gmp_strval(gmp_Utils::gmp_mod2(gmp_sub(gmp_mul($l, gmp_sub($p1->x, $x3)), $p1->y), $p));

            if (gmp_cmp(0, $y3) > 0) $y3 = gmp_strval(gmp_add($p, $y3));

            $p3 = new Point($p1->curve, $x3, $y3);

            return $p3;
        } else {
            throw new ErrorException("Please install GMP");
        }
    }

    public function getX() {
        return $this->x;
    }

    public function getY() {
        return $this->y;
    }

    public function getCurve() {
        return $this->curve;
    }

    public function getOrder() {
        return $this->order;
    }

}
?>