<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 */
class Utilisateur
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
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $role = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = true;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $score;

    /**
     * @ORM\ManyToMany(targetEntity=Tournois::class, mappedBy="user_tournois")
     */
    private $tournois;

    /**
     * @ORM\OneToMany(targetEntity=Report::class, mappedBy="user_accuser")
     */
    private $reports_give;

    /**
     * @ORM\OneToMany(targetEntity=Report::class, mappedBy="user_accused")
     */
    private $reports_take;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $riotAccountId;

    public function __construct()
    {
        $this->tournois = new ArrayCollection();
        $this->reports_give = new ArrayCollection();
        $this->reports_take = new ArrayCollection();
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getRole(): ?bool
    {
        return $this->role;
    }

    public function setRole(bool $role): self
    {
        $this->role = $role;

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

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): self
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @return Collection|Tournois[]
     */
    public function getTournois(): Collection
    {
        return $this->tournois;
    }

    public function addTournoi(Tournois $tournoi): self
    {
        if (!$this->tournois->contains($tournoi)) {
            $this->tournois[] = $tournoi;
            $tournoi->addUserTournoi($this);
        }

        return $this;
    }

    public function removeTournoi(Tournois $tournoi): self
    {
        if ($this->tournois->removeElement($tournoi)) {
            $tournoi->removeUserTournoi($this);
        }

        return $this;
    }

    /**
     * @return Collection|Report[]
     */
    public function getReportsGive(): Collection
    {
        return $this->reports_give;
    }

    public function addReportsGive(Report $reportsGive): self
    {
        if (!$this->reports_give->contains($reportsGive)) {
            $this->reports_give[] = $reportsGive;
            $reportsGive->setUserAccuser($this);
        }

        return $this;
    }

    public function removeReportsGive(Report $reportsGive): self
    {
        if ($this->reports_give->removeElement($reportsGive)) {
            // set the owning side to null (unless already changed)
            if ($reportsGive->getUserAccuser() === $this) {
                $reportsGive->setUserAccuser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Report[]
     */
    public function getReportsTake(): Collection
    {
        return $this->reports_take;
    }

    public function addReportsTake(Report $reportsTake): self
    {
        if (!$this->reports_take->contains($reportsTake)) {
            $this->reports_take[] = $reportsTake;
            $reportsTake->setUserAccused($this);
        }

        return $this;
    }

    public function removeReportsTake(Report $reportsTake): self
    {
        if ($this->reports_take->removeElement($reportsTake)) {
            // set the owning side to null (unless already changed)
            if ($reportsTake->getUserAccused() === $this) {
                $reportsTake->setUserAccused(null);
            }
        }

        return $this;
    }

    public function getRiotAccountId(): ?string
    {
        return $this->riotAccountId;
    }

    public function setRiotAccountId(string $riotAccountId): self
    {
        $this->riotAccountId = $riotAccountId;

        return $this;
    }
}
