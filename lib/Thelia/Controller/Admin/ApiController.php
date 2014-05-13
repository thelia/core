<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Thelia\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Core\Event\Api\ApiCreateEvent;
use Thelia\Core\Event\Api\ApiDeleteEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Api\ApiCreateForm;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\Api;
use Thelia\Model\ApiQuery;
use Thelia\Tools\URL;


/**
 * Class ApiController
 * @package Thelia\Controller\Admin
 * @author Manuel Raynaud <mraynaud@openstudio.fr>
 */
class ApiController extends BaseAdminController
{
    public function downloadAction($api_id)
    {
        if (null !== $response = $this->checkAuth([AdminResources::API], [], AccessManager::VIEW)) {
            return $response;
        }

        $api = ApiQuery::create()->findPk($api_id);

        if (null === $api) {
            $response = $this->errorPage(Translator::getInstance()->trans("api id %id does not exists", ['%id' => $api_id]));
        } else {
            $response = $this->retrieveSecureKey($api);
        }

        return $response;
    }

    public function deleteAction()
    {
        if (null !== $response = $this->checkAuth([AdminResources::API], [], AccessManager::DELETE)) {
            return $response;
        }

        $api_id = $this->getRequest()->request->get('api_id');

        $api = ApiQuery::create()->findPk($api_id);

        if (null === $api) {
            $response = $this->errorPage(Translator::getInstance()->trans("api id %id does not exists", ['%id' => $api_id]));
        } else {
            $response = $this->deleteApi($api);
        }

        return $response;
    }

    private function deleteApi(Api $api)
    {
        $event = new ApiDeleteEvent($api);

        $this->dispatch(TheliaEvents::API_DELETE, $event);

        return RedirectResponse::create(URL::getInstance()->absoluteUrl($this->getRoute('admin.configuration.api')));
    }

    /**
     * @param Api $api
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function retrieveSecureKey(Api $api)
    {
        $response = Response::create($api->getSecureKey());
        $response->headers->add([
            'Content-Type' => 'application/octet-stream',
            'Content-disposition' => sprintf('filename=%s.key', $api->getApiKey())
        ]);

        return $response;
    }

    public function indexAction()
    {
        if (null !== $response = $this->checkAuth([AdminResources::API], [], AccessManager::VIEW)) {
            return $response;
        }

        return $this->renderList();
    }

    public function createAction()
    {
        if (null !== $response = $this->checkAuth([AdminResources::API], [], AccessManager::CREATE)) {
            return $response;
        }

        $form = new ApiCreateForm($this->getRequest());
        $error_msg = null;
        try {

            $createForm = $this->validateForm($form);

            $event = new ApiCreateEvent(
                $createForm->get('label')->getData(),
                $createForm->get('profile')->getData() ?: null
            );

            $this->dispatch(TheliaEvents::API_CREATE, $event);

            return RedirectResponse::create($form->getSuccessUrl());

        } catch(FormValidationException $e) {
            $error_msg = $this->createStandardFormValidationErrorMessage($e);
        } catch(\Exception $e) {
            $error_msg = $e->getMessage();
        }

        if (false !== $error_msg) {
            $this->setupFormErrorContext(
                $this->getTranslator()->trans("%obj creation", array('%obj' => 'Api')),
                $error_msg,
                $form,
                $e
            );

            // At this point, the form has error, and should be redisplayed.
            return $this->renderList();
        }
    }

    protected function renderList()
    {
        $apiAccessList = ApiQuery::create()->find()->toArray();
        return $this->render(
            'api',
            [
                'api_list' => $apiAccessList
            ]
        );
    }

}