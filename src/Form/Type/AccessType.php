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

namespace Effiana\Access\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class AccessType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           ->add('view', CheckboxType::class, [
               'required' => false
           ])
           ->add('create', CheckboxType::class, [
               'required' => false,
               'disabled' => true
           ])
           ->add('edit', CheckboxType::class, [
               'required' => false,
               'disabled' => true
           ])
           ->add('delete', CheckboxType::class, [
               'required' => false,
               'disabled' => true
           ])
        ;
    }

}