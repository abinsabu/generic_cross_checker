<?php
namespace GENE\GenCrosschecker\Controller;

use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * *************************************************************
 *
 * Copyright notice
 *
 * (c) 2020
 *
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 * *************************************************************
 */

/**
 * ContentCheckerController
 */
class ContentCheckerController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * contentCheckerRepository
     *
     * @var \GENE\GenCrosschecker\Domain\Repository\ContentCheckerRepository
     *
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $contentCheckerRepository = null;

    /**
     * remoteDatabseObject
     *
     * @var TYPO3\\CMS\\Core\\Database\\DatabaseConnection
     */
    protected $remoteDatabseObject = null;

    /**
     * extensionConfiguration
     *
     * @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility 
     */
    protected $extensionConfiguration = null;

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $dbList = [];
        $this->getConfiguartions();
        $connectionStatus = 0;
        $this->remoteDatabseObject = $this->contentCheckerRepository->getConnected($this->extensionConfiguration);
        if ($this->remoteDatabseObject) {
            $dbList = $this->contentCheckerRepository->doGetTableList(
                $this->remoteDatabseObject,
                $this->extensionConfiguration
            );
            $this->doGetFilterForm();
            $connectionStatus = 1;
        } else {
            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                    'mlang_labels_flashMessage',
                    'gen_crosschecker'
                ),
                $messageTitle = '',
                $severity = \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR,
                $storeInSession = false
            );
        }
        $this->view->assignMultiple(
            [
                'dbList' => $dbList,
                'connectionStatus' => $connectionStatus
            ]
        );
    }

    /**
     * action list
     *
     * @return void
     */
    public function showAction()
    {
        $filterFormArr = $this->request->getArguments();
        $this->getConfiguartions();
        $connectionStatus = 0;
        $this->remoteDatabseObject = $this->contentCheckerRepository->getConnected($this->extensionConfiguration);
        if ($this->remoteDatabseObject) {
            $connectionStatus = 1;
            $dbList = $this->contentCheckerRepository->doGetTableList(
                $this->remoteDatabseObject,
                $this->extensionConfiguration
            );
            $listData = $this->contentCheckerRepository->doGetContents($this->remoteDatabseObject, $filterFormArr, $dbList);
            $this->doGetFilterForm();
        } else {
            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                    'mlang_labels_flashMessage',
                    'gen_crosschecker'
                ),
                $messageTitle = '',
                $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR,
                $storeInSession = false
            );
        }
        $this->view->assignMultiple(
            [
                'dbList' => $dbList,
                'listData' => $listData,
                'filterFormArr' => $filterFormArr,
                'connectionStatus' => $connectionStatus
            ]
        );
    }

    /**
     * doGetFilterForm
     *
     * @return Object
     */
    public function doGetFilterForm()
    {
        $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/DateTimePicker');
        return true;
    }

    /**
     * getConfiguartions
     *
     * @return boolean
     */
    public function getConfiguartions()
    {
        $this->extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['gen_crosschecker'];
    }
}
