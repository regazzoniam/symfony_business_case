<?php

namespace App\Twig;

use App\Entity\Command;
use App\Service\BasketService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class SumPriceAllProductExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('sumPriceAllProduct', [$this, 'showSumPriceAllProduct']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sum', [$this, 'doSomething']),
        ];
    }

    public function showSumPriceAllProduct(Command $basketEntity)
    {
        $products = $basketEntity->getProducts();
        $pricesOfProducts = [];
        foreach($products as $product){
            $price = $product->getPrice();
            array_push($pricesOfProducts, $price);
        }
        $totalPrice = array_sum($pricesOfProducts);
        return $totalPrice;
    }
}
