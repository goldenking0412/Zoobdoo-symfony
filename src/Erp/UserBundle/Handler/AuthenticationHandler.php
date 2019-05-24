<?php
namespace Erp\UserBundle\Handler;

use Erp\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var SecurityContext
     */
    protected $security;

    /**
     * @param RouterInterface $router
     * @param SecurityContext $security
     */
    public function __construct(RouterInterface $router, SecurityContext $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    /**
     * Call when authentication is success
     *
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->security->getToken()->getUser();
        $url = 'erp_user_profile_dashboard';

        if ($user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_SUPER_ADMIN)) {
            $url = 'sonata_admin_dashboard';
        } elseif ($user->hasRole(User::ROLE_TENANT) || $user->hasRole(User::ROLE_MANAGER)) {
            if ($user->hasRole(User::ROLE_MANAGER)) {
                $url = 'erp_user_dashboard_dashboard';
            }

            if (!$user->getIsTermOfUse()) {
                $url = 'erp_user_term_of_use';
            }
        }

        return new RedirectResponse($this->router->generate($url));
    }
}
