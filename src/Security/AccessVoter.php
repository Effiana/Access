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

namespace Effiana\Access\Security;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AccessVoter
 * @package Effiana\Access\Security
 */
class AccessVoter extends Voter
{
    public const CREATE = 'create';
    public const READ   = 'read';
    public const UPDATE = 'update';
    public const DELETE = 'delete';

    protected const ALL = [
        self::CREATE,
        self::READ,
        self::UPDATE,
        self::DELETE,
    ];
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, self::ALL, true)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            // the user must be logged in; if not, deny access
            return false;
        }


        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate($subject, $user);
            case self::READ:
                return $this->canRead($subject, $user);
            case self::UPDATE:
                return $this->canUpdate($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
        }

        throw new LogicException('This code should not be reached!');
    }

    private function canCreate($post, UserInterface $user): ?bool
    {
        // if they can edit, they can view
        if ($this->canUpdate($post, $user)) {
            return true;
        }
    }

    private function canRead($post, UserInterface $user): ?bool
    {
        // if they can edit, they can view
        if ($this->canUpdate($post, $user)) {
            return true;
        }
    }

    private function canUpdate($post, UserInterface $user): bool
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $post->getOwner();
    }
    private function canDelete($post, UserInterface $user): bool
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $post->getOwner();
    }
}