<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Comment;
use App\Entity\GroupeFigure;
use App\Entity\VisuelFigure;
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
     * @Route("/figure/newFigure", name="newFigure")
     * @Route("/figure/edit/{id}", name="editFigure")
     */
    public function newFigure(Figure $figure = null, Request $request, EntityManagerInterface $manager, VisuelFigureRepository $repoVisuelFigure): Response
    {
        if(!$figure ){
            $figure = new Figure;
        }
        $formFigure = $this->createFormBuilder($figure)
                     ->add('groupe', EntityType::class, array(
                        'class'=> GroupeFigure::class,
                        'choice_label'=> 'title')
                      )
                     ->add('title',TextType::class)
                     ->add('shortDescription',TextType::class)
                     ->add('content', TextareaType::class, ['required'=>false])
                     ->add('save', SubmitType::class)
                     ->add('mainVisuel2')
                     ->getForm();

        $formFigure->handleRequest($request);

        if($formFigure->isSubmitted() && $formFigure->isValid()){

            // Gestion des dates d'ajout et de mise à jour
            if(!$figure->getId()){
                $figure->setCreateAt(new \DateTime);
                $figure->setUpdateAt(new \DateTime); 
            }else{
                $figure->setUpdateAt(new \DateTime); 
            }

            $firstImg = true;
            // Gestion de l'image principale
            if(isset($_POST['form']['mainVisuel2'])){
                $int = (int) $_POST['form']['mainVisuel2'];
                $newMainVisuel = $repoVisuelFigure->find($int);
                $figure->setMainVisuel($newMainVisuel);
                $firstImg = false;
            }
            $manager->persist($figure);


            //Gestions ajout des images  
            for($f = 1; $f <= count($_FILES); $f++){
                $name = "inputGroupFile".strval($f);

                $extension = explode('.', $_FILES[$name]['name']);
                if($_FILES[$name]['size'] !== 0){

                    $baseName = 'uploads/img/'.md5(uniqid()).'.'.end($extension);
                    $newFileName = str_replace('\\', '/', $this->getParameter('upload_directory').'/'.$baseName);
    
                    try{
                        move_uploaded_file($_FILES[$name]['tmp_name'], $newFileName);
                    }catch (\Execption $e) {
                        var_dump('Error move');
                    }
    
                    $newImg = new VisuelFigure();
                    $newImg->setType('picture');
                    $newImg->setUrl($baseName);
                    $newImg->setFigure($figure);
    
                    $manager->persist($newImg);
    
                    if($firstImg === true ){
                        $firstImg = false;
                        $figure->setMainVisuel($newImg);
                    }
                }
                
            }
            

            // Gestion de l'ajout des vidéos
            $strAddVideo = 'inputVideoUrl';
            $i = 1;
            while( isset( $_POST[$strAddVideo.strval($i)] ) ){
                $newLink ="";
                $regex = '/https:\/\/(?:youtu\.com\/|(?:[a-z]{2,3}\.)?youtube\.com\/watch(?:\?|#\!)v=)([\w-]{11}).*/';
                if (preg_match($regex, $_POST[$strAddVideo.strval($i)], $matches)) {
                    $id = explode("=", $_POST[$strAddVideo.strval($i)]);
                    $newLink = "https://www.youtube.com/embed/".end($id);

                    $video = new VisuelFigure;
                    $video->setType('video');
                    $video->setUrl($newLink); 
                    $video->setFigure($figure); 
                    $manager->persist($video);
                }
                $i++;
            }

            // Suppression des images
            $strDeleteElement = 'deleteElement';
            $e = 1;
            while( isset($_POST[$strDeleteElement.strval($e)]) ){
                $idFigureToDelete = (int) $_POST[$strDeleteElement.strval($e)];
                $visuelElementDelete = $repoVisuelFigure->find($idFigureToDelete);
                if($visuelElementDelete->getType() === "picture"){
                    unlink($visuelElementDelete->getUrl());
                }
                $manager->remove($visuelElementDelete);
                $e++; 
            }
            // Faire sauver en BDD
            $manager->flush();
            return $this->redirectToRoute('figure', ['slug'=>$figure->getSlug()] );
        }

        return $this->render('figure/newFigure.html.twig', [
            'formFigure'=>$formFigure->createView(),
            'figure'=>$figure
        ]);
    }

    /**
     * @Route("/figure/{slug}", name="figure")
     */
    public function figure(Figure $figure, Request $request, EntityManagerInterface $manager): Response
    {
        if($request->request->get('comment') !== null){
            $comment = new Comment;
            
            $comment->setValue($request->request->get('comment'));
            $comment->setUser($this->getUser());
            $comment->setCreateAt(new \DateTime);
            $comment->setFigure($figure);

            $manager->persist($comment); 
            $manager->flush();
            $toast = ['icon'=>'success', 'heading'=>'Succés', 'text'=> 'Votre message a bien été pris en compte.', 'showHideTransition'=> 'fade',  'hideAfter'=>'3000'];
            return $this->render('figure/index.html.twig', [
                'figure'=>$figure, 
                'toast'=>json_encode($toast)
            ]);
        }
        return $this->render('figure/index.html.twig', [
            'figure'=>$figure, 
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
        // $figureUse = $figure[0];
        $nbElementsGet = 8;
        $indexStart = 0 + ( $nbElementsGet * $blockElement );

        //On cherche l'ensemble des commentaires via une requette
        // $comments = $figure->getComments();
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
