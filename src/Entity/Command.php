<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\AverageBasketAmountController;
use App\Controller\BasketCountController;
use App\Controller\CommandCountController;
use App\Controller\PercentConversionBasketController;
use App\Controller\PercentConversionCommandController;
use App\Controller\RecurrenceCommandClientController;
use App\Controller\TotalAmountCommandController;
use App\Repository\CommandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommandRepository::class)]
#[ApiResource(
    collectionOperations: ['GET','POST',
    'get_total_command_from_dates' => [
        'method' => 'GET',
        'path' => '/commands/get_total_command',
        'controller' => CommandCountController::class],
    
    'get_total_command_amount_from_dates' => [
        'method' => 'GET',
        'path' => '/commands/get_total_command_amount',
        'controller' => TotalAmountCommandController::class],

    'get_total_basket_from_dates' => [
        'method' => 'GET',
        'path' => '/commands/get_total_basket_from_dates',
        'controller' => BasketCountController::class],

    'get_average_basket_amount_from_dates' => [
        'method' => 'GET',
        'path' => '/commands/get_average_basket_amount_from_dates',
        'controller' => AverageBasketAmountController::class],
    
    'get_percent_conversion_basket_from_dates' => [
        'method' => 'GET',
        'path' => '/commands/get_percent_conversion_basket_from_dates',
        'controller' => PercentConversionBasketController::class],

    'get_percent_conversion_command_from_dates' => [
        'method' => 'GET',
        'path' => '/commands/get_percent_conversion_command_from_dates',
        'controller' => PercentConversionCommandController::class],

    'get_recurrence_command_client_from_dates' => [
        'method' => 'GET',
        'path' => '/commands/get_recurrence_command_client_from_dates',
        'controller' => RecurrenceCommandClientController::class],
    ],
    itemOperations:['GET'],
    normalizationContext: ['groups' => ['groupCommand']]
)]
//Filtrer avec le nom de la ville (partial = idem LIKE)
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
#[ApiFilter(OrderFilter::class, properties: ['command.user'])]
#[ApiFilter(SearchFilter::class, properties: ['user.firstName' => 'exact'])]
class Command
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['groupAdress','groupCommand','groupProduct','groupUser'])]
    private $id;

    #[ORM\Column(type: 'integer')]
    #[  Assert\NotNull(
        message: 'Le prix total (en cents) de la commande doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'Le prix total (en cents) de la commande doit être renseigné',
        normalizer: 'trim'
    ),
        Assert\Positive(
        message: 'Le prix total de la commande doit être un nombre positif',
    ),
        Assert\Type(
        type: 'integer',
        message: 'Le prix total de la commande doit être un nombre',
    )]
    #[Groups(['groupAdress','groupCommand','groupProduct','groupUser'])]
    private $totalPrice;

    #[ORM\Column(type: 'integer')]
    #[  Assert\NotNull(
        message: 'Le numéro de commande doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'Le numéro de commande doit être renseigné',
        normalizer: 'trim'
    ),
        Assert\Positive(
        message: 'Le numéro de commande doit être un nombre positif',
    ),
        Assert\Type(
        type: 'integer',
        message: 'Le numéro de commande doit être un nombre',
    )]
    #[Groups(['groupAdress','groupCommand','groupProduct','groupUser'])]
    private $numCommand;

    #[ORM\Column(type: 'datetime')]
    #[  Assert\NotNull(
        message: 'La date de la commande doit être renseignée',
    ),
        Assert\NotBlank(
        message: 'La date de commande doit être renseignée',
        normalizer: 'trim'
    ),
    //     Assert\EqualTo(
    //     value: 'today',
    //     message: 'La date de la commande doit être associée à aujourd\'hui',
    // ),
        Assert\Type(
        type: 'datetime',
        message: 'La date de la commande doit être une chaine de caractères',
    )]  
    #[Groups(['groupAdress','groupCommand','groupProduct','groupUser'])]
    private $createdAt;

    #[ORM\Column(type: 'integer')]
    #[  Assert\NotNull(
        message: 'Le statut doit être renseigné',
    ),
        Assert\NotBlank(
        message: 'Le statut doit être renseigné',
        normalizer: 'trim'
    ),
        Assert\Positive(
        message: 'Le statut doit être un nombre positif',
    ),
        Assert\Type(
        type: 'integer',
        message: 'Le statut doit être un nombre',
    )]
    #[Groups(['groupAdress','groupCommand','groupProduct','groupUser'])]
    private $status;

    #[ORM\ManyToOne(targetEntity: Adress::class, inversedBy: 'commands')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['groupCommand'])]
    private $adress;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'commands')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['groupCommand'])]
    private $user;

    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'commands')]
    #[Groups(['groupCommand'])]
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(int $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getNumCommand(): ?int
    {
        return $this->numCommand;
    }

    public function setNumCommand(int $numCommand): self
    {
        $this->numCommand = $numCommand;

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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAdress(): ?Adress
    {
        return $this->adress;
    }

    public function setAdress(?Adress $adress): self
    {
        $this->adress = $adress;

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
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $this->products->removeElement($product);

        return $this;
    }
}
