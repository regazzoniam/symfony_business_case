<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    collectionOperations: ['GET','POST'],
    itemOperations:['GET'],
    normalizationContext: ['groups' => ['groupCategory']]
)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['groupCategory','groupProduct'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[  Assert\NotNull(
        message: 'Le nom de catégorie doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'Le nom de catégorie doit être renseigné',
        normalizer: 'trim'
    ),   
        Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Le nom de catégorie doit contenir minimum 3 caractères',
        maxMessage: 'Le nom de catégorie doit contenir maximum 255 caractères',
    )]
    #[Groups(['groupCategory','groupProduct'])]
    private $label;

    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'categories')]
    #[Groups(['groupCategory'])]
    private $products;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'categories')]
    #[Groups(['groupCategory'])]
    private $categoryParent;

    #[ORM\OneToMany(mappedBy: 'categoryParent', targetEntity: self::class)]
    #[Groups(['groupCategory'])]
    private $categories;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->categories = new ArrayCollection();
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

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            $product->removeCategory($this);
        }

        return $this;
    }

    public function getCategoryParent(): ?self
    {
        return $this->categoryParent;
    }

    public function setCategoryParent(?self $categoryParent): self
    {
        $this->categoryParent = $categoryParent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(self $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setCategoryParent($this);
        }

        return $this;
    }

    public function removeCategory(self $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getCategoryParent() === $this) {
                $category->setCategoryParent(null);
            }
        }

        return $this;
    }
}
