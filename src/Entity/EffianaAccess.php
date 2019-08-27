<?php
/**
 * This file is part of the Effiana package.
 *
 * (c) Effiana, LTD
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Dominik Labudzinski <dominik@labudzinski.com>
 */
declare(strict_types=0);

namespace Effiana\Access\Entity;

use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Class EffianaAccess
 * @package Effiana\Access\Entity
 *
 * @Doctrine\ORM\Mapping\Table(name="effiana_access")
 * @Doctrine\ORM\Mapping\Entity()
 */
class EffianaAccess
{
    use Blameable;
    use Timestampable;
    /**
     * @var int
     *
     * @Doctrine\ORM\Mapping\Column(name="id", type="integer")
     * @Doctrine\ORM\Mapping\Id
     * @Doctrine\ORM\Mapping\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Doctrine\ORM\Mapping\Column(name="entity_class", type="string", length=1000)
     */
    protected $entityClass;

    /**
     * @var int
     * @Doctrine\ORM\Mapping\Column(name="entity_id", type="integer")
     */
    protected $entityId;



}