<?php
class Dice {
    public function __construct(private int $sides=6) {
        if ($sides < 2) throw new InvalidArgumentException('Um dado precisa ter pelo menos 2 lados.');
    }
    public function roll(): int { return random_int(1, $this->sides); }
    public function sides(): int { return $this->sides; }
}
