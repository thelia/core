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

namespace Thelia\Core\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Thelia\Core\Archiver\ArchiverInterface;
use Thelia\Core\Serializer\SerializerInterface;
use Thelia\ImportExport\Export\AbstractExport;

/**
 * Class ExportEvent.
 *
 * @author Jérôme Billiras <jbilliras@openstudio.fr>
 */
class ExportEvent extends Event
{
    /**
     * @var \Thelia\ImportExport\Export\AbstractExport An export
     */
    protected $export;

    /**
     * @var \Thelia\Core\Serializer\SerializerInterface A serializer interface
     */
    protected $serializer;

    /**
     * @var \Thelia\Core\Archiver\ArchiverInterface|null An archiver interface
     */
    protected $archiver;

    /**
     * @var string Path to generated export
     */
    protected $filePath;

    /**
     * Event constructor.
     *
     * @param \Thelia\ImportExport\Export\AbstractExport  $export     An export
     * @param \Thelia\Core\Serializer\SerializerInterface $serializer A serializer interface
     * @param \Thelia\Core\Archiver\ArchiverInterface     $archiver   An archiver interface
     */
    public function __construct(
        AbstractExport $export,
        SerializerInterface $serializer,
        ArchiverInterface $archiver = null
    ) {
        $this->export = $export;
        $this->serializer = $serializer;
        $this->archiver = $archiver;
    }

    /**
     * Get export.
     *
     * @return \Thelia\ImportExport\Export\AbstractExport An export
     */
    public function getExport()
    {
        return $this->export;
    }

    /**
     * Set export.
     *
     * @param \Thelia\ImportExport\Export\AbstractExport $export An export
     *
     * @return $this Return $this, allow chaining
     */
    public function setExport(AbstractExport $export): self
    {
        $this->export = $export;

        return $this;
    }

    /**
     * Get serializer.
     *
     * @return \Thelia\Core\Serializer\SerializerInterface A serializer interface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Set serializer.
     *
     * @param \Thelia\Core\Serializer\SerializerInterface $serializer A serializer interface
     *
     * @return $this Return $this, allow chaining
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * Get archiver.
     *
     * @return mixed|\Thelia\Core\Archiver\ArchiverInterface An archiver interface
     */
    public function getArchiver()
    {
        return $this->archiver;
    }

    /**
     * Set archiver.
     *
     * @param mixed|\Thelia\Core\Archiver\ArchiverInterface $archiver An archiver interface
     *
     * @return $this Return $this, allow chaining
     */
    public function setArchiver(ArchiverInterface $archiver = null)
    {
        $this->archiver = $archiver;

        return $this;
    }

    /**
     * Get export file path.
     *
     * @return string Export file path
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Set export file path.
     *
     * @param string $filePath Export file path
     *
     * @return $this Return $this, allow chaining
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }
}
