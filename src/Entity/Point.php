<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PointRepository")
 */
class Point
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $x_position;

    /**
     * @ORM\Column(type="float")
     */
    private $y_position;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Chart", inversedBy="points")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chart;

    /**
     * @ORM\Column(type="boolean")
     */
    private $predicted;

	public function __construct() {
		$this->setPredicted(false);
	}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getXPosition(): ?float
    {
        return $this->x_position;
    }

    public function setXPosition(?float $x_position): self
    {
        $this->x_position = $x_position;

        return $this;
    }

    public function getYPosition(): ?float
    {
        return $this->y_position;
    }

    public function setYPosition(float $y_position): self
    {
        $this->y_position = $y_position;

        return $this;
    }

    public function getChart(): ?Chart
    {
        return $this->chart;
    }

    public function setChart(?Chart $chart): self
    {
        $this->chart = $chart;

        return $this;
    }

    public function getPredicted(): ?bool
    {
        return $this->predicted;
    }

    public function setPredicted(bool $predicted): self
    {
        $this->predicted = $predicted;

        return $this;
    }
}
