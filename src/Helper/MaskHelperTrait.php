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

use Exception;
use ReflectionClass;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

trait MaskHelperTrait
{
    /**
     * @param $value
     * @return array
     */
    protected function getArrayMask(int $value): array
    {
        $mask = [];
        try {
            $constants = (new ReflectionClass(MaskBuilder::class))->getConstants();
            $pattern = (new MaskBuilder($value))->getPattern();
            $pattern = str_split(str_replace(MaskBuilder::OFF, null, $pattern));

            foreach ($pattern as $maskMark) {
                switch (array_flip($constants)[$maskMark]) {
                    case 'CODE_EDIT':
                        $mask['edit'] = true;
                        break;
                    case 'CODE_CREATE':
                        $mask['create'] = true;
                        break;
                    case 'CODE_VIEW':
                        $mask['view'] = true;
                        break;
                    case 'CODE_DELETE':
                        $mask['delete'] = true;
                        break;
                }
            }
        } catch (Exception $ex) {
        }
        return $mask;
    }

    /**
     * @param array $value
     * @return int
     */
    public function getIntMask(array $value): int
    {
        $mask = new MaskBuilder();

        foreach ($value as $name => $item) {
            switch ($name) {
                case 'edit':
                    if($item === true) {
                        $mask->add('edit');
                    } else {
                        $mask->remove('edit');
                    }
                    break;
                case 'create':
                    if($item === true) {
                        $mask->add('create');
                    } else {
                        $mask->remove('create');
                    }
                    break;
                case 'view':
                    if($item === true) {
                        $mask->add('view');
                    } else {
                        $mask->remove('view');
                    }
                    break;
                case 'delete':
                    if($item === true) {
                        $mask->add('delete');
                    } else {
                        $mask->remove('delete');
                    }
                    break;
            }
        }

        return $mask->get();
    }
}