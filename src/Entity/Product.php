<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    collectionOperations: ['GET','POST'],
    itemOperations:['GET'],
    normalizationContext: ['groups' => ['groupProduct']]
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['groupBrand', 'groupCategory','groupCommand','groupProduct','groupProductPicture','groupReview'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[  Assert\NotNull(
        message: 'Le nom du produit doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'Le nom du produit doit être renseigné',
        normalizer: 'trim'
    ),   
        Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Le nom du produit doit contenir minimum 3 caractères',
        maxMessage: 'Le nom du produit doit contenir maximum 255 caractères',
    ),
        Assert\Type(
        type: 'string',
        message: 'Le nom du produit doit être une chaine de caractères',
    )]
    #[Groups(['groupBrand','groupCategory','groupCommand','groupProduct','groupProductPicture','groupReview'])]
    private $label;

    #[ORM\Column(type: 'text')]
    #[  Assert\NotNull(
        message: 'La description du produit doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'La description du produit doit être renseigné',
        normalizer: 'trim'
    ),   
        Assert\Type(
        type: 'string',
        message: 'La description du produit doit être une chaine de caractères',
    )]
    #[Groups(['groupBrand','groupCategory','groupCommand','groupProduct','groupProductPicture','groupReview'])]
    private $description;

    #[ORM\Column(type: 'integer')]
    #[  Assert\NotNull(
        message: 'Le prix (en cents) du produit doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'Le prix (en cents) du produit doit être renseigné',
        normalizer: 'trim'
    ),
        Assert\Positive(
        message: 'Le prix du produit doit être un nombre positif',
    ),
        Assert\Type(
        type: 'integer',
        message: 'Le prix du produit doit être un nombre',
    )]
    #[Groups(['groupBrand','groupCategory','groupCommand','groupProduct','groupProductPicture','groupReview'])]
    private $price;

    #[ORM\Column(type: 'integer')]
    #[  Assert\NotNull(
        message: 'Le stock du produit doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'Le stock du produit doit être renseigné',
        normalizer: 'trim'
    ),
        Assert\PositiveOrZero(
        message: 'Le stock du produit doit être un nombre positif (ou 0)',
    ),
        Assert\Type(
        type: 'integer',
        message: 'Le stock du produit doit être un nombre',
    )]
    #[Groups(['groupBrand','groupCategory','groupCommand','groupProduct','groupProductPicture','groupReview'])]
    private $stock;

    #[ORM\Column(type: 'boolean')]
    #[  Assert\NotNull(
        message: 'Le produit doit être actif (TRUE) ou inactif (FALSE)',
    ),
        Assert\NotBlank(
        message: 'Le produit doit être actif (TRUE) ou inactif (FALSE)',
        normalizer: 'trim'
    ),
        Assert\Type(
        type: 'bool',
        message: 'Le stock du produit doit être un booléen',
    )]
    #[Groups(['groupBrand','groupCategory','groupCommand','groupProduct','groupProductPicture','groupReview'])]
    private $isActif;

    #[ORM\ManyToMany(targetEntity: Command::class, mappedBy: 'products')]
    #[Groups(['groupProduct'])]
    private $commands;

    #[ORM\ManyToOne(targetEntity: Brand::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['groupProduct'])]
    private $brand;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Review::class)]
    #[Groups(['groupProduct'])]
    private $reviews;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products')]
    #[Groups(['groupProduct'])]
    private $categories;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductPicture::class, cascade:['persist'])]
    #[Groups(['groupProduct'])]
    private $productPictures;

    public function __construct()
    {
        $this->commands = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->productPictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getIsActif(): ?bool
    {
        return $this->isActif;
    }

    public function setIsActif(bool $isActif): self
    {
        $this->isActif = $isActif;

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
            $command->addProduct($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->commands->removeElement($command)) {
            $command->removeProduct($this);
        }

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

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
            $review->setProduct($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getProduct() === $this) {
                $review->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, ProductPicture>
     */
    public function getProductPictures(): Collection
    {
        return $this->productPictures;
    }

    public function addProductPicture(ProductPicture $productPicture): self
    {
        if (!$this->productPictures->contains($productPicture)) {
            $this->productPictures[] = $productPicture;
            $productPicture->setProduct($this);
        }

        return $this;
    }

    public function removeProductPicture(ProductPicture $productPicture): self
    {
        if ($this->productPictures->removeElement($productPicture)) {
            // set the owning side to null (unless already changed)
            if ($productPicture->getProduct() === $this) {
                $productPicture->setProduct(null);
            }
        }

        return $this;
    }
}
