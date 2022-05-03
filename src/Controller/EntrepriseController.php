<?php

namespace App\Controller;

use App\Entity\Entreprise;


use App\Form\EntrepriseType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntrepriseController extends AbstractController
{
    /**
     * @Route("/entreprise", name="app_entreprise") 
     */
    public function index(ManagerRegistry $doctrine): Response // ManagerRegistry permet de récupéré les donnée dans le dossier Repository
    {
        $entreprises = $doctrine->getRepository(Entreprise::class)->findAll();
        // On récupère l'endroit ou se trouves les requetes SQL de la class Entreprise (Récupere un tableau d'objet entreprise TOUTES les entreprise)
        return $this->render('entreprise/index.html.twig', [
            'controller_name' => 'EntrepriseController',
            'entreprises'=>$entreprises,
        ]);
    } // /!\ Envoyer l'information à la vue des entreprises sinon la vu et la requète ne sont pas liées

    /**
     * @Route("/entreprise/add", name="add_entreprise")
     * @Route("/entreprise/update/{id}", name="update_entreprise")
     */
    public function add(ManagerRegistry $doctrine, Entreprise $entreprise = null, Request $request): Response
    {
        if(!$entreprise){
           $entreprise = new Entreprise(); 
        }

        $entityManager = $doctrine->getManager();
        // Accède à des methodes qui permette de modifier en base de donnée
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        // Crée un formulaire avec les input de EntrepriseType, createform() demande toujours l'objet qu'on veux crée à partir du forumlaire
        $form->handleRequest($request);
        // On demande l'analyse de la requète du formulaire

        if($form->isSubmitted() && $form->isValid()){
            $entreprise = $form->getData(); // Récupere les données saisie dans le formulaire
            $entityManager->persist($entreprise); // Crée l'objet entreprise
            $entityManager->flush(); // Insere l'élément en base de donnée

            return $this->redirectToRoute('app_entreprise'); // Une fois insérer retourne sur la liste des entreprises
        }
        return $this->render('entreprise/add.html.twig', [
            'controller_name' => 'EntrepriseController',
            'formEntreprise'=>$form->createView()
           
        ]);
    }
        /**
     * @Route("/entreprise/delete{id}", name="delete_entreprise")
     */
    public function delete(ManagerRegistry $doctrine, Entreprise $entreprise){
        $entityManager = $doctrine->getManager();
        $entityManager->remove($entreprise);
        $entityManager->flush();
        return $this->redirectToRoute("app_entreprise");
    }

    /**
     * @Route("/entreprise/{id}", name="show_entreprise")
     */
    public function show(Entreprise $entreprise) : Response
    {
        return $this->render('entreprise/show.html.twig', [
            'controller_name' => 'EntrepriseController',
            'entreprise'=>$entreprise,
        ]);
        
    }


}
