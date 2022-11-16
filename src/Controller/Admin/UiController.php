<?php

namespace App\Controller\Admin;

use App\Entity\User;
use SmartCoreBundle\Form\Type\ColorType;
use SmartCoreBundle\Form\Type\CountryType;
use SmartCoreBundle\Form\Type\FontAwesomeType;
use SmartCoreBundle\Form\Type\Select2AjaxType;
use SmartCoreBundle\Form\Type\Select2EntityType;
use SmartCoreBundle\Form\Type\Select2TagType;
use SmartCoreBundle\Form\Type\Select2Type;
use SmartCoreBundle\Form\Type\SwitchType;
use SmartCoreBundle\Form\Type\TinymceType;
use SmartDatatablesBundle\Manager\TableManager;
use SmartFileUploadBundle\Form\Type\FileType;
use SmartUserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UiController extends AbstractController
{
    #[Route('/admin/ui', name: 'admin_ui')]
    public function index(Request $request): Response
    {
        // Here is an example of core form types
        $form = $this->createFormBuilder()
            ->add('color', ColorType::class)
            ->add('country', CountryType::class)
            ->add('fa', FontAwesomeType::class)
            ->add('usersEntity', Select2EntityType::class, [
                'class' => User::class,
            ])
            ->add('searchUser', Select2AjaxType::class, [
                'class' => User::class,
                'remote_route' => 'user_list',
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
            ])
            ->add('searchUser', Select2AjaxType::class, [
                'class' => User::class,
                'remote_route' => 'admin_ui_search',
                'multiple' => true,
            ])
            ->add('tags', Select2TagType::class, [
                'multiple' => true,
                // Predefined tags + add allowed
            ])
            ->add('tags1', Select2TagType::class, [
                'multiple' => true,
                'choices' => [
                    'Lemon' => 'lemon',
                    'Orange' => 'orange',
                ],
            ])
            ->add('categories', Select2Type::class, [
                'multiple' => true,
                'choices' => [
                    'Lemon' => 'lemon',
                    'Orange' => 'orange',
                ],
            ])
            ->add('enabled', SwitchType::class, [
                'label' => 'Enabled',
                'size' => SwitchType::SIZE_MEDIUM,
            ])
            ->add('content', TinymceType::class, [
                'toolbar_template' => TinymceType::TOOLBAR_MINIMAL,
            ])
            ->add('profilePictures', FileType::class, [
                'multiple' => true,
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('admin/ui/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/ui/datatable', name: 'admin_ui_datatable')]
    public function datatable(Request $request, TableManager $tableManager): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->queryAll();

        $table = $tableManager->init('users', $users, 'admin_ui_datatable');
        $table
            ->addColumn('username', 'Username', 'username')
            ->addColumn('email', 'Email', 'email');

        if ($request->isXmlHttpRequest()) {
            return $tableManager->prepareData();
        }

        return $this->render('admin/ui/datatable.html.twig', [
            'datatable' => $tableManager->renderView(),
        ]);
    }

    #[Route('/admin/ui/search', name: 'admin_ui_search', condition: 'request.isXmlHttpRequest()')]
    public function userList(Request $request): JsonResponse
    {
        $query = $request->get('q');

        if (null === $query) {
            $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        } else {
            $users = $this->getDoctrine()->getRepository(User::class)
                ->createQueryBuilder('u')
                ->where('u.email LIKE ?1')
                ->setParameter('1', '%'.$query.'%')
                ->getQuery()
                ->execute();
        }

        $users = array_map(function (UserInterface $user) {
            return [
                'id' => $user->getId(),
                'text' => $user->getUsername(),
            ];
        }, $users);

        return new JsonResponse($users);
    }
}
