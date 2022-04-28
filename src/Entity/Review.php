<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\VisitCountController;
use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ApiResource(
    collectionOperations: ['GET','POST'],
    itemOperations:['GET'],
    normalizationContext: ['groups' => ['groupReview']]
)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['groupBrand','groupReview','groupUser'])]
    private $id;

    #[ORM\Column(type: 'integer')]
    #[  Assert\NotNull(
        message: 'La note de l\'avis doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'La note de l\'avis doit être renseigné',
        normalizer: 'trim'
    ),
        Assert\Positive(
        message: 'La note de l\'avis doit être un nombre positif',
    ),
        Assert\LessThan(
        value: 11,
    ),
        Assert\Type(
        type: 'integer',
        message: 'La note de l\'avis doit être un nombre',
    )]
    #[Groups(['groupBrand','groupReview','groupUser'])]
    private $note;

    #[ORM\Column(type: 'datetime')]
    #[  Assert\NotNull(
        message: 'La date de l\'avis doit être renseignée',
    ),
        Assert\NotBlank(
        message: 'La date de l\'avis doit être renseignée',
        normalizer: 'trim'
    ),
        Assert\Type( 
        type: 'datetime',
        message: 'La date de l\'avis doit être une chaine de caractères',
    )]
    #[Groups(['groupBrand','groupReview','groupUser'])]
    private $createdAt;

    #[ORM\Column(type: 'text')]
    #[  Assert\NotNull(
        message: 'Le contenu de l\'avis doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'Le contenu de l\'avis doit être renseigné',
        normalizer: 'trim'
    ),   
        Assert\Type(
        type: 'string',
        message: 'Le ncontenu de l\'avis doit être une chaine de caractères',
    )]
    #[Groups(['groupBrand','groupReview','groupUser'])]
    private $content;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['groupReview'])]
    private $product;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['groupReview'])]
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
