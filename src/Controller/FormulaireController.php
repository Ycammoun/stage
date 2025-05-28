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
    #[Route(path: '/api/adduser', name: 'api_adduser', methods: ['POST'])]
    public function apiUserAdd(
        EntityManagerInterface $em,
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Données JSON invalides'], 400);
        }

        // Validation des champs obligatoires
        if (empty($data['mail']) || empty($data['password']) || empty($data['login'])) {
            return new JsonResponse(['error' => 'mail, login et password sont requis.'], 400);
        }

        // Validation champ sexe (optionnel, mais si présent, doit être dans la liste)
        $validSexes = ['Homme', 'Femme', 'Autre', 'Non renseigné'];
        $sexe = $data['sexe'] ?? null;
        if ($sexe !== null && !in_array($sexe, $validSexes, true)) {
            return new JsonResponse(['error' => 'Valeur de sexe invalide'], 400);
        }

        $user = new Utilisateur();
        $user->setLogin($data['login']);
        $user->setNom($data['nom'] ?? null);
        $user->setPrenom($data['prenom'] ?? null);
        $user->setDateNaissance(!empty($data['dateNaissance']) ? new \DateTime($data['dateNaissance']) : null);
        $user->setMail($data['mail']);
        $user->setNumero($data['numero'] ?? null);
        $user->setCodepostale($data['codepostale'] ?? null);
        $user->setSexe($sexe);

        // Hash du mot de passe
        $user->setPassword(
            $passwordHasher->hashPassword($user, $data['password'])
        );

        $user->setRoles(['ROLE_JOUEUR']);

        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'Utilisateur ajouté avec succès !'], 201);
    }
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
    public function addTournoiAction(EntityManagerInterface $em, Request $request): Response
    {
        $tournoi = new Tournoi();
        $form = $this->createForm(TournoiForm::class, $tournoi);
        $form->add('submit', SubmitType::class, ['label' => 'Add Tournoi']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tournoi);
            $em->flush();

            // Création des terrains
            $nbStade = $tournoi->getNbStade();
            for ($i = 0; $i < $nbStade; $i++) {
                $terrain = new Terrain();
                $terrain->setTournoi($tournoi);
                $terrain->setEstOccupé(false);  // attention au nom méthode, sans accent ici !
                $terrain->setNumero($i + 1);
                $em->persist($terrain);
            }
            $em->flush();

            // Ici, au lieu de rediriger, on continue et affiche le tournoi
            // Le formulaire est vide ou tu peux choisir de ne plus l'afficher après la soumission.
            return $this->render('form/form.html.twig', [
                'addTournoiForm' => $form->createView(),
                'tournoi' => $tournoi,
                'showDetails' => true, // optionnel, pour dire au twig d’afficher ou non le formulaire
            ]);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Le formulaire est invalide.');
        }

        return $this->render('form/form.html.twig', [
            'addTournoiForm' => $form->createView(),
            'tournoi' => null,  // Pas encore créé
            'showDetails' => false,
        ]);
    }

    #[Route(path: '/addtableau/{tournoiId}', name: '_addtableau')]
    public function addtableauAction(
        EntityManagerInterface $entityManager,
        Request $request,
        int $tournoiId
    ): Response {
        // Récupérer le tournoi par son ID
        $tournoi = $entityManager->getRepository(Tournoi::class)->find($tournoiId);
        if (!$tournoi) {
            throw $this->createNotFoundException('Tournoi non trouvé');
        }

        $tableau = new Tableau();
        $tableau->setTournoi($tournoi); // Lier le tableau au tournoi

        $form = $this->createForm(TableauForm::class, $tableau);
        $form->add('submit', SubmitType::class, ['label' => 'Add Tableau']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tableau);
            $entityManager->flush();

            // Optionnel : tu peux rediriger vers la même page pour éviter la soumission multiple
            return $this->redirectToRoute('_addtableau', ['tournoiId' => $tournoiId]);
        }

        return $this->render('form/form.html.twig', [
            'addTabForm' => $form->createView(),
            'tournoi' => $tournoi,
            'showDetails' => true,
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
        return $this->render('form/formEquipe.html.twig', [
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

        return $this->render('form/formpoule.html.twig',[
            'AddUserForm'=>$form->createView(),
        ]);
    }
    #[Route(path: '/gestiontournoi', name: 'gestiontournoi')]
    public function gestionTournoi(Request $request, EntityManagerInterface $em): Response
    {
        // Création d’un nouveau tournoi
        $nouveauTournoi = new Tournoi();
        $formTournoi = $this->createForm(TournoiForm::class, $nouveauTournoi);
        $formTournoi->handleRequest($request);

        if ($formTournoi->isSubmitted() && $formTournoi->isValid()) {
            $em->persist($nouveauTournoi);

            $nbStade = $nouveauTournoi->getNbStade();
            for ($i = 0; $i < $nbStade; $i++) {
                $terrain = new Terrain();
                $terrain->setTournoi($nouveauTournoi);
                $terrain->setEstOccupé(false);  // attention au nom méthode, sans accent ici !
                $terrain->setNumero($i + 1);
                $em->persist($terrain);
            }
            $em->flush();
            return $this->redirectToRoute('formgestiontournoi', ['tournoiId' => $nouveauTournoi->getId()]);
        }

        // Récupération du tournoi sélectionné
        $tournoiId = $request->query->get('tournoiId');
        $tournoi = $tournoiId ? $em->getRepository(Tournoi::class)->find($tournoiId) : null;

        // Formulaire d’ajout de tableau
        $tableau = new Tableau();
        $formTableau = $this->createForm(TableauForm::class, $tableau);
        $formTableau->handleRequest($request);

        if ($tournoi && $formTableau->isSubmitted() && $formTableau->isValid()) {
            $tableau->setTournoi($tournoi);
            $em->persist($tableau);
            $em->flush();
            return $this->redirectToRoute('formgestiontournoi', ['tournoiId' => $tournoi->getId()]);
        }

        // Formulaires d’ajout de poule (un par tableau)
        $pouleForms = [];

        if ($tournoi) {
            foreach ($tournoi->getTableaux() as $tableauItem) {
                $poule = new Poule();
                $form = $this->createForm(PouleForm::class, $poule, [
                    'action' => $this->generateUrl('formgestiontournoi', [
                        'tournoiId' => $tournoi->getId(),
                        'tableauId' => $tableauItem->getId(),
                    ])
                ]);

                // Si on est sur le tableau ciblé dans la requête (soumission)
                if ($request->query->get('tableauId') == $tableauItem->getId()) {
                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        $poule->setTableau($tableauItem);
                        $em->persist($poule);
                        $em->flush();
                        return $this->redirectToRoute('formgestiontournoi', ['tournoiId' => $tournoi->getId()]);
                    }
                }

                $pouleForms[$tableauItem->getId()] = $form->createView();
            }
        }

        return $this->render('form/gestion.html.twig', [
            'formTournoi' => $formTournoi->createView(),
            'formTableau' => $formTableau->createView(),
            'formPoule' => $pouleForms,
            'tournoi' => $tournoi,
        ]);
    }







}
