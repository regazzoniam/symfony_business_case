<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductPictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductPictureRepository::class)]
#[ApiResource(
    collectionOperations: ['GET','POST'],
    itemOperations:['GET'],
    normalizationContext: ['groups' => ['groupProductPicture']]
)]
class ProductPicture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['groupProduct','groupProductPicture'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[  Assert\NotNull(
        message: 'L\'emplacement de l\'image du produit doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'L\'emplacement de l\'image du produit doit être renseigné',
        normalizer: 'trim'
    ),   
        Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'L\'emplacement de l\'image du produit doit contenir minimum 5 caractères',
        maxMessage: 'L\'emplacement de l\'image du produit doit contenir maximum 255 caractères',
    ),
        Assert\Type(
        type: 'string',
        message: 'L\'emplacement de l\'image du produit doit être une chaine de caractères.',
    )]
    #[Groups(['groupProduct','groupProductPicture'])]
    private $path;

    #[ORM\Column(type: 'string', length: 255)]
    #[  Assert\NotNull(
        message: 'Le libellé de l\'image du produit doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'Le libellé de l\'image du produit doit être renseigné',
        normalizer: 'trim'
    ),   
        Assert\Length(
        min: 1,
        max: 255,
        minMessage: 'Le libellé de l\'image du produit doit contenir minimum 1 caractère',
        maxMessage: 'Le libellé de l\'image du produit doit contenir maximum 255 caractères',
    ),
        Assert\Type(
        type: 'string',
        message: 'Le libellé de l\'image du produit doit être une chaine de caractères.',
    )]
    #[Groups(['groupProduct','groupProductPicture'])]
    private $libele;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'productPictures')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['groupProductPicture'])]
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getLibele(): ?string
    {
        return $this->libele;
    }

    public function setLibele(string $libele): self
    {
        $this->libele = $libele;

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
}
