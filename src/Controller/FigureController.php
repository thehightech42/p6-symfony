<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Comment;
use App\Entity\GroupeFigure;
use App\Entity\VisuelFigure;
use App\Service\GestionFile;
use App\Form\ControlFigureType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\VisuelFigureRepository;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class FigureController extends AbstractController
{
    /**
     * @Route("/figure/newFigure", name="newFigure");
     * @Route("/figure/edit/{id}", name="editFigure") 
     */
    public function controlFigure(Figure $figure = null, Request $request, EntityManagerInterface $manager, VisuelFigureRepository $repoVisuelFigure): Response
    {
        // On initialiser une entité figure si c'est une nouvelle.
        if(!$figure){
            $figure = new Figure;
        }
        // On crée le formulaire
        $formFigure = $this->createForm(ControlFigureType::class, $figure);
        // On analyse la requette
        $formFigure->handleRequest($request);
        // Si le form est soumis et valide
        if($formFigure->isSubmitted() && $formFigure->isValid()){

            // Gestion des dates d'ajout et de mise à jour
            if(!$figure->getId()){
                $figure->setCreateAt(new \DateTime); // On ajoute la date de création
                $figure->setUpdateAt(new \DateTime); // On ajoute la date de mise à jours
            }else{
                $figure->setUpdateAt(new \DateTime); // On change la date de mise à jours si c'est un update
            }

            // Gestion de l'image principale en cas d'update
            $formMainVisuel = $formFigure->get('mainVisuelSelect')->getData();
            if( $formMainVisuel !== false ){
                $figure->setMainVisuel($repoVisuelFigure->find(intval($formMainVisuel)));
            }

            // On fait persiter la figure
            $manager->persist($figure);

            // Analyse du champ picturesVisuel
            $picturesVisuel = $formFigure->get('picturesVisuel')->getData();
            if($picturesVisuel !== "null"){ // Si c'est différent de nul
                foreach($picturesVisuel as $picture){ // On crée une boucle sur les données 
                    $pictureVisuel = new VisuelFigure; // Je crée une nouvelle entité VisuelFigure 
                    $pictureVisuel->typeAndMove('picture', $picture, $this->getParameter('upload_directory'))->setFigure($figure); // Justilise la méthode interné crée
                    $manager->persist($pictureVisuel); // On fait persiter 
                    if($figure->getMainVisuel() == null && $picture == $picturesVisuel[0]){ // Si c'est la première image
                        $figure->setMainVisuel($pictureVisuel); // On la défini comme principale
                    }
                }
            }

            // Analyse du champ vidéo
            $videos = $formFigure->get('videosVisuels')->getData();
            $regex = '/https:\/\/(?:youtu\.com\/|(?:[a-z]{2,3}\.)?youtube\.com\/watch(?:\?|#\!)v=)([\w-]{11}).*/';
            if (preg_match_all($regex, $videos, $arrayListeVideo, PREG_PATTERN_ORDER)) {
                $videoArray = [];
                foreach($arrayListeVideo[0] as $element){
                    $videosExplode = explode(" ", $element);
                    if(count($videosExplode) > 1 ){
                        foreach($videosExplode as $aloneVideo){
                            array_push($videoArray, $aloneVideo);
                        }
                    }else{
                        array_push($videoArray, $element);
                    }
                }
                if(count($videoArray) > 0){
                    foreach($videoArray as $url){
                        $video = new VisuelFigure;
                        $video->typeAndMove('video', $url)->setFigure($figure);
                        $manager->persist($video);
                    }

                }
            }


            // On flush
            $manager->flush();
            return $this->redirectToRoute('figure', ['slug'=>$figure->getSlug()] );
        }
        return $this->render('figure/controlFigure.html.twig', [
            'formFigure'=>$formFigure->createView(), 
            'figure'=>$figure
        ]);

    }
    
    /**
     * @Route("/figure/ajax/removeVisuelPicture", name="removeAjaxPicture", methods={"DELETE"})
     */
    public function deleteAjaxVisuelFigure(Request $request, EntityManagerInterface $manager, VisuelFigureRepository $repoVisuelFigure)
    {
        //On décode les données
        $data = json_decode($request->getContent(), true);

            if($this->isCsrfTokenValid('deleteVisuelFigure' . $data['_idVisuelFigure'], $data['_token'])){

                // On cherche l'entité visuelFigure
                $visuelFigureArray = $repoVisuelFigure->findBy(['id'=>$data['_idVisuelFigure']]);
                $visuelFigure = $visuelFigureArray[0];
                // Si c'est une image on la supprime du serveur
                if($visuelFigure->getType() === "picture"){
                    if($visuelFigure->getFigure()->getMainVisuel() == $visuelFigure){
                        $return['isMain'] = true;
                        foreach($visuelFigure->getFigure()->getVisuelFigures() as $visuelFigureInCollection){
                            if($visuelFigureInCollection !== $visuelFigure && $visuelFigureInCollection->getType() == "picture"){
                                $visuelFigure->getFigure()->setMainVisuel($visuelFigureInCollection);
                                $return['newMain'] = $visuelFigureInCollection->getId();
                            }
                        }
                    } 
                    unlink($visuelFigureArray[0]->getUrl());
                }

                // On remove dans la BDD
                $manager->remove($visuelFigureArray[0]);

                $manager->flush();
                // Reponse AJAX
                $return['success'] = true;
                return new JsonResponse($return);
            }else{
                // Reponse AJAX
                $return['success'] = false;
                return new JsonResponse($return);
            }
       
        return new JsonResponse($return);
    }

    /**
     * @Route("/figure/{slug}", name="figure")
     */
    public function figure(Figure $figure, Request $request, EntityManagerInterface $manager): Response
    {
        if($request->request->get('comment') !== null && $this->isCsrfTokenValid('add-message', $request->request->get('_token'))){
            $comment = new Comment;
            
            $comment->setValue($request->request->get('comment'));
            $comment->setUser($this->getUser());
            $comment->setCreateAt(new \DateTime);
            $comment->setFigure($figure);

            $manager->persist($comment); 
            $manager->flush();
            $toast = ['icon'=>'success', 'heading'=>'Succés', 'text'=> 'Votre message a bien été pris en compte.', 'showHideTransition'=> 'fade',  'hideAfter'=>'3000'];
            return $this->render('figure/readFigure.html.twig', [
                'figure'=>$figure, 
                'toast'=>json_encode($toast)
            ]);
            
        }

        return $this->render('figure/readFigure.html.twig', [
            'figure'=>$figure
        ]);
    }

    /**
     * @Route("/figure/delete/{id}", name="deleteFigure")
     */
    public function deleteFigure(Figure $figure, EntityManagerInterface $manager): Response
    {
        foreach($figure->getVisuelFigures() as $visuelFigure){
            if($visuelFigure->getType() === "picture"){
                unlink($visuelFigure->getUrl());
            }
        }

        $manager->remove($figure);

        $manager->flush();
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/figure/ajax/addGroupeFigure", name="addGroupeFigure")
     */
    public function addGroupeFigure( Request $request, EntityManagerInterface $manager) 
    {
       $groupe = new GroupeFigure(); 
       $groupe->setTitle($request->get('title')); 
       $groupe->setDescription($request->get('description'));

       $manager->persist($groupe);
       $manager->flush();
        
       $jsonData['id'] = $groupe->getId();  

       return new JsonResponse($jsonData);
    }

    /**
     * @Route("figure/ajax/comment", name="ajaxCommentPagination")
     */
    public function ajaxCommentPagination(Request $request, EntityManagerInterface $manager, FigureRepository $repoFigure, CommentRepository $repoComment ): Response
    {
        // On récupère le slug et la "page"
        $slugFigure = (string) $_POST['figure'];
        $blockElement = $_POST['blockElement'];

        //Je cherche la figure
        $figuresArray = $repoFigure->findBy(['slug'=>$slugFigure]);

        // var_dump($figuresArray[0]);
        $nbElementsGet = 8;
        $indexStart = 0 + ( $nbElementsGet * $blockElement );

        //On cherche l'ensemble des commentaires via une requette
        $comments = $repoComment->findByFigureIdPagination($figuresArray[0]->getId(), $nbElementsGet, $indexStart);

        $countElementsGet = count($comments);
        $endComments = false; 

        if($countElementsGet < $nbElementsGet){
            $endComments = true;
        }

        $jsonData = [];
        $jsonComments = [];

        for($i = 0; $i < count($comments); $i++){
            $comment = $comments[$i]; 
        
            $data = [
                'user'=>$comment->getUser()->getUsername(), 
                'value'=>$comment->getValue(),
                'created_at'=>$comment->getCreateAt()
            ];
            array_push($jsonComments, $data); 

        }

        if( count($jsonComments) > 0){
            $jsonData['comments'] = $jsonComments;
        }
        
        $jsonData['endData'] = $endComments;

        return new JsonResponse($jsonData);
    }
}
