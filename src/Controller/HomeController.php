<?php

namespace App\Controller;

use App\Entity\Bien;
use App\Repository\BienRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    /**
     * @Route("/", name="home")
     */
    public function home(BienRepository $bienRepository, Request $request) {

        $bien = new Bien();
        $allBiens = $bienRepository->findAll();

        return $this->render('home/index.html.twig', [
                    'allBiens' => $allBiens,
        ]);
    }
    
    //action Ajax pour la page home

    /**
     * @Route ("/ajax/form/data/", name="ajax-recherche-bien");
     */
    public function biensTraitementAjax(Request $requeteAjax) {

        $criteres = [];

        $criteres['prix'] = $requeteAjax->get('prix');
        $criteres['nombreDePieces'] = $requeteAjax->get('nombreDePieces');
       


        $biens = $this->getDoctrine()
                ->getManager()
                ->getRepository(Bien::class)
                ->findAllBiensByCriteres($criteres);

//        $allCostumes = $paginator->paginate(
//                $exemplaires, /* query NOT result */ $requeteAjax->query->getInt('page', 1)/* page number */, 12/* limit per page */
//        );

        $partialVue = $this->renderView("/partialVue/bienDiv.html.twig", [
            'allBiens' => $biens
        ]);
        $arrayReponse = ['partialVue' => $partialVue];
        return new JsonResponse($arrayReponse);
    }

}
