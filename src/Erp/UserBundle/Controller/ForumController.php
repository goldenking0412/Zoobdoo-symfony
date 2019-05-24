<?php

namespace Erp\UserBundle\Controller;

use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\ForumComment;
use Erp\UserBundle\Entity\ForumTopic;
use Erp\UserBundle\Entity\UserDocument;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\CoreBundle\Entity\EmailNotification;
use Erp\CoreBundle\Event\EmailNotificationEvent;
use Erp\UserBundle\Form\Type\ForumTopicFormType;
use Erp\UserBundle\Form\Type\ForumCommentFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Erp\UserBundle\Entity\User;
use Erp\CoreBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ForumController
 *
 * @package Erp\UserBundle\Controller
 */
class ForumController extends BaseController
{
    /**
     * Forum index page
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \HttpException
     */
    public function indexAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $forumTopic = new ForumTopic();
        $form = $this->createForumTopicForm($forumTopic);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $forumTopic->setUser($user);
                $this->em->persist($forumTopic);
                $this->em->flush();

                return $this->redirectToRoute('erp_user_profile_forum_topic', ['topicId' => $forumTopic->getId()]);
            }
        }

        $forumTopics = $this->em->getRepository('ErpUserBundle:ForumTopic')->findAll();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $forumTopics,
            $request->query->getInt('page', 1),
            $forumTopic::LIMIT_FORUM_TOPICS
        );

        return $this->render(
            'ErpUserBundle:Forum:index.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
                'pagination' => $pagination
            ]
        );
    }

    /**
     * Forum topic page
     *
     * @param Request $request
     * @param int     $topicId
     *
     * @return Response
     */
    public function topicAction(Request $request, $topicId)
    {
        /** @var User $user */
        $user = $this->getUser();

        $forumTopic = $this->em->getRepository('ErpUserBundle:ForumTopic')->find($topicId);

        if (!$forumTopic instanceof ForumTopic) {
            throw new NotFoundHttpException('Topic not found');
        }

        $topForumTopics = $this->em->getRepository('ErpUserBundle:ForumTopic')
            ->findBy([], ['updatedDate' => 'DESC'], ForumTopic::LIMIT_TOP_FORUM_TOPICS);

        $forumComments = $this->em->getRepository('ErpUserBundle:ForumComment')
            ->findBy(['forumTopic' => $forumTopic], ['updatedDate' => 'DESC']);

        $paginator = $this->get('knp_paginator');
        $forumCommentsPagination = $paginator->paginate(
            $forumComments,
            $request->query->getInt('page', 1),
            ForumComment::LIMIT_FORUM_COMMENTS
        );
        $forumCommentsPagination->setTemplate('ErpUserBundle:Forum:pagination.html.twig');

        return $this->render(
            'ErpUserBundle:Forum:topic.html.twig',
            [
                'user' => $user,
                'topic' => $forumTopic,
                'topForumTopics' => $topForumTopics,
                'forumCommentsPagination' => $forumCommentsPagination
            ]
        );
    }

    /**
     * Create comment action
     *
     * @param Request $request
     * @param int     $topicId
     *
     * @return JsonResponse
     */
    public function createCommentAction(Request $request, $topicId)
    {
        /** @var User $user */
        $user = $this->getUser();

        $forumTopic = $this->em->getRepository('ErpUserBundle:ForumTopic')->find($topicId);

        if (!$forumTopic instanceof ForumTopic) {
            throw new NotFoundHttpException('Topic not found');
        }

        $text = $request->get('text');

        $forumComment = new ForumComment();
        $forumComment->setUser($user);
        $forumComment->setText($text);
        $forumComment->setForumTopic($forumTopic);

        $validator = $this->get('validator');

        /** @var $errors \Symfony\Component\Validator\ConstraintViolationListInterface */
        $errors = $validator->validate($forumComment, null, ['CreatedForumComment']);

        if (count($errors)) {
            return new JsonResponse(['errors' => $errors->get(0)->getMessage()]);
        }

        $forumTopic->setUpdatedDate();

        $this->em->persist($forumComment);
        $this->em->persist($forumTopic);
        $this->em->flush();

        $this->sendNotificationEmail($forumTopic, $user);

        return $this->render('ErpUserBundle:Forum:comment.html.twig', ['comment' => $forumComment]);
    }

    /**
     * Render comment form
     *
     * @param ForumTopic $forumTopic
     *
     * @return Response
     */
    public function renderCommentFormAction(ForumTopic $forumTopic)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForumCommentForm($forumTopic, new ForumComment());

        return $this->render(
            'ErpUserBundle:Forum:comment-form.html.twig',
            [
                'user' => $user,
                'form' => $form->createView()
            ]
        );
    }

    /**
     * Create forum topic form
     *
     * @param ForumTopic $forumTopic
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createForumTopicForm(ForumTopic $forumTopic)
    {
        $actionForm = $this->generateUrl('erp_user_profile_forum_index');
        $formOptions = ['action' => $actionForm, 'method' => 'POST'];
        $form = $this->createForm(new ForumTopicFormType(), $forumTopic, $formOptions);

        return $form;
    }

    /**
     * Create forum comment form
     *
     * @param ForumTopic $forumTopic
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createForumCommentForm(ForumTopic $forumTopic, ForumComment $forumComment)
    {
        $actionForm = $this->generateUrl('erp_user_profile_forum_create_comment', ['topicId' => $forumTopic->getId()]);
        $formOptions = ['action' => $actionForm, 'method' => 'POST'];
        $form = $this->createForm(new ForumCommentFormType(), $forumComment, $formOptions);

        return $form;
    }

    /**
     * Sent email to managers with comments in current topic about a new comment in topic
     *
     * @param ForumTopic $forumTopic
     * @param User $user
     *
     * @return bool
     */
    protected function sendNotificationEmail(ForumTopic $forumTopic, $user)
    {
        $comments = $forumTopic->getForumComments();

        $managers = array();

        foreach ($comments as $comment) {
            if ($comment->getUser()->getId() != $user->getId() and $comment->getUser()->hasRole(User::ROLE_MANAGER)) {
                $managers[$comment->getUser()->getId()] = $comment->getUser();
            }
        }

        if ($managers) {
            foreach ($managers as $manager) {
                $event = new EmailNotificationEvent(
                    $manager,
                    EmailNotification::SETTING_FORUM_TOPICS,
                    [
                        '#url#' => $this->generateUrl(
                            'erp_user_profile_forum_topic',
                            ['topicId' => $forumTopic->getId()],
                            true
                        ),
                    ]
                );

                /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
                $dispatcher = $this->get('event_dispatcher');
                $dispatcher->dispatch(EmailNotification::EVENT_SEND_EMAIL_NOTIFICATION, $event);
            }
        }

        return true;
    }
}
