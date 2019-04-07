<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Card;
use AppBundle\Entity\Review;
use AppBundle\Entity\Reviewcomment;
use AppBundle\Entity\User;
use AppBundle\Entity\Errata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ErrataController extends Controller
{

    public function postAction (Request $request)
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();

        /* @var $user User */
        $user = $this->getUser();
        if(!$user) {
            throw $this->createAccessDeniedException("You are not logged in.");
        }

        // a user cannot post more erratas than her reputation
        if(count($user->getErratas()) >= $user->getReputation()) {
            throw new \Exception("Your reputation doesn't allow you to write more erratas.");
        }

        $card_id = filter_var($request->get('card_id'), FILTER_SANITIZE_NUMBER_INT);
        /* @var $card Card */
        $card = $em->getRepository('AppBundle:Card')->find($card_id);
        if(!$card) {
            throw new \Exception("This card does not exist.");
        }
        /*
          if(!$card->getPack()->getDateRelease())
          {
          throw new \Exception("You may not write a errata for an unreleased card.");
          }
         */
        // checking the user didn't already write a errata for that card
        $errata = $em->getRepository('AppBundle:Errata')->findOneBy(array('card' => $card, 'user' => $user));
        if($errata) {
            throw new \Exception("You cannot write more than 1 errata for a given card.");
        }

        $errata_raw = trim($request->get('errata'));

        $errata_raw = preg_replace(
            '%(?<!\()\b(?:(?:https?|ftp)://)(?:((?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?)(?:[^\s]*)?%iu', '[$1]($0)', $errata_raw);

        $errata_html = $this->get('texts')->markdown($errata_raw);
        if(!$errata_html) {
            throw new \Exception("Your errata is empty.");
        }

        $errata = new Errata();
        $errata->setCard($card);
        $errata->setUser($user);
        $errata->setTextMd($errata_raw);
        $errata->setTextHtml($errata_html);

        $em->persist($errata);

        $em->flush();

        return new JsonResponse([
            'success' => TRUE
        ]);
    }

    public function editAction (Request $request)
    {

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();

        /* @var $user User */
        $user = $this->getUser();
        if(!$user) {
            throw new UnauthorizedHttpException("You are not logged in.");
        }

        $errata_id = filter_var($request->get('errata_id'), FILTER_SANITIZE_NUMBER_INT);
        /* @var $errata Errata */
        $errata = $em->getRepository('AppBundle:Errata')->find($errata_id);
        if(!$errata) {
            throw new BadRequestHttpException("Unable to find errata.");
        }
        if($errata->getUser()->getId() !== $user->getId()) {
            throw new UnauthorizedHttpException("You cannot edit this errata.");
        }

        $errata_raw = trim($request->get('errata'));

        $errata_raw = preg_replace(
            '%(?<!\()\b(?:(?:https?|ftp)://)(?:((?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?)(?:[^\s]*)?%iu', '[$1]($0)', $errata_raw);

        $errata_html = $this->get('texts')->markdown($errata_raw);
        if(!$errata_html) {
            return new Response('Your errata is empty.');
        }

        $errata->setTextMd($errata_raw);
        $errata->setTextHtml($errata_html);

        $em->flush();

        return new JsonResponse([
            'success' => TRUE
        ]);
    }

    public function likeAction (Request $request)
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        if(!$user) {
            throw $this->createAccessDeniedException("You are not logged in.");
        }

        $errata_id = filter_var($request->request->get('id'), FILTER_SANITIZE_NUMBER_INT);
        /* @var $errata Errata */
        $errata = $em->getRepository('AppBundle:Errata')->find($errata_id);
        if(!$errata) {
            throw new \Exception("Unable to find errata.");
        }

        // a user cannot vote on her own errata
        if($errata->getUser()->getId() != $user->getId()) {
            // checking if the user didn't already vote on that errata
            $query = $em->getRepository('AppBundle:Errata')
                ->createQueryBuilder('r')
                ->innerJoin('r.votes', 'u')
                ->where('r.id = :errata_id')
                ->andWhere('u.id = :user_id')
                ->setParameter('errata_id', $errata_id)
                ->setParameter('user_id', $user->getId())
                ->getQuery();

            $result = $query->getResult();
            if(empty($result)) {
                $author = $errata->getUser();
                $author->setReputation($author->getReputation() + 1);
                $user->addErrataVote($errata);
                $errata->setNbVotes($errata->getnbVotes() + 1);
                $em->flush();
            }
        }
        return new JsonResponse([
            'success' => TRUE,
            'nbVotes' => $errata->getNbVotes()
        ]);
    }

    public function removeAction ($id, Request $request)
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        if(!$user || !in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            throw $this->createAccessDeniedException('No user or not admin');
        }

        $errata_id = filter_var($request->get('id'), FILTER_SANITIZE_NUMBER_INT);
        /* @var $errata Errata */
        $errata = $em->getRepository('AppBundle:Errata')->find($errata_id);
        if(!$errata) {
            throw new \Exception("Unable to find errata.");
        }

        $votes = $errata->getVotes();
        foreach($votes as $vote) {
            $errata->removeVote($vote);
        }
        $em->remove($errata);
        $em->flush();

        return new JsonResponse([
            'success' => TRUE
        ]);
    }

    public function listAction ($page = 1, Request $request)
    {
        $response = new Response();
        $response->setPublic();
        $response->setMaxAge($this->container->getParameter('cache_expiration'));

        $limit = 5;
        if($page < 1)
            $page = 1;
        $start = ($page - 1) * $limit;

        $pagetitle = "Card Erratas";

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT r FROM AppBundle:Errata r JOIN r.card c JOIN c.pack p ORDER BY c.name ASC";
        $query = $em->createQuery($dql)->setFirstResult($start)->setMaxResults($limit);

        $paginator = new Paginator($query, false);
        $maxcount = count($paginator);

        $erratas = [];
        foreach($paginator as $errata) {
            $erratas[] = $errata;
        }

        // pagination : calcul de nbpages // currpage // prevpage // nextpage
        // à partir de $start, $limit, $count, $maxcount, $page

        $currpage = $page;
        $prevpage = max(1, $currpage - 1);
        $nbpages = min(10, ceil($maxcount / $limit));
        $nextpage = min($nbpages, $currpage + 1);

        $route = $request->get('_route');

        $params = $request->query->all();

        $pages = [];
        for($page = 1; $page <= $nbpages; $page ++) {
            $pages[] = array(
                "numero" => $page,
                "url" => $this->generateUrl($route, $params + array(
                        "page" => $page
                    )),
                "current" => $page == $currpage
            );
        }

        return $this->render('AppBundle:Erratas:erratas.html.twig', array(
            'pagetitle' => $pagetitle,
            'pagedescription' => "Read the latest user-submitted erratas on the cards.",
            'erratas' => $erratas,
            'url' => $request->getRequestUri(),
            'route' => $route,
            'pages' => $pages,
            'prevurl' => $currpage == 1 ? null : $this->generateUrl($route, $params + array(
                    "page" => $prevpage
                )),
            'nexturl' => $currpage == $nbpages ? null : $this->generateUrl($route, $params + array(
                    "page" => $nextpage
                ))
        ), $response);
    }

    public function byauthorAction ($user_id, $page = 1, Request $request)
    {
        $response = new Response();
        $response->setPublic();
        $response->setMaxAge($this->container->getParameter('cache_expiration'));

        $limit = 5;
        if($page < 1)
            $page = 1;
        $start = ($page - 1) * $limit;

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->find($user_id);

        $pagetitle = "Card Erratas by " . $user->getUsername();

        $dql = "SELECT r FROM AppBundle:Errata r WHERE r.user = :user ORDER BY r.date_creation DESC";
        $query = $em->createQuery($dql)->setFirstResult($start)->setMaxResults($limit)->setParameter('user', $user);

        $paginator = new Paginator($query, false);
        $maxcount = count($paginator);

        $erratas = [];
        foreach($paginator as $errata) {
            $erratas[] = $errata;
        }

        // pagination : calcul de nbpages // currpage // prevpage // nextpage
        // à partir de $start, $limit, $count, $maxcount, $page

        $currpage = $page;
        $prevpage = max(1, $currpage - 1);
        $nbpages = min(10, ceil($maxcount / $limit));
        $nextpage = min($nbpages, $currpage + 1);

        $route = $request->get('_route');

        $params = $request->query->all();

        $pages = [];
        for($page = 1; $page <= $nbpages; $page ++) {
            $pages[] = array(
                "numero" => $page,
                "url" => $this->generateUrl($route, $params + array(
                        "user_id" => $user_id,
                        "page" => $page
                    )),
                "current" => $page == $currpage
            );
        }

        return $this->render('AppBundle:Erratas:erratas.html.twig', array(
            'pagetitle' => $pagetitle,
            'pagedescription' => "Read the latest user-submitted erratas on the cards.",
            'erratas' => $erratas,
            'url' => $request->getRequestUri(),
            'route' => $route,
            'pages' => $pages,
            'prevurl' => $currpage == 1 ? null : $this->generateUrl($route, $params + array(
                    "user_id" => $user_id,
                    "page" => $prevpage
                )),
            'nexturl' => $currpage == $nbpages ? null : $this->generateUrl($route, $params + array(
                    "user_id" => $user_id,
                    "page" => $nextpage
                ))
        ), $response);
    }

    public function commentAction (Request $request)
    {

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();

        /* @var $user User */
        $user = $this->getUser();
        if(!$user) {
            throw $this->createAccessDeniedException("You are not logged in.");
        }

        $errata_id = filter_var($request->get('comment_errata_id'), FILTER_SANITIZE_NUMBER_INT);
        /* @var $errata Errata */
        $errata = $em->getRepository('AppBundle:Errata')->find($errata_id);
        if(!$errata) {
            throw new \Exception("Unable to find errata.");
        }

        $comment_text = trim($request->get('comment'));
        $comment_text = htmlspecialchars($comment_text);
        if(!$comment_text) {
            throw new \Exception('Your comment is empty.');
        }

        $comment = new Erratacomment();
        $comment->setErrata($errata);
        $comment->setUser($user);
        $comment->setText($comment_text);

        $em->persist($comment);

        $em->flush();

        // send emails
        $spool = [];
        if($errata->getUser()->getIsNotifAuthor()) {
            if(!isset($spool[$errata->getUser()->getEmail()])) {
                $spool[$errata->getUser()->getEmail()] = 'AppBundle:Emails:newerratacomment_author.html.twig';
            }
        }
        unset($spool[$user->getEmail()]);

        $email_data = array(
            'username' => $user->getUsername(),
            'card_name' => $errata->getCard()->getName(),
            'url' => $this->generateUrl('cards_zoom', array('card_code' => $errata->getCard()->getCode()), UrlGeneratorInterface::ABSOLUTE_URL),
            'comment' => $comment->getText(),
            'profile' => $this->generateUrl('user_profile_edit', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );
        foreach($spool as $email => $view) {
            $message = Swift_Message::newInstance()
                ->setSubject("[thronesdb] New errata comment")
                ->setFrom(array("alsciende@thronesdb.com" => $user->getUsername()))
                ->setTo($email)
                ->setBody($this->renderView($view, $email_data), 'text/html');
            $this->get('mailer')->send($message);
        }

        return new JsonResponse([
            'success' => TRUE
        ]);
    }

    public function listLatestFirstAction ($page = 1, Request $request)
    {
        $response = new Response();
        $response->setPublic();
        $response->setMaxAge($this->container->getParameter('cache_expiration'));

        $limit = 5;
        if($page < 1)
            $page = 1;
        $start = ($page - 1) * $limit;

        $pagetitle = "Card Erratas";

        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT r FROM AppBundle:Errata r JOIN r.card c JOIN c.pack p ORDER BY r.date_creation DESC";
        $query = $em->createQuery($dql)->setFirstResult($start)->setMaxResults($limit);

        $paginator = new Paginator($query, false);
        $maxcount = count($paginator);

        $erratas = [];
        foreach($paginator as $errata) {
            $erratas[] = $errata;
        }

        // pagination : calcul de nbpages // currpage // prevpage // nextpage
        // à partir de $start, $limit, $count, $maxcount, $page

        $currpage = $page;
        $prevpage = max(1, $currpage - 1);
        $nbpages = min(10, ceil($maxcount / $limit));
        $nextpage = min($nbpages, $currpage + 1);

        $route = $request->get('_route');

        $params = $request->query->all();

        $pages = [];
        for($page = 1; $page <= $nbpages; $page ++) {
            $pages[] = array(
                "numero" => $page,
                "url" => $this->generateUrl($route, $params + array(
                        "page" => $page
                    )),
                "current" => $page == $currpage
            );
        }

        return $this->render('AppBundle:Erratas:erratas.html.twig', array(
            'pagetitle' => $pagetitle,
            'pagedescription' => "Read the latest user-submitted erratas on the cards.",
            'erratas' => $erratas,
            'url' => $request->getRequestUri(),
            'route' => $route,
            'pages' => $pages,
            'prevurl' => $currpage == 1 ? null : $this->generateUrl($route, $params + array(
                    "page" => $prevpage
                )),
            'nexturl' => $currpage == $nbpages ? null : $this->generateUrl($route, $params + array(
                    "page" => $nextpage
                ))
        ), $response);
    }

}
