<?php

namespace Thelia\Model;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Router;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Files\FileModelInterface;
use Thelia\Files\FileModelParentInterface;
use Thelia\Form\BaseForm;
use Thelia\Form\Definition\AdminForm;
use Thelia\Form\FolderDocumentModification;
use Thelia\Model\Base\FolderDocument as BaseFolderDocument;
use Thelia\Model\Breadcrumb\BreadcrumbInterface;
use Thelia\Model\Breadcrumb\FolderBreadcrumbTrait;

class FolderDocument extends BaseFolderDocument implements BreadcrumbInterface, FileModelInterface
{
    use \Thelia\Model\Tools\PositionManagementTrait;
    use FolderBreadcrumbTrait;

    /**
     * Calculate next position relative to our parent
     *
     * @param FolderDocumentQuery $query
     */
    protected function addCriteriaToPositionQuery($query)
    {
        $query->filterByFolder($this->getFolder());
    }

    /**
     * {@inheritDoc}
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        parent::preInsert($con);

        $this->setPosition($this->getNextPosition());

        return true;
    }

    /**
     * @inheritdoc
     */
    public function setParentId($parentId)
    {
        $this->setFolderId($parentId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getParentId()
    {
        return $this->getFolderId();
    }

    public function preDelete(ConnectionInterface $con = null)
    {
        parent::preDelete($con);

        $this->reorderBeforeDelete(
            [
                "folder_id" => $this->getFolderId(),
            ]
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getBreadcrumb(Router $router, ContainerInterface $container, $tab, $locale)
    {
        return $this->getFolderBreadcrumb($router, $container, $tab, $locale);
    }

    /**
     * @return FileModelParentInterface the parent file model
     */
    public function getParentFileModel()
    {
        return new Folder();
    }

    /**
     * Get the ID of the form used to change this object information
     *
     * @return BaseForm the form
     */
    public function getUpdateFormId()
    {
        return AdminForm::FOLDER_DOCUMENT_MODIFICATION;
    }

    /**
     * @return string the path to the upload directory where files are stored, without final slash
     */
    public function getUploadDir()
    {
        $uploadDir = ConfigQuery::read('documents_library_path');
        if ($uploadDir === null) {
            $uploadDir = THELIA_LOCAL_DIR . 'media' . DS . 'documents';
        } else {
            $uploadDir = THELIA_ROOT . $uploadDir;
        }

        return $uploadDir . DS . 'folder';
    }

    /**
     * @return string the URL to redirect to after update from the back-office
     */
    public function getRedirectionUrl()
    {
        return '/admin/folders/update/' . $this->getFolderId();
    }

    /**
     * Get the Query instance for this object
     *
     * @return ModelCriteria
     */
    public function getQueryInstance()
    {
        return FolderDocumentQuery::create();
    }
}
