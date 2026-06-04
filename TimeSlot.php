<?php

class TimeSlot
{
    private int    $id_timeslot;
    private string $location;
    private string $date;
    private string $time;
    private int $duration;
    private float $price;
    private int    $level;

    private int $player_count = 0;

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    private function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    // GETTERS
    public function getId(): int
    {
        return $this->id_timeslot;
    }
    public function getLocation(): string
    {
        return $this->location;
    }
    public function getDate(): string
    {
        return $this->date;
    }
    public function getTime(): string
    {
        return $this->time;
    }
    public function getLevel(): int
    {
        return $this->level;
    }
    public function getPlayerCount(): int
    {
        return $this->player_count;
    }

    public function getFormattedDate(): string
    {
        $months = [
            1 => 'janvier',
            2 => 'février',
            3 => 'mars',
            4 => 'avril',
            5 => 'mai',
            6 => 'juin',
            7 => 'juillet',
            8 => 'août',
            9 => 'septembre',
            10 => 'octobre',
            11 => 'novembre',
            12 => 'décembre'
        ];
        $ts = strtotime($this->date);
        return date('d', $ts) . ' ' . $months[(int)date('n', $ts)] . ' ' . date('Y', $ts);
    }

    public function getFormattedTime(): string
    {
        return substr($this->time, 0, 5); // "18:00"
    }

    // SETTERS
    public function setId_timeslot(int $id): self
    {
        $this->id_timeslot = $id;
        return $this;
    }
    public function setLocation(string $v): self
    {
        $this->location = $v;
        return $this;
    }
    public function setDate(string $v): self
    {
        $this->date = $v;
        return $this;
    }
    public function setTime(string $v): self
    {
        $this->time = $v;
        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration ?? 0;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price ?? 0.0;
        return $this;
    }

    public function setLevel(int $v): self
    {
        $this->level = $v;
        return $this;
    }
    public function setPlayer_count(int $v): self
    {
        $this->player_count = $v;
        return $this;
    }
    public function getFormattedDuration(): string
    {
        if ($this->duration < 60) return $this->duration . 'min';
        $h   = intdiv($this->duration, 60);
        $min = $this->duration % 60;
        return $min > 0 ? "{$h}h{$min}" : "{$h}h";
    }
}
