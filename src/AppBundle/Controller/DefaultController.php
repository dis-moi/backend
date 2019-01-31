<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Notice;
use AppBundle\Entity\Rating;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/notice-graph/{id}", name="notice_graph", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     */
    public function noticeGraphAction(Request $request, Notice $notice) {

        /** @var \AppBundle\Repository\RatingRepository $ratingRepository */
        $ratingRepository = $this->getDoctrine()->getRepository(Rating::class);
        $displayData = $ratingRepository->getGraphDataByNoticeType($notice, Rating::DISPLAY);
        $clickData = $ratingRepository->getGraphDataByNoticeType($notice, Rating::CLICK);
        $approveData = $ratingRepository->getGraphDataByNoticeType($notice, Rating::APPROVE);
        $dismissData = $ratingRepository->getGraphDataByNoticeType($notice, Rating::DISMISS);

        return $this->render('default/notice_graph_modal.html.twig', [
            'labels' => array_keys($displayData),
            'display_data' => array_values($displayData),
            'click_data' => array_values($clickData),
            'approve_data' => array_values($approveData),
            'dismiss_data' => array_values($dismissData)
        ]);
    }
}
