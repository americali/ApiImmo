<?php

namespace App\Controller;

use App\Entity\Bien;
use App\Form\BienFormType;
use App\Repository\BienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class BienController extends AbstractController {

    /**
     * @Route("/bien/ajouter", name="bienAjouter")
     */
    public function bienAjouter(EntityManagerInterface $em, Request $request) {
        $bien = new Bien();
        // 2. Création du formulaire du type souhaité
        $form = $this->createForm(BienFormType::class, $bien);

        // 3. Analyse de l'objet Request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            //$file = $bien->getImagePrincipale();
            $file = $form['photo']->getData();
            $fileName = md5(uniqid()). '.' . $file->guessExtension();

            // Move the file to the directory 
            try {
                $file->move(
                        $this->getParameter('kernel.project_dir') . '/public/images/biens');
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
            $bien->setPhoto($fileName);

            // Remplissage de l'entité avec les données du formulaire
            //$bien = $form->getData();
            //dump($bien); die();
            $em->persist($bien);
            $em->flush();
            $this->addFlash('success', "Bien est ajouté");
            // Rendu d'une vue où on affiche les données
            // Normalement on faire CRUD ici ou une autre opération...
            return $this->redirectToRoute('home');
        }
        // si non, on doit juste faire le rendu du formulaire
        else {
            return $this->render('bien/new_bien.html.twig', ['bienForm' => $form->createView(),
                        'controller_name' => 'BienController',]);
        }
    }

    /**
     * @Route("/edit/{id}/bien", name="edit_bien")
     */
    public function editBien(Bien $bien, Request $request, EntityManagerInterface $em) {

        $form = $this->createForm(BienFormType::class, $bien);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form['photo']->getData();

            if ($file) {
                $destination = $this->getParameter('kernel.project_dir') . '/public/images/biens';
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFileName = md5(uniqid()). '.' . $file->guessExtension();
                $file->move(
                        $destination, $newFileName
                );
                $bien->setPhoto($newFileName);
            }
            $em->persist($bien);
            $em->flush();

            $this->addFlash('success', 'Bien est mis à jour');
            return $this->redirectToRoute('home');
        }
        return $this->render('bien/edit_bien.html.twig', [
                    'bienForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/bien/details/{id}", name="detail_bien")
     */
    public function details($id, BienRepository $bienRepository, Environment $twigEnvironment, Request $request) {

        $bien = $bienRepository->find($id);
        //find exemplaire with id

        dump($bien);
        return $this->render('bien/detail_bien.html.twig', [
                    'bien' => $bien,
                    
        ]);
    }


}
