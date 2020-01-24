<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Notice;
use AppBundle\Entity\Rating;
use AppBundle\Repository\RatingRepository;
use Exception;
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
     * @Route("/debug-sentry")
     * @throws Exception
     */
    public function debug_sentry()
    {
      throw new Exception('My first Sentry error!');
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

        $badgeData = $this->ratingRepository
            ->getGraphDataByNoticeTypes($notice, [Rating::BADGE]);
        $displayData = $this->ratingRepository
            ->getGraphDataByNoticeTypes($notice, [Rating::DISPLAY]);
        $unfoldData = $this->ratingRepository
            ->getGraphDataByNoticeTypes($notice, [Rating::UNFOLD]);
        $clickData = $this->ratingRepository
            ->getGraphDataByNoticeTypes($notice, [Rating::OUTBOUND_CLICK]);
        $likeData = $this->ratingRepository
            ->getGraphDataByNoticeBalanceType($notice, Rating::LIKE, Rating::UNLIKE);
        $dislikeData = $this->ratingRepository
            ->getGraphDataByNoticeBalanceType($notice, Rating::DISLIKE, Rating::UNDISLIKE);
        $dismissData = $this->ratingRepository
            ->getGraphDataByNoticeBalanceType($notice, Rating::DISMISS, Rating::UNDISMISS);

        return $this->render('default/notice_graph_modal.html.twig', [
            'labels' => array_keys($displayData),
            'badge_data' => array_values($badgeData),
            'display_data' => array_values($displayData),
            'unfold_data' => array_values($unfoldData),
            'click_data' => array_values($clickData),
            'like_data' => array_values($likeData),
            'dislike_data' => array_values($dislikeData),
            'dismiss_data' => array_values($dismissData)
        ]);
    }
}
