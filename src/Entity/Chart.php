<?php

namespace App\Entity;

use App\Chart\PointsCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChartRepository")
 */
class Chart
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Point", mappedBy="chart", orphanRemoval=true)
     */
    private $points;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $x_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $y_name;

    public function __construct()
    {
        $this->points = new PointsCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Point[]
     */
    public function getPoints(): Collection
    {
        return $this->points;
    }

    public function addPoint(Point $point): self
    {
        if (!$this->points->contains($point)) {
            $this->points[] = $point;
            $point->setChart($this);
        }

        return $this;
    }

    public function removePoint(Point $point): self
    {
        if ($this->points->contains($point)) {
            $this->points->removeElement($point);
            // set the owning side to null (unless already changed)
            if ($point->getChart() === $this) {
                $point->setChart(null);
            }
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getXName(): ?string
    {
        return $this->x_name;
    }

    public function setXName(string $x_name): self
    {
        $this->x_name = $x_name;

        return $this;
    }

    public function getYName(): ?string
    {
        return $this->y_name;
    }

    public function setYName(string $y_name): self
    {
        $this->y_name = $y_name;

        return $this;
    }
}
