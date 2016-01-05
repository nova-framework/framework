<?php
/**
 * Filtrator
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 04th, 2016
 */

namespace Nova\Input\Filtration;

use Sirius\Filtration\Filtrator as BaseFiltrator;

use Nova\Input\Filtration\FilterFactory;


class Filtrator extends BaseFiltrator
{
    public function __construct(FilterFactory $filterFactory = null)
    {
        if ($filterFactory === null) {
            $filterFactory = new FilterFactory();
        }

        parent::__construct($filterFactory);
    }
}
