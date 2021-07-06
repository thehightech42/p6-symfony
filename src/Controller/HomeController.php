<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/figure/ajax/figure", name="ajaxFigurePagination")
     */
    public function ajaxFigurePagination(FigureRepository $repo): Response
    {
        $limit = 8;
        $offset = $_POST['page'] * $limit ;

        $figures = $repo->findFigurePagination($limit, $offset);

        $nbFigure = count($figures);

        $endData = false;
        if($nbFigure < $limit ){
            $endData = true;
        }

        $jsonData = [];
        $figuresData = [];

        for($i = 0; $i < $nbFigure; $i++){
            $figure = $figures[$i];
            $groupe = $figure->getGroupe();

            $data = [
                'id'=>$figure->getId(),
                'title'=>$figure->getTitle(),
                'shortDescription'=>$figure->getShortDescription(),
                // 'content'=>$figure->getContent(),
                // 'path'=> $this->generateUrl('figure', ['id'=>$figure->getId()]),
                'path' => "figure/".$figure->getSlug(),
                'date'=>$figure->getCreateAt(),
                'titleGroupe'=>$groupe->getTitle()
            ];

            // $mainVisuel = $figure->getMainVisuel();
            // if($mainVisuel != null){
            //     $data['mainVisuel'] = $mainVisuel->getUrl();
            // }

            array_push($figuresData, $data);
        }
        $jsonData['figures'] = $figuresData; 
        $jsonData['endData'] = $endData; 

        return new JsonResponse($jsonData);
    }

}
