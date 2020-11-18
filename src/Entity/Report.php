<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=ReportRepository::class)
 */
class Report
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $motif;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity=tournois::class, inversedBy="reports")
     */
    private $tournois_report;

    /**
     * @ORM\ManyToOne(targetEntity=utilisateur::class, inversedBy="reports_give")
     */
    private $user_accuser;

    /**
     * @ORM\ManyToOne(targetEntity=utilisateur::class, inversedBy="reports_take")
     */
    private $user_accused;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getTournoisReport(): ?tournois
    {
        return $this->tournois_report;
    }

    public function setTournoisReport(?tournois $tournois_report): self
    {
        $this->tournois_report = $tournois_report;

        return $this;
    }

    public function getUserAccuser(): ?utilisateur
    {
        return $this->user_accuser;
    }

    public function setUserAccuser(?utilisateur $user_accuser): self
    {
        $this->user_accuser = $user_accuser;

        return $this;
    }

    public function getUserAccused(): ?utilisateur
    {
        return $this->user_accused;
    }

    public function setUserAccused(?utilisateur $user_accused): self
    {
        $this->user_accused = $user_accused;

        return $this;
    }
}
