<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thelia\Controller\Admin;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Template\TemplateAddAttributeEvent;
use Thelia\Core\Event\Template\TemplateAddFeatureEvent;
use Thelia\Core\Event\Template\TemplateCreateEvent;
use Thelia\Core\Event\Template\TemplateDeleteAttributeEvent;
use Thelia\Core\Event\Template\TemplateDeleteEvent;
use Thelia\Core\Event\Template\TemplateDeleteFeatureEvent;
use Thelia\Core\Event\Template\TemplateDuplicateEvent;
use Thelia\Core\Event\Template\TemplateUpdateEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Template\ParserContext;
use Thelia\Form\Definition\AdminForm;
use Thelia\Model\AttributeTemplateQuery;
use Thelia\Model\FeatureTemplateQuery;
use Thelia\Model\Template;
use Thelia\Model\TemplateQuery;

/**
 * Manages product templates.
 *
 * @author Franck Allimant <franck@cqfdev.fr>
 */
class TemplateController extends AbstractCrudController
{
    public function __construct()
    {
        parent::__construct(
            'template',
            null,
            null,
            AdminResources::TEMPLATE,
            TheliaEvents::TEMPLATE_CREATE,
            TheliaEvents::TEMPLATE_UPDATE,
            TheliaEvents::TEMPLATE_DELETE,
            null, // No visibility toggle
            null // No position update
        );
    }

    protected function getCreationForm()
    {
        return $this->createForm(AdminForm::TEMPLATE_CREATION);
    }

    protected function getUpdateForm()
    {
        return $this->createForm(AdminForm::TEMPLATE_MODIFICATION);
    }

    protected function getCreationEvent($formData)
    {
        $createEvent = new TemplateCreateEvent();

        $createEvent
            ->setTemplateName($formData['name'])
            ->setLocale($formData['locale'])
        ;

        return $createEvent;
    }

    protected function getUpdateEvent($formData)
    {
        $changeEvent = new TemplateUpdateEvent($formData['id']);

        // Create and dispatch the change event
        $changeEvent
            ->setLocale($formData['locale'])
            ->setTemplateName($formData['name'])
        ;

        // Add feature and attributes list
        return $changeEvent;
    }

    protected function getDeleteEvent()
    {
        return new TemplateDeleteEvent($this->getRequest()->get('template_id'));
    }

    protected function eventContainsObject($event)
    {
        return $event->hasTemplate();
    }

    protected function hydrateObjectForm(ParserContext $parserContext, $object)
    {
        $data = [
            'id' => $object->getId(),
            'locale' => $object->getLocale(),
            'name' => $object->getName(),
        ];

        // Setup the object form
        return $this->createForm(AdminForm::TEMPLATE_MODIFICATION, FormType::class, $data);
    }

    protected function getObjectFromEvent($event)
    {
        return $event->hasTemplate() ? $event->getTemplate() : null;
    }

    protected function getExistingObject()
    {
        $template = TemplateQuery::create()
            ->findOneById($this->getRequest()->get('template_id', 0));

        if (null !== $template) {
            $template->setLocale($this->getCurrentEditionLocale());
        }

        return $template;
    }

    /**
     * @param Template $object
     *
     * @return string
     */
    protected function getObjectLabel($object)
    {
        return $object->getName();
    }

    /**
     * @param Template $object
     *
     * @return int
     */
    protected function getObjectId($object)
    {
        return $object->getId();
    }

    protected function renderListTemplate($currentOrder)
    {
        return $this->render('templates', ['order' => $currentOrder]);
    }

    protected function renderEditionTemplate()
    {
        return $this->render(
            'template-edit',
            [
                    'template_id' => $this->getRequest()->get('template_id'),
            ]
        );
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function redirectToEditionTemplate($request = null, $id = null)
    {
        return $this->generateRedirectFromRoute(
            'admin.configuration.templates.update',
            [
                'template_id' => $id ?: $this->getRequest()->get('template_id'),
            ]
        );
    }

    protected function redirectToListTemplate()
    {
        return $this->generateRedirectFromRoute('admin.configuration.templates.default');
    }

    /**
     * Process delete failure, which may occurs if template is in use.
     *
     * @param TemplateDeleteEvent $deleteEvent
     *
     * @return \Thelia\Core\HttpFoundation\Response|null
     */
    protected function performAdditionalDeleteAction($deleteEvent)
    {
        if ($deleteEvent->getProductCount() > 0) {
            $this->getParserContext()->setGeneralError(
                $this->getTranslator()->trans(
                    'This template is in use in some of your products, and cannot be deleted. Delete it from all your products and try again.'
                )
            );

            return $this->renderList();
        }

        // Normal delete processing
        return null;
    }

    public function duplicateAction(EventDispatcherInterface $eventDispatcher)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(AdminResources::TEMPLATE, [], AccessManager::CREATE)) {
            return $response;
        }

        $template_id = (int) $this->getRequest()->get('template_id');

        if ($template_id > 0) {
            try {
                $event = new TemplateDuplicateEvent($template_id, $this->getCurrentEditionLocale());

                $eventDispatcher->dispatch($event, TheliaEvents::TEMPLATE_DUPLICATE);

                if ($event->hasTemplate()) {
                    $template_id = $event->getTemplate()->getId();
                }
            } catch (\Exception $ex) {
                // Any error
                return $this->errorPage($ex);
            }
        }

        return $this->redirectToEditionTemplate(null, $template_id);
    }

    public function getAjaxFeaturesAction()
    {
        return $this->render(
            'ajax/template-feature-list',
            ['template_id' => $this->getRequest()->get('template_id')]
        );
    }

    public function getAjaxAttributesAction()
    {
        return $this->render(
            'ajax/template-attribute-list',
            ['template_id' => $this->getRequest()->get('template_id')]
        );
    }

    public function addAttributeAction(EventDispatcherInterface $eventDispatcher)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(AdminResources::TEMPLATE, [], AccessManager::UPDATE)) {
            return $response;
        }

        $attribute_id = (int) $this->getRequest()->get('attribute_id');

        if ($attribute_id > 0) {
            $event = new TemplateAddAttributeEvent(
                $this->getExistingObject(),
                $attribute_id
            );

            try {
                $eventDispatcher->dispatch($event, TheliaEvents::TEMPLATE_ADD_ATTRIBUTE);
            } catch (\Exception $ex) {
                // Any error
                return $this->errorPage($ex);
            }
        }

        return $this->redirectToEditionTemplate();
    }

    public function deleteAttributeAction(EventDispatcherInterface $eventDispatcher)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(AdminResources::TEMPLATE, [], AccessManager::UPDATE)) {
            return $response;
        }

        $event = new TemplateDeleteAttributeEvent(
            $this->getExistingObject(),
            (int) $this->getRequest()->get('attribute_id')
        );

        try {
            $eventDispatcher->dispatch($event, TheliaEvents::TEMPLATE_DELETE_ATTRIBUTE);
        } catch (\Exception $ex) {
            // Any error
            return $this->errorPage($ex);
        }

        return $this->redirectToEditionTemplate();
    }

    public function updateAttributePositionAction(
        Request $request,
        EventDispatcherInterface $eventDispatcher
    ) {
        // Find attribute_template
        $attributeTemplate = AttributeTemplateQuery::create()
            ->filterByTemplateId($request->get('template_id', null))
            ->filterByAttributeId($request->get('attribute_id', null))
            ->findOne()
        ;

        return $this->genericUpdatePositionAction(
            $request,
            $eventDispatcher,
            $attributeTemplate,
            TheliaEvents::TEMPLATE_CHANGE_ATTRIBUTE_POSITION
        );
    }

    public function addFeatureAction(EventDispatcherInterface $eventDispatcher)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(AdminResources::TEMPLATE, [], AccessManager::UPDATE)) {
            return $response;
        }

        $feature_id = (int) $this->getRequest()->get('feature_id');

        if ($feature_id > 0) {
            $event = new TemplateAddFeatureEvent(
                $this->getExistingObject(),
                $feature_id
            );

            try {
                $eventDispatcher->dispatch($event, TheliaEvents::TEMPLATE_ADD_FEATURE);
            } catch (\Exception $ex) {
                // Any error
                return $this->errorPage($ex);
            }
        }

        return $this->redirectToEditionTemplate();
    }

    public function deleteFeatureAction(EventDispatcherInterface $eventDispatcher)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth(AdminResources::TEMPLATE, [], AccessManager::UPDATE)) {
            return $response;
        }

        $event = new TemplateDeleteFeatureEvent(
            $this->getExistingObject(),
            (int) $this->getRequest()->get('feature_id')
        );

        try {
            $eventDispatcher->dispatch($event, TheliaEvents::TEMPLATE_DELETE_FEATURE);
        } catch (\Exception $ex) {
            // Any error
            return $this->errorPage($ex);
        }

        return $this->redirectToEditionTemplate();
    }

    public function updateFeaturePositionAction(
        Request $request,
        EventDispatcherInterface $eventDispatcher
    ) {
        // Find feature_template
        $featureTemplate = FeatureTemplateQuery::create()
            ->filterByTemplateId($request->get('template_id', null))
            ->filterByFeatureId($request->get('feature_id', null))
            ->findOne()
        ;

        return $this->genericUpdatePositionAction(
            $request,
            $eventDispatcher,
            $featureTemplate,
            TheliaEvents::TEMPLATE_CHANGE_FEATURE_POSITION
        );
    }
}
