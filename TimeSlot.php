<?php

class TimeSlot
{
    private int $id_timeslot;
    private string $location;
    private string $date;
    private string $time;
    private int $duration;
    private float $price;

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    private function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $method = "set" . ucfirst($key);
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
    public function getDuration(): int
    {
        return $this->duration;
    }
    public function getPrice(): float
    {
        return $this->price;
    }

    // SETTERS
    public function setId_timeslot(int $id): self
    {
        $this->id_timeslot = $id;
        return $this;
    }
    public function setLocation(string $location): self
    {
        $this->location = $location;
        return $this;
    }
    public function setDate(string $date): self
    {
        $this->date = $date;
        return $this;
    }
    public function setTime(string $time): self
    {
        $this->time = $time;
        return $this;
    }
    public function setDuration(int $duration): self
    {
        $this->duration = $duration;
        return $this;
    }
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }
}
