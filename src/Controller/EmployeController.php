<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmployeController extends AbstractController
{
    /**
     * @Route("/employe", name="app_employe")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $employes = $doctrine->getRepository(Employe::class)->findAll();

        return $this->render('employe/index.html.twig', [
            'controller_name' => 'EmployeController',
            'employes'=> $employes,
        ]);
    }

    /**
     * @Route("/employe/add", name="add_employe")
     * @Route("/employe/update/{id}", name="update_employe")
     */
    public function add(ManagerRegistry $doctrine, Employe $employe = null, Request $request): Response
    {
        if(!$employe){
            $employe = new Employe(); 
         }
        $entityManager = $doctrine->getManager();
        // Accède à des methodes qui permette de modifier en base de donnée
        $form = $this->createForm(EmployeType::class, $employe);
        // Crée un formulaire avec les input de EntrepriseType, createform() demande toujours l'objet qu'on veux crée à partir du forumlaire
        $form->handleRequest($request);
        // On demande l'analyse de la requète du formulaire

        if($form->isSubmitted() && $form->isValid()){
            $employe = $form->getData(); // Récupere les données saisie dans le formulaire
            $entityManager->persist($employe); // Crée l'objet employé
            $entityManager->flush(); // Insere l'élément en base de donnée

            return $this->redirectToRoute('app_employe'); // Une fois insérer retourne sur la liste des employées
        }
        return $this->render('employe/add.html.twig', [
            'controller_name' => 'EmployeController',
            'formEmploye'=>$form->createView()
           
        ]);
    }

    /**
     * @Route("/employe/delete{id}", name="delete_employe")
     */
    public function delete(ManagerRegistry $doctrine, Employe $employe){
        $entityManager = $doctrine->getManager();
        $entityManager->remove($employe);
        $entityManager->flush();
        return $this->redirectToRoute("app_employe");
    }

    /**
     * @Route("/employe/{id}", name="show_employe")
     */
    public function show(Employe $employe) : Response
    {
        return $this->render('employe/show.html.twig', [
            'controller_name' => 'EmployeController',
            'employe'=>$employe,
        ]);
        
    }
}
