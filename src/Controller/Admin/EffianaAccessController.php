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

use Doctrine\ORM\EntityManager;
use Effiana\Access\Entity\EffianaEntityAccess;
use Effiana\Access\Form\Type\AccessType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class EffianaAccessController
 * @package Effiana\Access\Controller\Admin
 *
 * @Route("/manager/access")
 */
class EffianaAccessController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/add")
     * @return Response
     */
    public function add(Request $request): Response
    {
        $acl = new EffianaEntityAccess();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(AccessType::class, $acl, [
           'entityManager' => $em
        ]);

        $form->handleRequest($request);

        return $this->render(
            '@EffianaAccess/form.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}