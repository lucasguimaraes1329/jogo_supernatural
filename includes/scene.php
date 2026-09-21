<?php
class Scene {
    private string $id;
    private string $title;
    private string $background;
    private string $objective;
    private array $nodes;
    private array $characters;

    public function __construct(string $id,string $title,string $background,string $objective,array $nodes,array $characters=[]) {
        $this->id=$id; $this->title=$title; $this->background=$background; $this->objective=$objective;
        $this->nodes=$nodes; $this->characters=$characters;
    }
    public function id(): string { return $this->id; }
    public function title(): string { return $this->title; }
    public function background(): string { return $this->background; }
    public function objective(): string { return $this->objective; }
    public function characters(): array { return $this->characters; }
    public function node(string $id): ?array { return $this->nodes[$id] ?? null; }
    public function start(): array { return $this->nodes['start'] ?? []; }
}
