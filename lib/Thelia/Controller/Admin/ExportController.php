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

use Thelia\Core\FileFormat\Archive\ArchiveBuilderManagerTrait;
use Thelia\Core\FileFormat\Formatting\FormatterManagerTrait;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Loop\Export as ExportLoop;
use Thelia\Core\Event\ImportExport as ImportExportEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\FileFormat\Archive\AbstractArchiveBuilder;
use Thelia\Core\FileFormat\Formatting\AbstractFormatter;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Form\ExportForm;
use Thelia\ImportExport\Export\DocumentsExportInterface;
use Thelia\ImportExport\Export\ExportHandler;
use Thelia\ImportExport\Export\ImagesExportInterface;
use Thelia\Model\ExportCategoryQuery;
use Thelia\Model\ExportQuery;

/**
 * Class ExportController
 * @package Thelia\Controller\Admin
 * @author Manuel Raynaud <mraynaud@openstudio.fr>
 */
class ExportController extends BaseAdminController
{
    use ArchiveBuilderManagerTrait;
    use FormatterManagerTrait;

    public function indexAction()
    {
        if (null !== $response = $this->checkAuth([AdminResources::EXPORT], [], [AccessManager::VIEW])) {
            return $response;
        }

        $this->setOrders();

        return $this->render('export');
    }

    /**
     * @param  integer  $id
     * @return Response
     *
     * This method is called when the route /admin/export/{id}
     * is called with a POST request.
     */
    public function export($id)
    {
        if (null === $export = $this->getExport($id)) {
            return $this->render("404");
        }

        /**
         * Get needed services
         */
        $archiveBuilderManager = $this->getArchiveBuilderManager($this->container);
        $formatterManager = $this->getFormatterManager($this->container);

        /**
         * Get the archive builders
         */
        $archiveBuilders = [];
        foreach ($archiveBuilderManager->getNames() as $archiveBuilder) {
            $archiveBuilders[$archiveBuilder] = $archiveBuilder;
        }

        /**
         * Define and validate the form
         */
        $form = new ExportForm($this->getRequest());
        $errorMessage = null;

        try {
            $boundForm = $this->validateForm($form);

            $archiveBuilder = null;

            /**
             * Get the formatter and the archive builder if we have to compress the file(s)
             */

            /** @var \Thelia\Core\FileFormat\Formatting\AbstractFormatter $formatter */
            $formatter = $formatterManager->get(
                $boundForm->get("formatter")->getData()
            );

            if ($boundForm->get("do_compress")->getData()) {
                /** @var \Thelia\Core\FileFormat\Archive\ArchiveBuilderInterface $archiveBuilder */
                $archiveBuilder = $archiveBuilderManager->get(
                    $boundForm->get("archive_builder")->getData()
                );
            }

            /**
             * Return the generated Response
             */

            return $this->processExport(
                $formatter,
                $export->getHandleClassInstance($this->container),
                $archiveBuilder,
                $boundForm->get("images")->getData(),
                $boundForm->get("documents")->getData()
            );

        } catch (FormValidationException $e) {
            $errorMessage = $this->createStandardFormValidationErrorMessage($e);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        /**
         * If has an error, display it
         */
        if (null !== $errorMessage) {
            $form->setErrorMessage($errorMessage);

            $this->getParserContext()
                ->addForm($form)
                ->setGeneralError($errorMessage)
            ;
        }

        return $this->exportView($id);
    }

    /**
     * @param AbstractFormatter $formatter
     * @param ExportHandler $handler
     * @param AbstractArchiveBuilder $archiveBuilder
     * @param bool $includeImages
     * @param bool $includeDocuments
     * @return Response
     *
     * Processes an export by returning a response with the export's content.
     */
    protected function processExport(
        AbstractFormatter $formatter,
        ExportHandler $handler,
        AbstractArchiveBuilder $archiveBuilder = null,
        $includeImages = false,
        $includeDocuments = false
    ) {
        /**
         * Build an event containing the formatter and the handler.
         * Used for specific configuration (e.g: XML node names)
         */
        $data = $handler->buildFormatterData();
        $event = new ImportExportEvent($formatter, $handler , $data);

        $filename = $formatter::FILENAME . "." . $formatter->getExtension();

        if ($archiveBuilder === null) {
            $this->dispatch(TheliaEvents::BEFORE_EXPORT, $event);

            $formattedContent = $formatter->encode($data);

            return new Response(
                $formattedContent,
                200,
                [
                    "Content-Type" => $formatter->getMimeType(),
                    "Content-Disposition" =>
                        "attachment; filename=\"" . $filename . "\"",
                ]
            );
        } else {
            $event->setArchiveBuilder($archiveBuilder);
            $this->dispatch(TheliaEvents::BEFORE_EXPORT, $event);

            $formattedContent = $formatter->encode($data);

            if ($includeImages && $handler instanceof ImagesExportInterface) {
                $this->processExportImages($handler, $archiveBuilder);
            }

            if ($includeDocuments && $handler instanceof DocumentsExportInterface) {
                $this->processExportDocuments($handler, $archiveBuilder);
            }

            $archiveBuilder->addFileFromString(
                $formattedContent, $filename
            );

            return $archiveBuilder->buildArchiveResponse($formatter::FILENAME);
        }
    }

    /**
     * @param ImagesExportInterface $handler
     * @param AbstractArchiveBuilder $archiveBuilder
     *
     * Procedure that add images in the export's archive
     */
    protected function processExportImages(ImagesExportInterface $handler, AbstractArchiveBuilder $archiveBuilder)
    {
        foreach ($handler->getImagesPaths() as $name => $documentPath) {
            $archiveBuilder->addFile(
                $documentPath,
                $handler::IMAGES_DIRECTORY,
                is_integer($name) ? null : $name
            );
        }
    }

    /**
     * @param DocumentsExportInterface $handler
     * @param AbstractArchiveBuilder $archiveBuilder
     *
     * Procedure that add documents in the export's archive
     */
    protected function processExportDocuments(DocumentsExportInterface $handler, AbstractArchiveBuilder $archiveBuilder)
    {
        foreach ($handler->getDocumentsPaths() as $name => $documentPath) {
            $archiveBuilder->addFile(
                $documentPath,
                $handler::DOCUMENTS_DIRECTORY,
                is_integer($name) ? null : $name
            );
        }
    }

    /**
     * @param  integer  $id
     * @return Response
     *
     * This method is called when the route /admin/export/{id}
     * is called with a GET request.
     *
     * It returns a modal view if the request is an AJAX one,
     * otherwise it generates a "normal" back-office page
     */
    public function exportView($id)
    {
        if (null === $export = $this->getExport($id)) {
            return $this->render("404");
        }

        /**
         * Use the loop to inject the same vars in Smarty
         */
        $loop = new ExportLoop($this->container);

        $loop->initializeArgs([
            "export" => $export->getId()
        ]);

        $query = $loop->buildModelCriteria();
        $result= $query->find();

        $results = $loop->parseResults(
            new LoopResult($result)
        );

        $parserContext = $this->getParserContext();

        /** @var \Thelia\Core\Template\Element\LoopResultRow $row */
        foreach ($results as $row) {
            foreach ($row->getVarVal() as $name=>$value) {
                $parserContext->set($name, $value);
            }
        }

        /**
         * Inject conditions in smarty,
         * It is used to display or not the checkboxes "Include images"
         * and "Include documents"
         */
        $this->getParserContext()
            ->set("HAS_IMAGES", $export->hasImages($this->container))
            ->set("HAS_DOCUMENTS", $export->hasDocuments($this->container))
        ;

        /** Then render the form */
        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->render("ajax/export-modal");
        } else {
            return $this->render("export-page");
        }
    }


    public function changePosition($action, $id)
    {
        if (null !== $response = $this->checkAuth([AdminResources::EXPORT], [], [AccessManager::UPDATE])) {
            return $response;
        }

        $export = $this->getExport($id);

        if ($action === "up") {
            $export->upPosition();
        } elseif ($action === "down") {
            $export->downPosition();
        }

        $this->setOrders(null, "manual");

        return $this->render('export');
    }

    public function updatePosition($id, $value)
    {
        if (null !== $response = $this->checkAuth([AdminResources::EXPORT], [], [AccessManager::UPDATE])) {
            return $response;
        }

        $export = $this->getExport($id);

        $export->updatePosition($value);

        $this->setOrders(null, "manual");

        return $this->render('export');
    }

    public function changeCategoryPosition($action, $id)
    {
        if (null !== $response = $this->checkAuth([AdminResources::EXPORT], [], [AccessManager::UPDATE])) {
            return $response;
        }

        $category = $this->getCategory($id);

        if ($action === "up") {
            $category->upPosition();
        } elseif ($action === "down") {
            $category->downPosition();
        }

        $this->setOrders("manual");

        return $this->render('export');
    }

    public function updateCategoryPosition($id, $value)
    {
        if (null !== $response = $this->checkAuth([AdminResources::EXPORT], [], [AccessManager::UPDATE])) {
            return $response;
        }

        $category = $this->getCategory($id);

        $category->updatePosition($value);

        $this->setOrders("manual");

        return $this->render('export');
    }

    protected function setOrders($category = null, $export = null)
    {
        if ($category === null) {
            $category = $this->getRequest()->query->get("category_order", "manual");
        }

        if ($export === null) {
            $export = $this->getRequest()->query->get("export_order", "manual");
        }

        $this->getParserContext()
            ->set("category_order", $category)
        ;

        $this->getParserContext()
            ->set("export_order", $export)
        ;
    }

    protected function getExport($id)
    {
        $export = ExportQuery::create()->findPk($id);

        if (null === $export) {
            throw new \ErrorException(
                $this->getTranslator()->trans(
                    "There is no id \"%id\" in the exports",
                    [
                        "%id" => $id
                    ]
                )
            );
        }

        return $export;
    }

    protected function getCategory($id)
    {
        $category = ExportCategoryQuery::create()->findPk($id);

        if (null === $category) {
            throw new \ErrorException(
                $this->getTranslator()->trans(
                    "There is no id \"%id\" in the export categories",
                    [
                        "%id" => $id
                    ]
                )
            );
        }

        return $category;
    }
}
