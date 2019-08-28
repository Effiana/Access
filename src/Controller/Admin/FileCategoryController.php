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

namespace Effiana\Access\Controller\Admin;

use BrandOriented\DatabaseBundle\Entity\FileCategory;
use BrandOriented\DatabaseBundle\Entity\ProjectRole;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Effiana\Access\Entity\EffianaEntityAccess;
use Effiana\Access\Form\Type\FileCategoryAccessType;
use Effiana\Access\Helper\MaskHelperTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class FileCategoryController
 * @package Effiana\Access\Controller\Admin
 *
 * @Route("/manager/access/fileCategory")
 */
class FileCategoryController extends AbstractController
{
    use MaskHelperTrait;
    /**
     * @return Response
     * @Route("/", methods={"GET"}, name="file_category_access")
     */
    public function index(): Response
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $access = $em->getRepository(EffianaEntityAccess::class)
            ->createQueryBuilder('access')
            ->select('partial access.{id, mask, entityId, projectRoleId}')
            ->andWhere('access.entityClass = :entityClass')
            ->setParameters([
                'entityClass' => FileCategory::class
            ])->getQuery()->getArrayResult();

        $entityIds = array_column($access, 'entityId');
        $projectRoleIds = array_column($access, 'projectRoleId');

        $entities = $em->getRepository(FileCategory::class)
            ->createQueryBuilder('category', 'category.id')
            ->select('partial category.{id, name}')
            ->andWhere('category.id IN (:entityIds)')
            ->setParameters([
                'entityIds' => $entityIds
            ])->getQuery()->getArrayResult();


        $projectRoles = $em->getRepository(ProjectRole::class)
            ->createQueryBuilder('role', 'role.id')
            ->select('partial role.{id, name}')
            ->andWhere('role.id IN (:projectRoleIds)')
            ->setParameters([
                'projectRoleIds' => $projectRoleIds
            ])->getQuery()->getArrayResult();

        foreach ($access as $key => $value) {
            $access[$key]['role'] = 'undefined';
            $access[$key]['entity'] = 'undefined';
            if(isset($entities[$value['entityId']])) {
                $access[$key]['entity'] = $entities[$value['entityId']]['name'];
            }
            if(isset($projectRoles[$value['projectRoleId']])) {
                $access[$key]['role'] = $projectRoles[$value['projectRoleId']]['name'];
            }

            $access[$key]['mask'] = $this->getArrayMask($value['mask']);
        }

        return $this->render(
            '@EffianaAccess/list.html.twig',
            [
                'access' => $access
            ]
        );
    }
    /**
     * @param Request $request
     * @param int|null $id
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @Route("/add", methods={"GET", "POST"}, name="file_category_access_add")
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="file_category_access_edit")
     */
    public function addAction(Request $request, ?int $id): Response
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $acl = new EffianaEntityAccess();
        if($id !== null) {
            $acl = $em->getRepository(EffianaEntityAccess::class)->find($id);
        }

        if($acl instanceof EffianaEntityAccess) {
            $form = $this->createForm(FileCategoryAccessType::class, $acl, [
                'entityManager' => $em
            ]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $used = [];
                if($id === null) {
                    $used = $em->getRepository(EffianaEntityAccess::class)
                        ->createQueryBuilder('access')
                        ->select('access.id')
                        ->andWhere('access.entityClass = :entityClass')
                        ->andWhere('access.entityId = :entityId')
                        ->andWhere('access.projectRoleId = :projectRoleId')
                        ->setParameters([
                            'entityClass' => FileCategory::class,
                            'entityId' => $acl->getEntityId(),
                            'projectRoleId' => $acl->getProjectRole()->getId()
                        ])->getQuery()->getArrayResult();
                    $used = array_column($used, 'id');
                }
                if(empty($used)) {
                    $em->persist($acl);
                    $em->flush($acl);

                    return new RedirectResponse($this->generateUrl('file_category_access_edit', ['id' => $acl->getId()]));
                }

                $this->get('session')->getFlashBag()->add('error', 'Permissions already exist. You can change them below.');

                return new RedirectResponse($this->generateUrl('file_category_access_edit', ['id' => end($used)]));
            }

            return $this->render(
                '@EffianaAccess/form.html.twig',
                [
                    'form' => $form->createView()
                ]
            );
        }

        throw new NotFoundHttpException('Not found');
    }
}