<?php

/**
 * @file
 * CVC - Module
 */
    namespace Drupal\cvc_login\EventSubscriber;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpKernel\Event\GetResponseEvent;
    use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Event subscriber subscribing to KernelEvents::REQUEST.
 */
class RedirectAnonymousSubscriber implements EventSubscriberInterface
{

    /**
     * Implements event check.
     */
    public function checkAuthStatus(GetResponseEvent $event) 
    {

        global $base_url;

        if (\Drupal::currentUser()->isAnonymous()
            && \Drupal::routeMatch()->getRouteName() != 'user.login'
            && \Drupal::routeMatch()->getRouteName() != 'user.reset'
            && \Drupal::routeMatch()->getRouteName() != 'user.reset.form'
            && \Drupal::routeMatch()->getRouteName() != 'user.reset.login'
            && \Drupal::routeMatch()->getRouteName() != 'user.pass' 
        ) {

            // add logic to check other routes you want available to anonymous users,
            // otherwise, redirect to login page.

            $route_name = \Drupal::routeMatch()->getRouteName();
            if (strpos($route_name, 'view') === 0 
                && strpos($route_name, 'rest_') !== false
            ) {
                return;
            }
            $response = new RedirectResponse($base_url . '/user/login', 301);
            $event->setResponse($response);
            $event->stopPropagation();
            return;
        }
    }

    /**
     * Gets subscriber event.
     */
    public static function getSubscribedEvents() 
    {
        $events[KernelEvents::REQUEST][] = array('checkAuthStatus');
        return $events;
    }
}
