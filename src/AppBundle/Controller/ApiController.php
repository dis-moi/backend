<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Alternative;
use AppBundle\Entity\BrowserExtension\Criterion as CriterionDto;
use AppBundle\Entity\BrowserExtension\Editor as EditorDto;
use AppBundle\Entity\BrowserExtension\MatchingContextFactory;
use AppBundle\Entity\BrowserExtension\RecommendationFactory;
use AppBundle\Entity\Contributor;
use AppBundle\Entity\Editor;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Recommendation;
use AppBundle\Entity\Criterion;
use AppBundle\Query\MatchingContext\FindMatchingContextsByChannelQuery;
use AppBundle\Query\MatchingContext\FindMatchingContextsByChannelQueryHandler;
use AppBundle\Query\MatchingContext\MatchingContextCriterion;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Router;

class ApiController extends FOSRestController
{
    /**
     * @Route("/matchingcontexts")
     * @View()
     */
    public function getMatchingcontextsAction(Request $request) {
        $criteriaFilter = $request->get('criteria', null);
        $criteria = $criteriaFilter ? explode(",", $criteriaFilter) : [];
        $excludedEditorsFilter = $request->get('excluded_editors', null);
        $excludedEditors = $excludedEditorsFilter ? explode(",", $excludedEditorsFilter) : [];
        $excludedEditors = array_map(function($editorId) {return intval($editorId);}, $excludedEditors);
        $matchingContexts = $this->getDoctrine()
            ->getRepository('AppBundle:MatchingContext')
            ->findAllPublicMatchingContext($criteria, $excludedEditors);

        if (!$matchingContexts) {
            throw $this->createNotFoundException('No matching contexts exists');
        }

        $factory = new MatchingContextFactory(function ($id) {
            return $this->get('router')->generate('app_api_getrecommendation', ['id' => $id], Router::ABSOLUTE_URL);
        });

        return array_map(function ($matchingContext) use ($factory){
            return $factory->createFromMatchingContext($matchingContext);
        }, $matchingContexts);
    }

    /**
     * @Route("/{channel}/matchingcontexts")
     * @View()
     */
    public function getMatchingContextsByChannelAction(string $channel, Request $request) {
        $handler = new FindMatchingContextsByChannelQueryHandler(
            $this->getDoctrine()->getRepository('AppBundle:MatchingContext'),
            new MatchingContextFactory(function ($id) {
                return $this->get('router')->generate('app_api_getrecommendation', ['id' => $id], Router::ABSOLUTE_URL);
            }));

        return $handler->handle(
            new FindMatchingContextsByChannelQuery(
                $channel,
                MatchingContextCriterion::fromRequest($request))
        );

    }

    /**
     * @Route("/criteria")
     * @View()
     */
    public function getCriteriaAction() {
        $criteria = $this->getDoctrine()->getRepository('AppBundle:Criterion')->findAll();

        if (!$criteria) {
            throw $this->createNotFoundException('No criterion found');
        }

        return array_map(function (Criterion $criterion) {
            return new CriterionDto($criterion->getLabel(), $criterion->getSlug());
        }, $criteria);
    }

    /**
     * @Route("/editors")
     * @View()
     */
    public function getEditorsAction() {
        $editors = $this->getDoctrine()->getRepository('AppBundle:Editor')->findAll();

        if (!$editors) {
            throw $this->createNotFoundException('No editors found');
        }

        return array_map(function (Editor $editor) {
            return new EditorDto($editor->getId(), $editor->getLabel(), $editor->getUrl());
        }, $editors);
    }
    
    /**
     * @Route("/alternative/{id}")
     * @ParamConverter("alternative", class="AppBundle:Alternative")
     * @View()
     */
    public function getAlternativeAction(Alternative $alternative)
    {
        return $alternative;
    }

    /**
     * @Route("/recommendation/{id}")
     * @ParamConverter("recommendation", class="AppBundle:Recommendation")
     * @View()
     */
    public function getRecommendationAction(Recommendation $recommendation, Request $request)
    {
        if(!$recommendation->hasPublicVisibility()) throw $this->createNotFoundException(
            'No recommendation exists'
        );

        return (new RecommendationFactory(
            function(Contributor $contributor) use($request) {
                return $request->getSchemeAndHttpHost().$this->get('vich_uploader.storage')->resolveUri($contributor, 'imageFile');
        }))->createFromRecommendation($recommendation);
    }

    /**
     * @Route("/recommendations/{id}/feedbacks")
     * @ParamConverter("recommendation", class="AppBundle:Recommendation")
     * @View()
     */
    public function createRecommendationFeedbackAction(Recommendation $recommendation, Request $request)
    {
        if(!$recommendation->hasPublicVisibility()) throw $this->createNotFoundException(
            'No recommendation exists'
        );
        $postedJsonFeedback = $request->getContent();
        $postedFeedback = json_decode($postedJsonFeedback, $asArray = true);

        $feedbackType = array_key_exists('feedback', $postedFeedback) ? $postedFeedback['feedback'] : null;
        $feedbackContext = array_key_exists('context', $postedFeedback) ? $postedFeedback['context'] : [];

        try {
            $feedback = new Feedback(
                $recommendation,
                $feedbackType,
                $feedbackContext
            );
        } catch(\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], $statusClientError = 400);
        }

        $feedbackRepository = $this->getDoctrine()->getRepository('AppBundle:Feedback');
        $this->getDoctrine()->getManager()->persist($feedback);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([], $statusCreated = 201);
    }
}
