<?php declare(strict_types=1);

namespace Shopware\Core\Content\Product\Aggregate\ProductPrice;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Rule\RuleEntity;
use Shopware\Core\Framework\Pricing\PriceRuleEntity;
use Shopware\Core\System\Currency\CurrencyEntity;

class ProductPriceEntity extends PriceRuleEntity
{
    /**
     * @var string
     */
    protected $productId;

    /**
     * @var int
     */
    protected $quantityStart;

    /**
     * @var int|null
     */
    protected $quantityEnd;

    /**
     * @var ProductEntity|null
     */
    protected $product;

    /**
     * @var CurrencyEntity|null
     */
    protected $currency;

    /**
     * @var RuleEntity|null
     */
    protected $rule;

    /**
     * @var array|null
     */
    protected $attributes;

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(ProductEntity $product): void
    {
        $this->product = $product;
    }

    public function getCurrency(): ?CurrencyEntity
    {
        return $this->currency;
    }

    public function setCurrency(CurrencyEntity $currency): void
    {
        $this->currency = $currency;
    }

    public function getRule(): ?RuleEntity
    {
        return $this->rule;
    }

    public function setRule(RuleEntity $rule): void
    {
        $this->rule = $rule;
    }

    public function getQuantityStart(): int
    {
        return $this->quantityStart;
    }

    public function setQuantityStart(int $quantityStart): void
    {
        $this->quantityStart = $quantityStart;
    }

    public function getQuantityEnd(): ?int
    {
        return $this->quantityEnd;
    }

    public function setQuantityEnd(?int $quantityEnd): void
    {
        $this->quantityEnd = $quantityEnd;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    public function setAttributes(?array $attributes): void
    {
        $this->attributes = $attributes;
    }
}
