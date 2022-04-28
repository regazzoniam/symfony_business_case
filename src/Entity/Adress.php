<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AdressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdressRepository::class)]
#[ApiResource(
    collectionOperations: ['GET','POST'],
    itemOperations:['GET'],
    normalizationContext: ['groups' => ['groupAdress']]
)]
class Adress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['groupAdress','groupCity','groupCommand','groupUser'])]
    private $id;

    #[ORM\Column(type: 'integer')]
    #[  Assert\NotNull(
        message: 'Le numéro de rue doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'Le numéro de rue doit être renseigné',
        normalizer: 'trim'
    ),
        Assert\Positive(
        message: 'Le numéro de rue doit être doit être un nombre positif',
    ),
        Assert\Type(
        type: 'integer',
        message: 'Le numéro de rue doit être un nombre.',
    )]
    #[Groups(['groupAdress','groupCity','groupCommand','groupUser'])]
    private $streetNumber;

    #[ORM\Column(type: 'string', length: 255)]
    #[  Assert\NotNull(
        message: 'Le nom de rue doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'Le nom de rue doit être renseigné',
        normalizer: 'trim'
    ),   
        Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'Le nom de rue doit contenir minimum 5 caractères',
        maxMessage: 'Le nom de rue doit contenir maximum 255 caractères',
    ),
        Assert\Type(
        type: 'string',
        message: 'Le nom de rue doit être une chaine de caractères.',
    )]
    #[Groups(['groupAdress','groupCity','groupCommand','groupUser'])]
    private $streetName;

    #[ORM\OneToMany(mappedBy: 'adress', targetEntity: Command::class)]
    #[Groups(['groupAdress'])]
    private $commands;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'adresses')]
    #[Groups(['groupAdress'])]
    private $users;

    #[ORM\ManyToOne(targetEntity: City::class, inversedBy: 'adresses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['groupAdress'])]
    private $city;

    public function __construct()
    {
        $this->commands = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStreetNumber(): ?int
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(int $streetNumber): self
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getStreetName(): ?string
    {
        return $this->streetName;
    }

    public function setStreetName(string $streetName): self
    {
        $this->streetName = $streetName;

        return $this;
    }

    /**
     * @return Collection<int, Command>
     */
    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function addCommand(Command $command): self
    {
        if (!$this->commands->contains($command)) {
            $this->commands[] = $command;
            $command->setAdress($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->commands->removeElement($command)) {
            // set the owning side to null (unless already changed)
            if ($command->getAdress() === $this) {
                $command->setAdress(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addAdress($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeAdress($this);
        }

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }
}
