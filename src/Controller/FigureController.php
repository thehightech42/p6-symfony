<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\GroupeFigure;
use App\Entity\VisuelFigure;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\VisuelFigureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
                     ->add('content', TextareaType::class)
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

            // Gestion de l'image principale
            if(isset($_POST['form']['mainVisuel2'])){
                $int = (int) $_POST['form']['mainVisuel2'];
                $newMainVisuel = $repoVisuelFigure->find($int);
                $figure->setMainVisuel($newMainVisuel);
            }
            $manager->persist($figure);

            //Gestions ajout des images  
            for($f = 1; $f <= count($_FILES); $f++){
                $name = "inputGroupFile".strval($f);
                $pathinfos = pathinfo($_FILES[$name]['tmp_name']);

                $extension = explode('.', $_FILES[$name]['name']);

                $baseName = 'uploads/img/'.md5(uniqid()).'.'.end($extension);
                $newFileName = str_replace('\\', '/', $this->getParameter('upload_directory').'/'.$baseName);
                // var_dump($newFileName);
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

                // var_dump($newImg);
                var_dump($figure->getVisuelFigures());
                // var_dump($f);
                // var_dump($figure); 

                if($f === 1 && $figure->getVisuelFigures()->isEmpty() ){
                // if($f === 1 ){
                    var_dump('test');
                    $figure->setMainVisuel($newImg);
                }
                // var_dump($figure);
                // $manager->persist($newImg);
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
    public function figure(Figure $figure): Response
    {
        dump($figure);
        return $this->render('figure/index.html.twig', [
            'controller_name' => 'FigureController',
            'figure'=>$figure
        ]);
    }
}
