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

namespace Thelia\Core;

/**
 * Root class of Thelia
 *
 * It extends Symfony\Component\HttpKernel\Kernel for changing some features
 *
 *
 * @author Manuel Raynaud <manu@raynaud.io>
 */

use Propel\Runtime\Connection\ConnectionManagerSingle;
use Propel\Runtime\Connection\ConnectionWrapper;
use Propel\Runtime\Propel;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Debug\Debug;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Yaml;
use Thelia\Config\DatabaseConfiguration;
use Thelia\Config\DefinePropel;
use Thelia\Core\DependencyInjection\Loader\XmlFileLoader;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Template\ParserInterface;
use Thelia\Core\Template\TemplateDefinition;
use Thelia\Core\Translation\Translator;
use Thelia\Log\Tlog;
use Thelia\Model\Map\ProductTableMap;
use Thelia\Model\Module;
use Thelia\Model\ModuleQuery;

class Thelia extends Kernel
{
    const THELIA_VERSION = '2.3.6';

    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);

        if ($debug) {
            Debug::enable();
        }

        $this->initPropel();
    }

    public static function isInstalled()
    {
        return file_exists(THELIA_CONF_DIR . 'database.yml');
    }

    protected function initPropel()
    {
        if (self::isInstalled() === false) {
            return ;
        }

        /** @var \Propel\Runtime\ServiceContainer\StandardServiceContainer $serviceContainer */
        $serviceContainer = Propel::getServiceContainer();
        $serviceContainer->setDefaultDatasource('thelia');

        $manager = new ConnectionManagerSingle();
        $manager->setConfiguration($this->getPropelConfig());
        $manager->setName('thelia');

        $serviceContainer->setConnectionManager('thelia', $manager);
        $serviceContainer->setAdapterClass('thelia', 'mysql');

        /** @var ConnectionWrapper $con */
        $con = Propel::getConnection(ProductTableMap::DATABASE_NAME);
        $con->setAttribute(ConnectionWrapper::PROPEL_ATTR_CACHE_PREPARES, true);

        if ($this->isDebug()) {
            // In debug mode, we have to initialize Tlog at this point, as this class uses Propel
            Tlog::getInstance()->setLevel(Tlog::DEBUG);

            $serviceContainer->setLogger('defaultLogger', Tlog::getInstance());
            $con->useDebug(true);
        }

        $this->checkMySQLConfigurations($con);
    }

    protected function checkMySQLConfigurations(ConnectionWrapper $con)
    {
        // todo add cache for this test
        $result = $con->query("SELECT VERSION() as version, @@SESSION.sql_mode as session_sql_mode");

        if ($result && $data = $result->fetch(\PDO::FETCH_ASSOC)) {
            $sessionSqlMode = explode(',', $data['session_sql_mode']);
            if (empty($sessionSqlMode[0])) {
                unset($sessionSqlMode[0]);
            }
            $canUpdate = false;

            // MariaDB is not impacted by this problem
            if (false === strpos($data['version'], 'MariaDB')) {
                // MySQL 5.6+ compatibility
                if (version_compare($data['version'], '5.6.0', '>=')) {
                    // add NO_ENGINE_SUBSTITUTION
                    if (!in_array('NO_ENGINE_SUBSTITUTION', $sessionSqlMode)) {
                        $sessionSqlMode[] = 'NO_ENGINE_SUBSTITUTION';
                        $canUpdate = true;
                        Tlog::getInstance()->addWarning("Add sql_mode NO_ENGINE_SUBSTITUTION. Please configure your MySQL server.");
                    }

                    // remove STRICT_TRANS_TABLES
                    if (($key = array_search('STRICT_TRANS_TABLES', $sessionSqlMode)) !== false) {
                        unset($sessionSqlMode[$key]);
                        $canUpdate = true;
                        Tlog::getInstance()->addWarning("Remove sql_mode STRICT_TRANS_TABLES. Please configure your MySQL server.");
                    }

                    // remove ONLY_FULL_GROUP_BY
                    if (($key = array_search('ONLY_FULL_GROUP_BY', $sessionSqlMode)) !== false) {
                        unset($sessionSqlMode[$key]);
                        $canUpdate = true;
                        Tlog::getInstance()->addWarning("Remove sql_mode ONLY_FULL_GROUP_BY. Please configure your MySQL server.");
                    }

                    // remove STRICT_ALL_TABLES, the scheme has been fixed in version 2.4 of Thelia
                    if (version_compare(Thelia::THELIA_VERSION, '2.4', '<')) {
                        if (($key = array_search('STRICT_ALL_TABLES', $sessionSqlMode)) !== false) {
                            unset($sessionSqlMode[$key]);
                            $canUpdate = true;
                            Tlog::getInstance()->addWarning("Remove sql_mode STRICT_ALL_TABLES. Please configure your MySQL server or update your Thelia on version 2.4 or higher.");
                        }
                    }
                }
            } else {
                // MariaDB 10.2.4+ compatibility
                if (version_compare($data['version'], '10.2.4', '>=')) {
                    // remove STRICT_TRANS_TABLES
                    if (($key = array_search('STRICT_TRANS_TABLES', $sessionSqlMode)) !== false) {
                        unset($sessionSqlMode[$key]);
                        $canUpdate = true;
                        Tlog::getInstance()->addWarning("Remove sql_mode STRICT_TRANS_TABLES. Please configure your MySQL server.");
                    }
                }

                if (version_compare($data['version'], '10.1.7', '>=')) {
                    if (!in_array('NO_ENGINE_SUBSTITUTION', $sessionSqlMode)) {
                        $sessionSqlMode[] = 'NO_ENGINE_SUBSTITUTION';
                        $canUpdate = true;
                        Tlog::getInstance()->addWarning("Add sql_mode NO_ENGINE_SUBSTITUTION. Please configure your MySQL server.");
                    }
                }
            }

            if (! empty($canUpdate)) {
                if (null === $con->query("SET SESSION sql_mode='" . implode(',', $sessionSqlMode) . "';")) {
                    throw new \RuntimeException('Failed to set MySQL global and session sql_mode');
                }
            }
        } else {
            Tlog::getInstance()->addWarning("Failed to get MySQL version and sql_mode");
        }
    }

    /**
     * Gets the container's base class.
     *
     * All names except Container must be fully qualified.
     *
     * @return string
     */
    protected function getContainerBaseClass()
    {
        return '\Thelia\Core\DependencyInjection\TheliaContainer';
    }

    /**
     *
     * process the configuration and create a cache.
     *
     * @return array configuration for propel
     */
    protected function getPropelConfig()
    {
        $cachePath = $this->getCacheDir() . DS . 'PropelConfig.php';

        $cache = new ConfigCache($cachePath, $this->debug);

        if (!$cache->isFresh()) {
            if (file_exists(THELIA_CONF_DIR."database_".$this->environment.".yml")) {
                $file = THELIA_CONF_DIR."database_".$this->environment.".yml";
            } else {
                $file = THELIA_CONF_DIR . 'database.yml';
            }

            $definePropel = new DefinePropel(
                new DatabaseConfiguration(),
                Yaml::parse(file_get_contents($file)),
                $this->getEnvParameters()
            );

            $resource = [
                new FileResource($file)
            ];

            $config = $definePropel->getConfig();

            $code = sprintf("<?php return %s;", var_export($config, true));

            $cache->write($code, $resource);
        }

        $config = require $cachePath;

        return $config;
    }

    /**
     * dispatch an event when application is boot
     */
    public function boot()
    {
        parent::boot();

        if (self::isInstalled()) {
            $this->getContainer()->get("event_dispatcher")->dispatch(TheliaEvents::BOOT);
        }
    }

    /**
     * Add all module's standard templates to the parser environment
     *
     * @param ParserInterface $parser the parser
     * @param Module          $module the Module.
     */
    protected function addStandardModuleTemplatesToParserEnvironment($parser, $module)
    {
        $stdTpls = TemplateDefinition::getStandardTemplatesSubdirsIterator();

        foreach ($stdTpls as $templateType => $templateSubdirName) {
            $this->addModuleTemplateToParserEnvironment($parser, $module, $templateType, $templateSubdirName);
        }
    }

    /**
     * Add a module template directory to the parser environment
     *
     * @param ParserInterface $parser             the parser
     * @param Module          $module             the Module.
     * @param string          $templateType       the template type (one of the TemplateDefinition type constants)
     * @param string          $templateSubdirName the template subdirectory name (one of the TemplateDefinition::XXX_SUBDIR constants)
     */
    protected function addModuleTemplateToParserEnvironment($parser, $module, $templateType, $templateSubdirName)
    {
        // Get template path
        $templateDirectory = $module->getAbsoluteTemplateDirectoryPath($templateSubdirName);
        try {
            $templateDirBrowser = new \DirectoryIterator($templateDirectory);

            $code = ucfirst($module->getCode());

            /* browse the directory */
            foreach ($templateDirBrowser as $templateDirContent) {
                /* is it a directory which is not . or .. ? */
                if ($templateDirContent->isDir() && ! $templateDirContent->isDot()) {
                    $parser->addMethodCall(
                        'addTemplateDirectory',
                        array(
                            $templateType,
                            $templateDirContent->getFilename(),
                            $templateDirContent->getPathName(),
                            $code
                        )
                    );
                }
            }
        } catch (\UnexpectedValueException $ex) {
            // The directory does not exists, ignore it.
        }
    }

    /**
     *
     * Load some configuration
     * Initialize all plugins
     *
     */
    protected function loadConfiguration(ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . "/../Config/Resources"));
        $finder = Finder::create()
            ->name('*.xml')
            ->depth(0)
            ->in(__DIR__ . "/../Config/Resources");

        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {
            $loader->load($file->getBaseName());
        }

        if (defined("THELIA_INSTALL_MODE") === false) {
            $modules = ModuleQuery::getActivated();

            $translationDirs = array();



            /** @var Module $module */
            foreach ($modules as $module) {
                try {
                    $definition = new Definition();
                    $definition->setClass($module->getFullNamespace());
                    $definition->addMethodCall("setContainer", array(new Reference('service_container')));

                    $container->setDefinition(
                        "module." . $module->getCode(),
                        $definition
                    );

                    $compilers = call_user_func(array($module->getFullNamespace(), 'getCompilers'));

                    foreach ($compilers as $compiler) {
                        if (is_array($compiler)) {
                            $container->addCompilerPass($compiler[0], $compiler[1]);
                        } else {
                            $container->addCompilerPass($compiler);
                        }
                    }

                    $loader = new XmlFileLoader($container, new FileLocator($module->getAbsoluteConfigPath()));
                    $loader->load("config.xml", "module." . $module->getCode());

                    $envConfigFileName = sprintf("config_%s.xml", $this->environment);
                    $envConfigFile = sprintf('%s%s%s', $module->getAbsoluteConfigPath(), DS, $envConfigFileName);

                    if (is_file($envConfigFile) && is_readable($envConfigFile)) {
                        $loader->load($envConfigFileName, "module." . $module->getCode());
                    }
                } catch (\Exception $e) {
                    Tlog::getInstance()->addError(
                        sprintf("Failed to load module %s: %s", $module->getCode(), $e->getMessage()),
                        $e
                    );
                }
            }

            /** @var ParserInterface $parser */
            $parser = $container->getDefinition('thelia.parser');

            /** @var \Thelia\Core\Template\TemplateHelperInterface $templateHelper */
            $templateHelper = $container->get('thelia.template_helper');

            /** @var Module $module */
            foreach ($modules as $module) {
                try {
                    $this->loadModuleTranslationDirectories($module, $translationDirs, $templateHelper);

                    $this->addStandardModuleTemplatesToParserEnvironment($parser, $module);
                } catch (\Exception $e) {
                    Tlog::getInstance()->addError(
                        sprintf("Failed to load module %s: %s", $module->getCode(), $e->getMessage()),
                        $e
                    );
                }
            }

            // Load core translation
            $translationDirs['core'] = THELIA_LIB . 'Config' . DS . 'I18n';

            // Load core translation
            $translationDirs[Translator::GLOBAL_FALLBACK_DOMAIN] = THELIA_LOCAL_DIR . 'I18n';

            // Standard templates (front, back, pdf, mail)

            /** @var TemplateDefinition $templateDefinition */
            foreach ($templateHelper->getStandardTemplateDefinitions() as $templateDefinition) {
                if (is_dir($dir = $templateDefinition->getAbsoluteI18nPath())) {
                    $translationDirs[$templateDefinition->getTranslationDomain()] = $dir;
                }
            }

            if ($translationDirs) {
                $this->loadTranslation($container, $translationDirs);
            }
        }
    }

    private function loadModuleTranslationDirectories(Module $module, array &$translationDirs, $templateHelper)
    {
        // Core module translation
        if (is_dir($dir = $module->getAbsoluteI18nPath())) {
            $translationDirs[$module->getTranslationDomain()] = $dir;
        }

        // Admin includes translation
        if (is_dir($dir = $module->getAbsoluteAdminIncludesI18nPath())) {
            $translationDirs[$module->getAdminIncludesTranslationDomain()] = $dir;
        }

        // Module back-office template, if any
        $templates =
            $templateHelper->getList(
                TemplateDefinition::BACK_OFFICE,
                $module->getAbsoluteTemplateBasePath()
            );

        foreach ($templates as $template) {
            $translationDirs[$module->getBackOfficeTemplateTranslationDomain($template->getName())] =
                $module->getAbsoluteBackOfficeI18nTemplatePath($template->getName());
        }

        // Module front-office template, if any
        $templates =
            $templateHelper->getList(
                TemplateDefinition::FRONT_OFFICE,
                $module->getAbsoluteTemplateBasePath()
            );

        foreach ($templates as $template) {
            $translationDirs[$module->getFrontOfficeTemplateTranslationDomain($template->getName())] =
                $module->getAbsoluteFrontOfficeI18nTemplatePath($template->getName());
        }

        // Module pdf template, if any
        $templates =
            $templateHelper->getList(
                TemplateDefinition::PDF,
                $module->getAbsoluteTemplateBasePath()
            );

        foreach ($templates as $template) {
            $translationDirs[$module->getPdfTemplateTranslationDomain($template->getName())] =
                $module->getAbsolutePdfI18nTemplatePath($template->getName());
        }

        // Module email template, if any
        $templates =
            $templateHelper->getList(
                TemplateDefinition::EMAIL,
                $module->getAbsoluteTemplateBasePath()
            );

        foreach ($templates as $template) {
            $translationDirs[$module->getEmailTemplateTranslationDomain($template->getName())] =
                $module->getAbsoluteEmailI18nTemplatePath($template->getName());
        }
    }

    private function loadTranslation(ContainerBuilder $container, array $dirs)
    {
        $translator = $container->getDefinition('thelia.translator');

        foreach ($dirs as $domain => $dir) {
            try {
                $finder = Finder::create()
                    ->files()
                    ->depth(0)
                    ->in($dir);

                /** @var \DirectoryIterator $file */
                foreach ($finder as $file) {
                    list($locale, $format) = explode('.', $file->getBaseName(), 2);

                    $translator->addMethodCall('addResource', array($format, (string) $file, $locale, $domain));
                }
            } catch (\InvalidArgumentException $ex) {
                // Ignore missing I18n directories
                Tlog::getInstance()->addWarning("loadTranslation: missing $dir directory");
            }
        }
    }

    /**
     *
     * initialize session in Request object
     *
     * All param must be change in Config table
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */

    /**
     * Gets a new ContainerBuilder instance used to build the service container.
     *
     * @return ContainerBuilder
     */
    protected function getContainerBuilder()
    {
        return new TheliaContainerBuilder(new ParameterBag($this->getKernelParameters()));
    }

    /**
     * Builds the service container.
     *
     * @return ContainerBuilder The compiled service container
     *
     * @throws \RuntimeException
     */
    protected function buildContainer()
    {
        $container = parent::buildContainer();

        $this->loadConfiguration($container);
        $container->customCompile();

        return $container;
    }

    /**
     * Gets the cache directory.
     *
     * @return string The cache directory
     *
     * @api
     */
    public function getCacheDir()
    {
        if (defined('THELIA_ROOT')) {
            return THELIA_CACHE_DIR . $this->environment;
        } else {
            return parent::getCacheDir();
        }
    }

    /**
     * Gets the log directory.
     *
     * @return string The log directory
     *
     * @api
     */
    public function getLogDir()
    {
        if (defined('THELIA_ROOT')) {
            return THELIA_LOG_DIR;
        } else {
            return parent::getLogDir();
        }
    }

    /**
     * Returns the kernel parameters.
     *
     * @return array An array of kernel parameters
     */
    protected function getKernelParameters()
    {
        $parameters = parent::getKernelParameters();

        $parameters["thelia.root_dir"] = THELIA_ROOT;
        $parameters["thelia.core_dir"] = dirname(__DIR__); // This class is in core/lib/Thelia/Core.
        $parameters["thelia.module_dir"] = THELIA_MODULE_DIR;

        return $parameters;
    }

    /**
     * return available bundle
     *
     * Part of Symfony\Component\HttpKernel\KernelInterface
     *
     * @return array An array of bundle instances.
     *
     */
    public function registerBundles()
    {
        $bundles = array(
            /* TheliaBundle contain all the dependency injection description */
            new Bundle\TheliaBundle(),
        );

        /**
         * OTHER CORE BUNDLE CAN BE DECLARE HERE AND INITIALIZE WITH SPECIFIC CONFIGURATION
         *
         * HOW TO DECLARE OTHER BUNDLE ? ETC
         */

        return $bundles;
    }

    /**
     * Loads the container configuration
     *
     * part of Symfony\Component\HttpKernel\KernelInterface
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     *
     * @api
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        //Nothing is load here but it's possible to load container configuration here.
        //exemple in sf2 : $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
