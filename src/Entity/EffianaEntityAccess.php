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

use BrandOriented\DatabaseBundle\Entity\ProjectRole;
use Knp\DoctrineBehaviors\Contract\Entity\BlameableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Blameable\BlameableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Class EffianaAccess
 * @package Effiana\Access\Entity
 *
 * @Doctrine\ORM\Mapping\Table(name="effiana_entity_access")
 * @Doctrine\ORM\Mapping\Entity()
 */
class EffianaEntityAccess implements BlameableInterface, TimestampableInterface
{
    use BlameableTrait;
    use TimestampableTrait;
    /**
     * @var int
     *
     * @Doctrine\ORM\Mapping\Column(name="id", type="integer")
     * @Doctrine\ORM\Mapping\Id
     * @Doctrine\ORM\Mapping\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @Doctrine\ORM\Mapping\Column(name="entity_id", type="integer")
     */
    protected $entityId;

    /**
     * @var string
     * @Doctrine\ORM\Mapping\Column(name="entity_class", type="string", length=1000)
     */
    protected $entityClass;

    /**
     * @var int
     * @Doctrine\ORM\Mapping\Column(name="mask", type="integer")
     */
    private $mask;

    /**
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var ProjectRole|null
     *
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="BrandOriented\DatabaseBundle\Entity\Users")
     * @Doctrine\ORM\Mapping\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(name="project_role_id", type="integer", nullable=true)
     */
    private $projectRoleId;

    /**
     * @var ProjectRole|null
     *
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="BrandOriented\DatabaseBundle\Entity\ProjectRole")
     * @Doctrine\ORM\Mapping\JoinColumn(name="project_role_id", referencedColumnName="id", nullable=true)
     */
    private $projectRole;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    /**
     * @param int $entityId
     * @return self
     */
    public function setEntityId(int $entityId): self
    {
        $this->entityId = $entityId;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass(): ?string
    {
        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     * @return self
     */
    public function setEntityClass(string $entityClass): self
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * @return int
     */
    public function getMask(): ?int
    {
        if($this->mask !== null) {
            MaskBuilder::resolveMask($this->mask);
        }
        return $this->mask;
    }

    /**
     * @param int $mask
     * @return self
     */
    public function setMask(int $mask): self
    {
        MaskBuilder::resolveMask($mask);
        $this->mask = $mask;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int|null $userId
     * @return self
     */
    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return ProjectRole|null
     */
    public function getUser(): ?ProjectRole
    {
        return $this->user;
    }

    /**
     * @param ProjectRole|null $user
     * @return self
     */
    public function setUser(?ProjectRole $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getProjectRoleId(): ?int
    {
        return $this->projectRoleId;
    }

    /**
     * @param int|null $projectRoleId
     * @return self
     */
    public function setProjectRoleId(?int $projectRoleId): self
    {
        $this->projectRoleId = $projectRoleId;
        return $this;
    }

    /**
     * @return ProjectRole|null
     */
    public function getProjectRole(): ?ProjectRole
    {
        return $this->projectRole;
    }

    /**
     * @param ProjectRole|null $projectRole
     * @return self
     */
    public function setProjectRole(?ProjectRole $projectRole): self
    {
        $this->projectRole = $projectRole;
        return $this;
    }
}
