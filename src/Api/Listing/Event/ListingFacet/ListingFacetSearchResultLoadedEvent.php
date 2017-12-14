<?php declare(strict_types=1);

namespace Shopware\Api\Listing\Event\ListingFacet;

use Shopware\Api\Listing\Struct\ListingFacetSearchResult;
use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Event\NestedEvent;

class ListingFacetSearchResultLoadedEvent extends NestedEvent
{
    const NAME = 'listing_facet.search.result.loaded';

    /**
     * @var ListingFacetSearchResult
     */
    protected $result;

    public function __construct(ListingFacetSearchResult $result)
    {
        $this->result = $result;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): TranslationContext
    {
        return $this->result->getContext();
    }
}