<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Token;
use App\Form\RegistrationType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Repository\TokenRepository;
use Symfony\Component\Mime\Address;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormBuilder;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
                            'urlConfirmEmailWithToken'=> $this->generateUrl("security_confirmEmail", ['hash' => $token->getHash()], UrlGeneratorInterface::ABSOLUTE_URL),
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
     * @Route("/changer-nom-utilisateur", name="security_updateUsername")
     */
    public function updateUsername(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();

        //Form user information
        $formUser = $this->createFormBuilder($user)
                        ->add('username', TextType::class)
                        ->add('email', EmailType::class, ['attr'=> ['readonly'=>'true']])
                        ->add('save', SubmitType::class)
                        ->getForm(); 

        $formUser->handleRequest($request);
        
        if($formUser->isSubmitted() && $formUser->isValid() ){
            $manager->persist($user); 
            $manager->flush();

        }

        return $this->render('security/updateUsername.html.twig', [
            'formUser'=>$formUser->createView()
            ] 
        );
    }


    /**
     * @Route("/changer-mot-de-passe", name="security_updatePassword")
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();

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
            // var_dump($user);
            if($encoder->isPasswordValid($user, $user->oldPassword)){
                $newPasswordHash = $encoder->encodePassword($user, $user->newPassword);
                $user->setPassword($newPasswordHash); 

                $manager->persist($user);
                $manager->flush();
            }else{
                // var_dump('false oldPassword');
                //$oldPasswordCheck = false;
                $formPassword->get('oldPassword')->addError(new FormError('Votre ancien mot de passe ne correspond pas'));
            }
        }

        return $this->render('security/updatePassword.html.twig', [
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
    public function confirmEmail(Token $token, EntityManagerInterface $manager, MailerInterface $mailer)
    {
        $user = $token->getUserObjInToken();

        $user->setConfirmEmail(true); 

        $manager->persist($user);
        $otherToken = new Token($user);


        //We delete the token in BDD
        $manager->remove($token);
        $manager->flush();

        $email = (new TemplatedEmail())
                    ->from('no-repley@p6-symfony.numeriquesimple.fr')
                    ->to( $user->getEmail())
                    ->subject('Bienvenue sur SNOWTRICKS !')
                    ->htmlTemplate('security/email/email_welcome.html.twig')
                    ->context([
                        'user'=>$user
                    ]);
            
        $mailer->send($email);

        return $this->redirectToRoute('security_login');
    }

    /**
     * @Route("/mot-de-passe-oublie", name="security_forgotPassword")
     */
    public function forgotPassword(Request $request, UserRepository $userRepo, TokenRepository $tokenRepo,  EntityManagerInterface $manager, MailerInterface $mailer)
    {
        if( $request->request->get('emailFogot') !== null ){

            $user = $userRepo->findBy(['email'=>$request->request->get('emailFogot')]);
            $user = $user[0];
            if($user->getConfirmEmail() === true){

                $tokenExist = $tokenRepo->findBy(['user'=>$user]);
                if(count($tokenExist) !== 0){
                    $oldToken = $tokenExist[0];
                    $this->removeToken($oldToken);
                }
                $token = new Token($user);
                $manager->persist($token); 
                $manager->flush();

                $email = ( new TemplatedEmail())
                            ->from('no-reply@p6-symfony.numeriquesimple.fr')
                            ->to($user->getEmail())
                            ->subject("Reintilisation de mot de passe")
                            ->htmlTemplate('/security/email/email_forgotPassword.html.twig')
                            ->context([
                                'user' => $user,
                                'urlWithTokenFogotPassword'=> $this->generateUrl('security_resetPassword', ['hash' => $token->getHash()], UrlGeneratorInterface::ABSOLUTE_URL)
                            ]); 
                $mailer->send($email);
            }
            else{
                var_dump('Reinitialisation impossible');
            }
        }
        return $this->render('security/forgotPassword.html.twig');

    }

    public function removeToken(Token $token)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($token);
        $manager->flush();
        return;
    }

    /**
     * @Route("/security/reinitialisation-de-mot-passe/{hash}", name="security_resetPassword")
     */
    public function resetPassword(Request $request, Token $token, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = $token->getUserObjInToken();

        $formResetPassword = $this->createFormBuilder($user)
                        ->add('username', HiddenType::class)
                        ->add('email', HiddenType::class)
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

        $formResetPassword->handleRequest($request);

        if($formResetPassword->isSubmitted() && $formResetPassword->isValid()){

            $newPasswordHash = $encoder->encodePassword($user, $user->newPassword);
            $user->setPassword($newPasswordHash); 

            $manager->persist($user);
            $manager->remove($token);
            $manager->flush();
            
           return $this->redirectToRoute('security_login');

        }

        return $this->render('security/resetPassword.html.twig', [
            'form'=>$formResetPassword->createView()
        ]);
        
    }


    /**
     * Use for try to send Email
     * @Route("/email", name="security_email")
     */
    public function email(MailerInterface $mailer)
    {
        $user = new User; 
        $user->setUsername('Antonin'); 
        $user->setEmail('contact@antoninpfistner.fr'); 

        $otherToken = new Token($user);

        $email = (new TemplatedEmail())
                    ->from('no-repley@p6-symfony.numeriquesimple.fr')
                    ->to( $user->getEmail())
                    ->subject('Bienvenue sur SNOWTRICKS !')
                    ->htmlTemplate('security/email/email_welcome.html.twig')
                    ->context([
                        'user'=>$user
                    ]);
            
        $mailer->send($email);

        return $this->redirectToRoute('home');
    }

}