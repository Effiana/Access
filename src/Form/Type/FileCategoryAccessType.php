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

use BrandOriented\DatabaseBundle\Entity\FileCategory;
use BrandOriented\DatabaseBundle\Entity\ProjectRole;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Effiana\Access\Entity\EffianaEntityAccess;
use Effiana\Access\Helper\MaskHelperTrait;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileCategoryAccessType extends AbstractType
{
    use MaskHelperTrait;
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws ORMException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('entityClass', HiddenType::class, [
                'data' => FileCategory::class
            ])
            ->add('projectRole', EntityType::class, [
                'class' => ProjectRole::class,
                'choice_label' => 'name',
                'attr' => [
                    'readOnly' => $options['data'] instanceof EffianaEntityAccess && $options['data']->getProjectRole() !== null
                ],
                'query_builder' => static function(EntityRepository $repository) use ($options) {
                    $query = $repository->createQueryBuilder('projectRole');
                    if($options['data'] instanceof EffianaEntityAccess && $options['data']->getProjectRole() !== null) {
                        $query
                            ->andWhere('projectRole.id = :projectRoleId')
                            ->setParameters([
                                'projectRoleId' => $options['data']->getProjectRole()->getId()
                            ]);
                    }

                    return $query;
                }
            ])
            ->add('entityId',EntityType::class, [
                'class' => FileCategory::class,
                'choice_label' => 'name',
                'attr' => [
                    'readOnly' => $options['data'] instanceof EffianaEntityAccess && $options['data']->getEntityId() !== null
                ],
                'query_builder' => static function(EntityRepository $repository) use ($options) {
                    $query = $repository->createQueryBuilder('fileCategory');
                    if($options['data'] instanceof EffianaEntityAccess && $options['data']->getEntityId() !== null) {
                        $query
                            ->andWhere('fileCategory.id = :entityId')
                            ->setParameters([
                                'entityId' => $options['data']->getEntityId()
                            ]);
                    }

                    return $query;
                }
            ])
            ->add('mask', AccessType::class)


            ->add('submit', SubmitType::class, [
                'label' => 'Save'
            ])
        ;

        $builder
            ->get('entityId')
                ->addModelTransformer(new CallbackTransformer(
                static function ($id) {
                    return $id;
                },
                static function (FileCategory $category) {
                    return $category->getId();
                }
            ))
        ;

        $builder
            ->get('mask')
                ->addModelTransformer(new CallbackTransformer(
                static function (?int $id) {
                    if($id === null) {
                        return [];
                    }
                    return self::getArrayMask($id);
                },
                static function ($value) {
                    return self::getIntMask($value);
                }
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EffianaEntityAccess::class,
            'entityManager' => null
        ]);
    }
}