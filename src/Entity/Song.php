<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SongRepository;
use App\Traits\StatisticsPropertiesTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: SongRepository::class)]
class Song
{
    use StatisticsPropertiesTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['song', 'pool'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['song', 'pool'])]
    #[Assert\Length(
        min: 3,
        max: 30,
        minMessage: 'Your first name must be at least {{ limit }} characters long',
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['song', 'pool'])]
    #[Assert\Length(
        min: 3,
        max: 30,
        minMessage: 'Your first name must be at least {{ limit }} characters long',
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    private ?string $artiste = null;

    /**
     * @var Collection<int, Pool>
     */
    #[ORM\ManyToMany(targetEntity: Pool::class, inversedBy: 'songs')]
    #[Groups(['song'])]
    private Collection $pools;

    public function __construct()
    {
        $this->pools = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getArtiste(): ?string
    {
        return $this->artiste;
    }

    public function setArtiste(string $artiste): static
    {
        $this->artiste = $artiste;

        return $this;
    }

    /**
     * @return Collection<int, Pool>
     */
    public function getPools(): Collection
    {
        return $this->pools;
    }

    public function addPool(Pool $pool): static
    {
        if (!$this->pools->contains($pool)) {
            $this->pools->add($pool);
        }

        return $this;
    }

    public function removePool(Pool $pool): static
    {
        $this->pools->removeElement($pool);

        return $this;
    }

    public function removePools(): static
    {
        foreach ($this->getPools() as $pool) {
            $this->removePool($pool);
        }

        return $this;
    }
}
