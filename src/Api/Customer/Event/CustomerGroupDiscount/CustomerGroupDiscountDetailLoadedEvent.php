<?php declare(strict_types=1);

namespace Shopware\Api\Customer\Event\CustomerGroupDiscount;

use Shopware\Api\Customer\Collection\CustomerGroupDiscountDetailCollection;
use Shopware\Api\Customer\Event\CustomerGroup\CustomerGroupBasicLoadedEvent;
use Shopware\Context\Struct\ShopContext;
use Shopware\Framework\Event\NestedEvent;
use Shopware\Framework\Event\NestedEventCollection;

class CustomerGroupDiscountDetailLoadedEvent extends NestedEvent
{
    public const NAME = 'customer_group_discount.detail.loaded';

    /**
     * @var ShopContext
     */
    protected $context;

    /**
     * @var CustomerGroupDiscountDetailCollection
     */
    protected $customerGroupDiscounts;

    public function __construct(CustomerGroupDiscountDetailCollection $customerGroupDiscounts, ShopContext $context)
    {
        $this->context = $context;
        $this->customerGroupDiscounts = $customerGroupDiscounts;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): ShopContext
    {
        return $this->context;
    }

    public function getCustomerGroupDiscounts(): CustomerGroupDiscountDetailCollection
    {
        return $this->customerGroupDiscounts;
    }

    public function getEvents(): ?NestedEventCollection
    {
        $events = [];
        if ($this->customerGroupDiscounts->getCustomerGroups()->count() > 0) {
            $events[] = new CustomerGroupBasicLoadedEvent($this->customerGroupDiscounts->getCustomerGroups(), $this->context);
        }

        return new NestedEventCollection($events);
    }
}