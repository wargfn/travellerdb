<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
	public function indexAction()
	{
		return $this->render('AppBundle:Admin:index.html.twig');
	}

	public function errataIndexAction($page=1, Request $request)
    {
        $response = new Response();
        $response->setPublic();
        $response->setMaxAge($this->container->getParameter('cache_expiration'));

        $limit = 220;
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
}
