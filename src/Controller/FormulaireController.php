<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Poule;
use App\Entity\Tableau;
use App\Entity\Terrain;
use App\Entity\Tournoi;
use App\Entity\Utilisateur;
use App\Form\EquipeForm;
use App\Form\PouleForm;
use App\Form\TableauForm;
use App\Form\TournoiForm;
use App\Form\UtilisateurForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
#[Route(path: '/form', name: 'form')]
final class FormulaireController extends AbstractController
{
    #[Route(path: '/adduser', name: '_adduser')]
    public function userAddAction(
        EntityManagerInterface $em,
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurForm::class, $user);
        $form->add('submit', SubmitType::class,['label'=>'Add User']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_JOUEUR']);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_main1');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Le formulaire est invalide.');
        }

        return $this->render('form/form.html.twig', [
            'AddUserForm' => $form->createView(),
        ]);
    }
    #[Route(path: '/addtournoi', name: '_addtournoi')]
    public function addTournoiAction(EntityManagerInterface $em,Request $request): Response
    {
        $tournoi = new Tournoi();
        $form = $this->createForm(TournoiForm::class, $tournoi);
        $form->add('submit', SubmitType::class,['label'=>'Add Tournoi']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tournoi);
            $em->flush();
            $nbStade = $tournoi->getNbStade();
            for ($i=0;$i<$nbStade;$i++){
                $terrain=new Terrain();
                $terrain->setTournoi($tournoi);
                $terrain->setEstOccupé(false);
                $terrain->setNumero($i+1);
                $em->persist($terrain);

            }
            $em->flush();
            return $this->redirectToRoute('form_addtableau');


        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Le formulaire est invalide.');

        }

        return $this->render('form/form.html.twig', [
            'AddUserForm' => $form->createView(),
        ]);
    }
    #[Route(path: '/addtableau', name: '_addtableau')]
    public function addtableauAction(EntityManagerInterface $entityManager,Request $request): Response
    {
        $tableau = new Tableau();
        $form = $this->createForm(TableauForm::class, $tableau);
        $form->add('submit', SubmitType::class,['label'=>'Add Tableau']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tableau);
            $entityManager->flush();

        }



        return $this->render('form/formTableau.html.twig', [
            'AddUserForm' => $form->createView(),
        ]);
    }
    #[Route(path: '/addequipe', name: '_addequipe')]
    public function addEquipeAction(EntityManagerInterface $entityManager,Request $request): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeForm::class, $equipe);
        $form->add('submit', SubmitType::class,['label'=>'Add Equipe']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $equipe->setNom($equipe->getJoueurs()->get(0)->getNom(). ' '.$equipe->getJoueurs()->get(1)->getNom());
            $entityManager->persist($equipe);
            $entityManager->flush();

        }
        return $this->render('form/form.html.twig', [
            'AddUserForm' => $form->createView(),
        ]);
    }
    #[Route(path: '/addpoule' ,name: '_addpoule')]
    public function addPouleAction(EntityManagerInterface $entityManager,Request $request): Response{
        $poule= new Poule();
        $form=$this->createForm(PouleForm::class,$poule);
        $form->add('submit', SubmitType::class,['label'=>'Add Poule']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($poule);
            $entityManager->flush();
        }

        return $this->render('form/form.html.twig',[
            'AddUserForm'=>$form->createView(),
        ]);
    }



}
