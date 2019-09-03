<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SortieRepository")
 */
class Sortie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan("today")
     */
    private $debut;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan("today")
     */
    private $clotureInscriptions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $inscriptionsMax;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)

     */
    private $urlPhoto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Participant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organisateur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etat")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etat;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Participant", mappedBy="inscriptions")
     */
    private $inscriptions;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;


    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDebut(): ?\DateTimeInterface
    {
        return $this->debut;
    }

    public function setDebut(\DateTimeInterface $debut): self
    {
        $this->debut = $debut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getClotureInscriptions(): ?\DateTimeInterface
    {
        return $this->clotureInscriptions;
    }

    public function setClotureInscriptions(\DateTimeInterface $clotureInscriptions): self
    {
        $this->clotureInscriptions = $clotureInscriptions;

        return $this;
    }

    public function getInscriptionsMax(): ?int
    {
        return $this->inscriptionsMax;
    }

    public function setInscriptionsMax(?int $inscriptionsMax): self
    {
        $this->inscriptionsMax = $inscriptionsMax;

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

    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }

    public function setUrlPhoto(?string $urlPhoto): self
    {
        $this->urlPhoto = $urlPhoto;

        return $this;
    }

    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Participant $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection|Participant[]
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Participant $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions[] = $inscription;
            $inscription->addInscription($this);
        }

        return $this;
    }

    public function removeInscription(Participant $inscription): self
    {
        if ($this->inscriptions->contains($inscription)) {
            $this->inscriptions->removeElement($inscription);
            $inscription->removeInscription($this);
        }

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

}
