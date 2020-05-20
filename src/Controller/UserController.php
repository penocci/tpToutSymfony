<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
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
     * @Route("/user/add", name="user_add")
     */
    public function addForm(EntityManagerInterface $entityManager, Request $request)
    {

        $personne = new User();

        $form = $this->createForm(UserType::class, $personne);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $personne = $form->getData();
            $entityManager->persist($personne);
            $entityManager->flush();
            return $this->redirectToRoute('user_show_all');
        }
        return $this->render('user/add.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/user/show", name="user_show_all")
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
     * @Route("/user/{id}", name="user_show")
     */
    public function showUser(int $id, UserRepository $userRepository)
    {
        $personne = $userRepository->find($id);
        if (!$personne) {
            throw $this->createNotFoundException(
                'Personne non trouvée avec l\'id ' . $id
            );
        }
        return $this->render('user/index.html.twig', [
            'controller_name' => 'PersonneController',
            'personne' => $personne,
            'adjectif' => 'recherchée'
        ]);
    }
    /**
     * @Route("/user/delete/{id}", name="user_delete")
     */
    public function deleteUser(int $id, EntityManagerInterface
    $entityManager)
    {
        $personne = $entityManager->getRepository(User::class)
            ->find($id);
        if (!$personne) {
            throw $this->createNotFoundException(
                'Personne non trouvée avec l\'id ' . $id
            );
        }
        $entityManager->remove($personne);
        $entityManager->flush();
        return $this->redirectToRoute("user_show_all");
    }


    /**
     * @Route("/user/edit/{id}", name="user_update")
     */
    public function updateUser(User $personne, EntityManagerInterface $entityManager, Request $request)
    {
        $form = $this->createForm(UserType::class, $personne);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $personne = $form->getData();
            $entityManager->flush();
            return $this->redirectToRoute('user_show_all');
        }
        return $this->render('user/add.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{nom}/{prenom}", name="user_show_one")
     */
    public function showUserByNomAndPrenom(
        string $nom,
        string $prenom,
        UserRepository $userRepository
    ) {
        $personne = $userRepository->findOneBy([
            "nom" => $nom,
            "prenom" => $prenom
        ]);
        if (!$personne) {
            throw $this->createNotFoundException('Personne non trouvée');
        }
        return $this->render('personne/index.html.twig', [
            'controller_name' => 'PersonneController',
            'personne' => $personne,
            'adjectif' => 'recherchée'
        ]);
    }
   
}
