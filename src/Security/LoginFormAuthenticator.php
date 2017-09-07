<?php
/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2017 Cerema — Alexandre Tranchant
 * @license   Propriétaire Cerema
 */

namespace App\Security;

use App\Form\LoginForm;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

/**
 * LoginFormAuthenticator class.
 *
 * @category Security
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 * @see https://knpuniversity.com/screencast/symfony-security/authenticator-get-user-check-credentials
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * Form factory interface.
     * If you want to know which class is your form factory, just type:
     * $ php ./bin/console debug:container form.factory.
     *
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * Entity Manager Interface.
     *
     * If you want to know which class is your entity manager, just type:
     * $ php ./bin/console debug:container entity_manager and select yours.
     *
     * @var EntityManager
     */
    private $em;

    /**
     * User password encoder.
     *
     * @var UserPasswordEncoder
     */
    private $passwordEncoder;

    /**
     * Router Interface.
     * If you want to know which class is your password encoder, just type:
     * php ./bin/console debug:container password_encoder.
     *
     * @var RouterInterface
     */
    private $router;

    /**
     * LoginFormAuthenticator constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param EntityManager        $em
     * @param RouterInterface      $router
     * @param UserPasswordEncoder  $passwordEncoder
     */
    public function __construct(FormFactoryInterface $formFactory, EntityManager $em, RouterInterface $router, UserPasswordEncoder $passwordEncoder)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Check credentials.
     *
     * @param mixed         $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['password'];

        return $this->passwordEncoder->isPasswordValid($user, $password);
    }

    /**
     * Provide credentials.
     *
     * @param Request $request
     *
     * @return mixed|null
     */
    public function getCredentials(Request $request)
    {
        $isLoginSubmit = $request->getPathInfo() == '/login' && $request->isMethod('POST');
        if (!$isLoginSubmit) {
            // skip authentication
            return null;
        }

        //We create the form and handle the request
        $form = $this->formFactory->create(LoginForm::class);
        //Magic !!!!
        $form->handleRequest($request);

        //Store the identifiant to push it in the login form when credential errors occured..
        $data = $form->getData();
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data['mail']
        );

        return $data;
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate('homepage');
    }

    /**
     * Generate the Login URL in router.
     *
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->router->generate('security_login');
    }

    /**
     * Get User.
     *
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return null|object
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['mail'];

        return $this->em
            ->getRepository('App:User')
            ->findOneBy(['mail' => $username]);
    }
}
