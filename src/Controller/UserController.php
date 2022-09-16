<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->json([
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_user_new", methods={"POST"})
     */
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $data = json_decode($request->getContent(), true);
        $hashedPassword = $passwordHasher->hashPassword(
            $user,   
            $data['password']
        );
        $user
            ->setName($data['name'])
            ->setEmail($data['email'])
            ->setStatus($data['status'])
            ->setPrivilege($data['privilege'])
            ->setPassword($hashedPassword)
            ->setCreatedAt(new \DateTime("now", new \DateTimeZone("America/Sao_Paulo")))
            ->setUpdatedAt(new \DateTime("now", new \DateTimeZone("America/Sao_Paulo")))
        ;

        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->persist($user);
        $doctrine->flush();

        return $this->json([
            'UserCreated' => $user
        ]);

    }

    /**
     * @Route("/findOne/{id}", name="app_user_show", methods={"GET"})
     */
    public function show($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOne($id);

        return $this->json([
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="_editUser", methods={"PUT"})
     */
    public function edit(Request $request, $id): Response
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        $user
            ->setName($data['name'])
            ->setEmail($data['email'])
            ->setStatus($data['status'])
            ->setPrivilege($data['privilege'])
            ->setUpdatedAt(new \DateTime("now", new \DateTimeZone("America/Sao_Paulo")))
        ;

        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->flush();

        return $this->json([
            'UserUpdated' => $user
        ]);
        
    }

    /**
     * @Route("/delete/{id}", name="_deleteUser", methods={"DELETE"})
     * 
     */
    public function delete($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
 
        if (!$user) {
            return $this->json( [
                "Error" => 'No user found for id' ." ".$id,
            ]);
        }
 
        $entityManager->remove($user);
        $entityManager->flush();
 
        return $this->json([
                "SuccessUserDeleted" => $user
        ]);
 
    }

    /**
     * @Route("/active", name="app_user_active", methods={"GET"})
     */
    public function active(): Response
    {   

        $entityManager = $this->getDoctrine()->getManager();
        $userActive =  $entityManager->getRepository(User::class)
                            ->findAllActive(true);
   
        return $this->json([
            'user' => $userActive,
        ]);
    }

    /**
     * @Route("/disabled", name="app_user_disabled", methods={"GET"})
     */
    public function disabled(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $userDisabled =  $entityManager->getRepository(User::class)
                                    ->findAllActive(false);
        return $this->json([
            'user' => $userDisabled,
        ]);
    }

    /**
     * @Route("/disable/{id}", name="app_user_disable", methods={"PUT"})
     */
    public function disableUser($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        $user
            ->setStatus(false)
            ->setUpdatedAt(new \DateTime("now", new \DateTimeZone("America/Sao_Paulo")))
        ;

        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->flush();

        return $this->json([
            'Success' => 'user '.$id. ' disabled',
        ]);
    }
}
