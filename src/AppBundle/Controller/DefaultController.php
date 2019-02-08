<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Notice;
use AppBundle\Entity\Rating;
use AppBundle\Repository\RatingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    protected $ratingRepository;

    /**
     * DefaultController constructor.
     * @param RatingRepository $ratingRepository
     */
    public function __construct(RatingRepository $ratingRepository)
    {
        $this->ratingRepository = $ratingRepository;
    }

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
     * @Route("/notice-graph/{id}", name="notice_graph", options={"expose"=true})
     */
    public function noticeGraphAction(Request $request, Notice $notice) {

        $displayData = $this->ratingRepository->getGraphDataByNoticeType($notice, Rating::DISPLAY);
        $clickData = $this->ratingRepository->getGraphDataByNoticeType($notice, Rating::CLICK);
        $approveData = $this->ratingRepository->getGraphDataByNoticeType($notice, Rating::APPROVE);
        $dismissData = $this->ratingRepository->getGraphDataByNoticeType($notice, Rating::DISMISS);

        return $this->render('default/notice_graph_modal.html.twig', [
            'labels' => array_keys($displayData),
            'display_data' => array_values($displayData),
            'click_data' => array_values($clickData),
            'approve_data' => array_values($approveData),
            'dismiss_data' => array_values($dismissData)
        ]);
    }
}
