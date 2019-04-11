<?php

namespace Modules\Content\Platform\Types;

use Modules\Content\Platform\ContentType;
use Modules\Content\Platform\TaxonomyManager;


abstract class Taxonomy extends Content
{

    public function __construct(TaxonomyManager $manager, array $options)
    {
        parent::__construct($manager, $options);
    }
}
