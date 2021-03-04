<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thelia\Core\Form\Type\Field;

use Thelia\Model\AdminLogQuery;

/**
 * Class AdminLogIdType.
 *
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class AdminLogIdType extends AbstractIdType
{
    /**
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     *
     * Get the model query to check
     */
    protected function getQuery()
    {
        return new AdminLogQuery();
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'admin_log_id';
    }
}
