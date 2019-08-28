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

namespace Effiana\Access\Helper;

use BrandOriented\DatabaseBundle\Entity\ProjectRoleUser;
use Doctrine\ORM\EntityManager;
use Effiana\Access\Entity\EffianaEntityAccess;

class ProjectRoleAccessToEntity
{
    use MaskHelperTrait;
    /**
     * @var int
     */
    private $userId;
    /**
     * @var array
     */
    private $projectIds;
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ProjectRoleAccessToEntity constructor.
     * @param EntityManager $entityManager
     * @param int $userId
     * @param array $projectIds
     */
    public function __construct(EntityManager $entityManager, int $userId, array $projectIds)
    {
        $this->userId = $userId;
        $this->projectIds = $projectIds;
        $this->entityManager = $entityManager;
    }

    private function getUserProjectRoles(): array
    {
        return $this->entityManager->getRepository(ProjectRoleUser::class)
            ->createQueryBuilder('projectRoleUser')
            ->select('projectRoleUser.projectRoleId AS id')
            ->andWhere('projectRoleUser.userId = :userId')
            ->andWhere('projectRoleUser.projectId IN (:projectIds)')
            ->setParameters([
                'userId' => $this->userId,
                'projectIds' => $this->projectIds
            ])->getQuery()->getArrayResult();
    }

    public function getAccess(string $class): array
    {
        $projectRolesForCurrentUser = $this->getUserProjectRoles();

        $result = $this->entityManager->getRepository(EffianaEntityAccess::class)
            ->createQueryBuilder('effianaEntityAccess')
            ->select('partial effianaEntityAccess.{id,entityId,mask}')
            ->andWhere('effianaEntityAccess.entityClass = :entityClass')
            ->andWhere('effianaEntityAccess.projectRoleId IN (:projectRoleIds)')
            ->setParameters([
                'entityClass' => $class,
                'projectRoleIds' => $projectRolesForCurrentUser
            ])->getQuery()->getArrayResult();

        return array_map(static function ($item) {
            $item['mask'] = self::getArrayMask($item['mask']);
            return $item;
        }, $result);
    }

}