<?php



namespace Drupal\annonce\EventSubscriber;



use Drupal\Component\Datetime\Time;

use Drupal\Core\Database\Connection;

use Drupal\Core\Messenger\MessengerInterface;

use Drupal\Core\Routing\RouteMatchInterface;

use Drupal\Core\Session\AccountProxyInterface;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\HttpKernel\KernelEvents;



class DisplayThingsSubscriber implements EventSubscriberInterface {



  protected $messenger;

  protected $currentUser;

  protected $currentRouteMatch;

  protected $database;

  protected $time;



  public function __construct(MessengerInterface $messenger, AccountProxyInterface $currentUser,

                              RouteMatchInterface $currentRouteMatch, Connection $database, Time $time) {

    $this->messenger = $messenger;

    $this->currentUser = $currentUser;

    $this->currentRouteMatch = $currentRouteMatch;

    $this->database = $database;

    $this->time = $time;

  }



  public static function getSubscribedEvents() {

    $events[KernelEvents::REQUEST][] = ['called'];

    return $events;

  }



  public function called() {

    if ($this->currentRouteMatch->getRouteName()=='entity.annonce.canonical') {

       $this->messenger->addMessage(t('Entity Annonce'));
      // $this->messenger->addMessage(t('Event for %username', array('%username' => $this->currentUser->getAccountName())));

      $this->database->insert('annonce_history')->fields(array(

        'aid' => $this->currentRouteMatch->getParameter('annonce')->id(),

        'uid' => $this->currentUser->id(),

        'date' => $this->time->getCurrentTime(),

      ))->execute();

    }

  }



}
