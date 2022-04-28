<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BrandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BrandRepository::class)]
#[ApiResource(
    collectionOperations: ['GET','POST'],
    itemOperations:['GET'],
    normalizationContext: ['groups' => ['groupBrand']]
)]
class Brand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['groupBrand','groupProduct'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[  Assert\NotNull(
        message: 'La marque doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'La marque doit être renseigné',
        normalizer: 'trim'
    ),   
        Assert\Length(
        min: 1,
        max: 255,
        minMessage: 'La marque doit contenir minimum 1 caractère',
        maxMessage: 'La marque doit contenir maximum 255 caractères',
    ),
        Assert\Type(
        type: 'string',
        message: 'La marque doit être une chaine de caractères.',
    )]
    #[Groups(['groupBrand','groupProduct'])]
    private $label;

    #[ORM\Column(type: 'string', length: 255)]
    #[  Assert\NotNull(
        message: 'L\'emplacement de l\'image doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'L\'emplacement de l\'image doit être renseigné',
        normalizer: 'trim'
    ),   
        Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'L\'emplacement de l\'image doit contenir minimum 5 caractères',
        maxMessage: 'L\'emplacement de l\'image doit contenir maximum 255 caractères',
    ),
        Assert\Type(
        type: 'string',
        message: 'L\'emplacement de l\'image doit être une chaine de caractères.',
    )]
    #[Groups(['groupBrand','groupProduct'])]
    private $imagePath;

    #[ORM\OneToMany(mappedBy: 'brand', targetEntity: Product::class)]
    #[Groups(['groupBrand'])]
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): self
    {
        $this->imagePath = $imagePath;

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
            $product->setBrand($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getBrand() === $this) {
                $product->setBrand(null);
            }
        }

        return $this;
    }
}
