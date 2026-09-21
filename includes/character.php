<?php
class Character {
    private string $id;
    private string $name;
    private string $role;
    private int $skill;
    private string $sprite;
    private string $portrait;

    public function __construct(string $id, string $name, string $role, int $skill, string $sprite, string $portrait) {
        $this->id=$id; $this->name=$name; $this->role=$role; $this->skill=$skill;
        $this->sprite=$sprite; $this->portrait=$portrait;
    }
    public function id(): string { return $this->id; }
    public function name(): string { return $this->name; }
    public function role(): string { return $this->role; }
    public function skill(): int { return $this->skill; }
    public function sprite(): string { return $this->sprite; }
    public function portrait(): string { return $this->portrait; }
    public function toArray(): array { return ['id'=>$this->id,'name'=>$this->name,'role'=>$this->role,'skill'=>$this->skill,'sprite'=>$this->sprite,'portrait'=>$this->portrait]; }
}
