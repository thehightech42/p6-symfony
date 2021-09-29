<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Token;
use App\Form\RegistrationType;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/security", name="security")
     */
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    /**
     * @Route("/inscription", name="security_registration")
     */

    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, MailerInterface $mailer){


        $user = new User;

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid() ) {
            $hash = $encoder->encodePassword($user, $user->getPassword()); 
            $user->setPassword($hash); 
            $user->setConfirmEmail(false);

            $manager->persist($user);
            
            $token = new Token($user);
            $manager->persist($token);
            // var_dump($token);

            $manager->flush();

            $email = (new TemplatedEmail())
                        ->from('no-reply@p6-symfony.numeriquesimple.fr')
                        ->to( $user->getEmail() )
                        ->subject('Confirmation Email')
                        ->htmlTemplate('security/email/email_signIn.html.twig')
                        ->context([
                            'urlConfirmEmailWithToken'=> $this->generateUrl("security_confirmEmail", ['hash' => $token->getHash()], true),
                            'user'=>$user
                        ]);

            $mailer->send($email);

            return $this->redirectToRoute('security_login');
       
        }
        return $this->render('security/registration.html.twig', [
            'form'=>$form->createView()
        ]);

    }


    /**
     * @Route("/mon-compte", name="security_myaccount")
     */
    public function myaccount(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();

        //Form user information
        $formUser = $this->createFormBuilder($user)
                        ->add('username', TextType::class)
                        ->add('email', EmailType::class)
                        ->add('save', SubmitType::class)
                        ->getForm(); 

        $formUser->handleRequest($request);
        
        if($formUser->isSubmitted() && $formUser->isValid() ){
            $manager->persist($user); 
            $manager->flush();

        }

        //Form update user password
        $formPassword = $this->createFormBuilder($user)
                        ->add('username', HiddenType::class)
                        ->add('email', HiddenType::class)
                        ->add('oldPassword', PasswordType::class)
                        ->add('newPassword', RepeatedType::class, [
                            'type'=>PasswordType::class,
                            'invalid_message'=> "Les mots de saisis ne sont pas les mêmes", 
                            'options'=>['attr' => ['class'=>'password-field']],
                            'required'=>true,
                            'first_options'=>['label'=>'Mot de passe', 'attr' => ['placeholder'=>'Votre mot de passe']],
                            'second_options'=>['label'=>'Confirmation Mot de passe', 'attr' => ['placeholder'=>'Confirmer votre mot de passe']]
                        ])
                        ->add('save', SubmitType::class)
                        ->getForm(); 

        $formPassword->handleRequest($request);
        $oldPasswordCheck = true;

        if($formPassword->isSubmitted() && $formPassword->isValid()){
            var_dump($user);
            if($encoder->isPasswordValid($user, $user->oldPassword)){
                $newPasswordHash = $encoder->encodePassword($user, $user->newPassword);
                $user->setPassword($newPasswordHash); 

                $manager->persist($user);
                $manager->flush();
            }else{
                var_dump('false oldPassword');
                //$oldPasswordCheck = false;
                $formPassword->get('oldPassword')->addError(new FormError('Votre ancien mot de passe ne correspond pas'));
            }
        }


        return $this->render('security/myaccount.html.twig', [
            'formUser'=>$formUser->createView(), 
            'formPassword'=>$formPassword->createView(),
            'oldPasswordCheck'=>$oldPasswordCheck
            ] 
        );
    }


    /**
     * @Route("/connexion", name = "security_login")
     */
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout(){}

    

    /**
     * @Route("/security/confirmEmail/{hash}", methods={"GET"}, name="security_confirmEmail")
     */
    public function confirmEmail(Token $token, EntityManagerInterface $manager)
    {
        $user = $token->getUserObjInToken();

        $user->setConfirmEmail(true); 

        $manager->persist($user);

        //We delete the token in BDD
        $manager->remove($token);
        $manager->flush();


        return $this->redirectToRoute('home');
    }


    
    /**
     * Use for try to send Email
     * @Route("/email", name="security_email")
     */
    public function email(MailerInterface $mailer)
    {
        $email = (new TemplatedEmail())
                    ->from("no-reply@p6-symfony.numeriquesimple.fr")
                    ->to("antonin.pfistner@gmail.com")
                    ->subject("Test Email")
                    // ->text("Envoyer un mail c'est cool !")
                    // ->html("<h1>Titre H1 test</h1><br><p>Je suis une paragraphe</p>");
                    ->htmlTemplate('security/email/email_signIn.html.twig')
                    ->context([
                        'username'=>'Antonin'
                    ]);
        $mailer->send($email);

        return $this->redirectToRoute('home');
    }

}