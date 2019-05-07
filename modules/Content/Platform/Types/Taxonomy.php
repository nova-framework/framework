<?php

namespace Modules\Content\Platform\Types;

use Modules\Content\Platform\ContentType;
use Modules\Content\Platform\TaxonomyTypeManager;


abstract class Taxonomy extends ContentType
{

    public function __construct(TaxonomyTypeManager $manager, array $options)
    {
        parent::__construct($manager, $options);
    }
}
