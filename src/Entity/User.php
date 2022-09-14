<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UserCountController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    collectionOperations: [
        'GET','POST',
        'get_total_users_from_dates' => [
            'method' => 'GET',
            'path' => '/users/get_total_users',
            'controller' => UserCountController::class],
],
    itemOperations:['GET'],
    normalizationContext: ['groups' => ['groupUser']]
)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['groupAdress','groupCommand','groupReview','groupUser'])]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotNull(
    message: 'L\'email doit être renseigné',
    ),
    Assert\NotBlank(
    message: 'L\'email doit être renseigné',
    normalizer: 'trim'
    ),   
    Assert\Email(
        message: 'L\'email doit être renseigné sous un format valide'
    )]
    #[Groups(['groupAdress','groupCommand','groupReview','groupUser'])]
    private $email;

    #[ORM\Column(type: 'json')]
    // tableau laissé vide car sera défini par le getUser()
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    #[  Assert\NotNull(
        message: 'Le prénom doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'Le prénom doit être renseigné',
        normalizer: 'trim'
    ),   
        Assert\Type(
        type: 'string',
        message: 'Le prénom doit être une chaine de caractères',
    )]
    #[Groups(['groupAdress','groupCommand','groupReview','groupUser'])]
    private $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[  Assert\NotNull(
        message: 'Le nom doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'Le nom doit être renseigné',
        normalizer: 'trim'
    ),   
        Assert\Type(
        type: 'string',
        message: 'Le nom doit être une chaine de caractères',
    )]
    #[Groups(['groupAdress','groupCommand','groupReview','groupUser'])]
    private $lastName;

    #[ORM\Column(type: 'datetime')]
    #[  Assert\NotNull(
        message: 'La date de création de l\'utilisateur doit être renseignée',
    ),
        Assert\NotBlank(
        message: 'La date de création de l\'utilisateur doit être renseignée',
        normalizer: 'trim'
    ),
        Assert\Type(
        type: 'datetime',
        message: 'La date de création de l\'utilisateur doit être une chaine de caractères',
    )] 
    #[Groups(['groupAdress','groupCommand','groupReview','groupUser'])]
    private $createdAt;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Command::class)]
    #[Groups(['groupUser'])]
    private $commands;

    #[ORM\ManyToMany(targetEntity: Adress::class, inversedBy: 'users')]
    #[Groups(['groupUser'])]
    private $adresses;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Review::class)]
    #[Groups(['groupUser'])]
    private $reviews;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ResetPassword::class)]
    private $resetPasswords;

    public function __construct()
    {
        $this->commands = new ArrayCollection();
        $this->adresses = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->resetPasswords = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return (string) $this->email;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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
            $command->setUser($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->commands->removeElement($command)) {
            // set the owning side to null (unless already changed)
            if ($command->getUser() === $this) {
                $command->setUser(null);
            }
        }

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
        }

        return $this;
    }

    public function removeAdress(Adress $adress): self
    {
        $this->adresses->removeElement($adress);

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, ResetPassword>
     */
    public function getResetPasswords(): Collection
    {
        return $this->resetPasswords;
    }

    public function addResetPassword(ResetPassword $resetPassword): self
    {
        if (!$this->resetPasswords->contains($resetPassword)) {
            $this->resetPasswords[] = $resetPassword;
            $resetPassword->setUser($this);
        }

        return $this;
    }

    public function removeResetPassword(ResetPassword $resetPassword): self
    {
        if ($this->resetPasswords->removeElement($resetPassword)) {
            // set the owning side to null (unless already changed)
            if ($resetPassword->getUser() === $this) {
                $resetPassword->setUser(null);
            }
        }

        return $this;
    }

    
    // public function isAdmin(): bool
    // {
    //     return in_array('ROLE_ADMIN', $this->getRoles());
    // }
}
