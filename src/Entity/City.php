<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CityRepository::class)]
//Ce qui appareit dans l'API
#[ApiResource(
    collectionOperations: ['GET','POST'],
    itemOperations:['GET'],
    normalizationContext: ['groups' => ['groupCity']]
)]
//Filtrer avec le nom de la ville (partial = idem LIKE)
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['groupAdress','groupCity'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[  Assert\NotNull(
        message: 'Le nom de ville doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'Le nom de ville doit être renseigné',
        normalizer: 'trim'
    ),   
        Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Le nom de ville doit contenir minimum 3 caractères',
        maxMessage: 'Le nom de ville doit contenir maximum 255 caractères',
    ),
        Assert\Type(
        type: 'string',
        message: 'Le nom de ville doit être une chaine de caractères',
    )]
    #[Groups(['groupAdress','groupCity'])]
    private $name;

    #[ORM\Column(type: 'integer')]
    #[  Assert\NotNull(
        message: 'Le code postal doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'Le code postal doit être renseigné',
        normalizer: 'trim'
    ),
        Assert\Positive(
        message: 'Le code postal doit être un nombre positif',
    ),
        Assert\Type(
        type: 'integer',
        message: 'Le code postal doit être un nombre',
    )]
    #[Groups(['groupAdress','groupCity'])]
    private $cp;

    #[ORM\OneToMany(mappedBy: 'city', targetEntity: Adress::class)]
    #[Groups(['groupCity'])]
    private $adresses;

    public function __construct()
    {
        $this->adresses = new ArrayCollection();
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

    public function getCp(): ?int
    {
        return $this->cp;
    }

    public function setCp(int $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * @return Collection<int, Adress>
     */
    public function getAdresses(): Collection
    {
        return $this->adresses;
    }

    public function addAdress(Adress $adress): self
    {
        if (!$this->adresses->contains($adress)) {
            $this->adresses[] = $adress;
            $adress->setCity($this);
        }

        return $this;
    }

    public function removeAdress(Adress $adress): self
    {
        if ($this->adresses->removeElement($adress)) {
            // set the owning side to null (unless already changed)
            if ($adress->getCity() === $this) {
                $adress->setCity(null);
            }
        }

        return $this;
    }
}
