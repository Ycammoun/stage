<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    /*#[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function apiLogin(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['login']) || !isset($data['motDePasse'])) {
            return new JsonResponse(['error' => 'Champs login et motDePasse requis'], 400);
        }

        $login = $data['login'];
        $motDePasse = $data['motDePasse'];

        $user = $em->getRepository(Utilisateur::class)->findOneBy(['login' => $login]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $motDePasse)) {
            return new JsonResponse(['error' => 'Identifiants invalides'], 401);
        }

        // Succès, renvoyer données user sans mot de passe
        return new JsonResponse([
            'success' => true,
            'user' => [
                'id' => $user->getId(),
                'login' => $user->getLogin(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
            ],
        ]);
    }*/
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function apiLoginAction(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // On vérifie les clés 'login' et 'password' (pas 'motDePasse')
        if (!isset($data['login']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Champs login et password requis'], 400);
        }

        $user = $em->getRepository(Utilisateur::class)->findOneBy(['login' => $data['login']]);

        // Vérifie si utilisateur existe et si mot de passe valide
        if (!$user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['error' => 'Identifiants invalides'], 401);
        }

        $token = $jwtManager->create($user);

        return new JsonResponse([
            'success' => true,
            'token' => $token,
            'utilisateur' => [
                'id' => $user->getId(),
                'login' => $user->getLogin(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
            ],
        ]);
    }
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
