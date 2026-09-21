<?php
class Challenge {
    private string $id; private string $title; private string $description; private int $difficulty; private int $bonus; private Dice $dice;
    public function __construct(string $id,string $title,string $description,int $difficulty,int $bonus=0,?Dice $dice=null){$this->id=$id;$this->title=$title;$this->description=$description;$this->difficulty=$difficulty;$this->bonus=$bonus;$this->dice=$dice??new Dice(6);}
    public function id():string{return $this->id;} public function title():string{return $this->title;} public function description():string{return $this->description;} public function difficulty():int{return $this->difficulty;}
    public function attempt(int $characterSkill=0):array{$roll=$this->dice->roll();$total=$roll+$characterSkill+$this->bonus;return ['id'=>$this->id,'title'=>$this->title,'description'=>$this->description,'roll'=>$roll,'bonus'=>$characterSkill+$this->bonus,'total'=>$total,'difficulty'=>$this->difficulty,'success'=>$total>=$this->difficulty];}
}
