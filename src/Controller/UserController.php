<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
           
        ]);
    }
    /**
     * @Route("/user/add", name="user_add", methods={"GET","POST"})
     */
    public function addForm(EntityManagerInterface $entityManager, Request $request)
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            ));
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user_show_all');
        }
        return $this->render('user/add.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
        ]);
    }



    /**
     * @IsGranted("ROLE_FORMATEUR")
     * @Route("/user/show", name="user_show_all", methods={"GET"})
     */
    public function showAllUser(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        if (!$users) {
            throw $this->createNotFoundException('La table est vide');
        }
        return $this->render('user/show.html.twig', [
            'controller_name' => 'userController',
            'users' => $users,
        ]);
    }
    /**
     * @IsGranted("ROLE_FORMATEUR")
     * @Route("/user/{id}", name="user_show", methods={"GET"})
     */
    public function showUser(int $id, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException(
                'Personne non trouvée avec l\'id ' . $id
            );
        }
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'personne' => $user,
            'adjectif' => 'recherchée'
        ]);
    }
    /**
     * @Route("/user/delete/{id}", name="user_delete", methods={"DELETE"})
     */
    public function deleteUser(int $id, EntityManagerInterface
    $entityManager)
    {
        $use = $entityManager->getRepository(User::class)
            ->find($id);
        if (!$use) {
            throw $this->createNotFoundException(
                'Personne non trouvée avec l\'id ' . $id
            );
        }
        $entityManager->remove($use);
        $entityManager->flush();
        return $this->redirectToRoute("user_show_all");
    }


    /**
     * @Route("/user/{id}/edit", name="user_update", methods={"GET","POST"})
     */
    public function updateUser(User $user, EntityManagerInterface $entityManager, Request $request)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityManager->flush();
            return $this->redirectToRoute('user_show_all');
        }
        return $this->render('user/add.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_FORMATEUR")
     * @Route("/user/{nom}/{prenom}", name="user_show_one", methods={"GET"})
     */
    public function showUserByNomAndPrenom(
        string $nom,
        string $prenom,
        UserRepository $userRepository
    ) {
        $user = $userRepository->findOneBy([
            "nom" => $nom,
            "prenom" => $prenom
        ]);
        if (!$user) {
            throw $this->createNotFoundException('Personne non trouvée');
        }
        return $this->render('user/index.html.twig', [
            'controller_name' => 'userController',
            'personne' => $user,
            'adjectif' => 'recherchée'
        ]);
    }
   
}
