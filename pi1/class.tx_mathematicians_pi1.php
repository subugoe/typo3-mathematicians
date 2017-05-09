<?php

declare(strict_types=1);

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 muehlhoelzer <mmuehlh@sub.uni-goettingen.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Plugin 'mathematicians' for the 'mathematicians' extension.
 */
class tx_mathematicians_pi1 extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin
{
    /**
     * @var string
     */
    public $prefixId = 'tx_mathematicians_pi1';

    /**
     * @var string
     */
    public $scriptRelPath = 'pi1/class.tx_mathematicians_pi1.php';

    /**
     * @var string
     */
    public $extKey = 'mathematicians';

    /**
     * @var bool
     */
    public $pi_checkCHash = true;

    /**
     * @var string
     */
    protected $templateCode;

    /**
     * @var \TYPO3\CMS\Fluid\View\StandaloneView
     */
    protected $view;

    /**
     * The main method of the PlugIn.
     *
     * @param string $content The PlugIn content
     * @param array  $conf    The PlugIn configuration
     *
     * @return string The content that is displayed on the website
     */
    public function main($content, $conf): string
    {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        $this->pi_USER_INT_obj = 1;

        $this->addAssets();
        $this->view = $this->initializeTemplate();

        //generate search form
        if (\TYPO3\CMS\Core\Utility\GeneralUtility::_POST('person')) {
            $this->view->setTemplate('Search');
            $person = \TYPO3\CMS\Core\Utility\GeneralUtility::_POST('person');
            $this->view->assignMultiple([
                'searchTerm' => $person,
                'owResult' => $this->oberwolfachSearch($person),
                'genResult' => $this->genealogySearch($person),
            ]);
        } else {
            $this->view->setTemplate('Mathematicians');
        }

        $this->view->assign('language', $GLOBALS['TSFE']->sys_language_uid);

        return $this->view->render();
    }

    protected function addAssets()
    {
        /** @var \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer */
        $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);

        $pageRenderer->addJsFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('mathematicians').'Resources/Public/JavaScript/maths.js');
        $pageRenderer->addCssFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('mathematicians').'Resources/Public/Css/Mathematicians.css');
    }

    /**
     * @return \TYPO3\CMS\Fluid\View\StandaloneView
     */
    protected function initializeTemplate(): \TYPO3\CMS\Fluid\View\StandaloneView
    {
        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $template */
        $template = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Fluid\View\StandaloneView::class);
        $template->setLayoutRootPaths([\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:mathematicians/Resources/Private/Templates/Layouts/')]);
        $template->setTemplateRootPaths([\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mathematicians').'Resources/Private/Templates/']);
        $template->setPartialRootPaths([\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mathematicians').'Resources/Private/Templates/Partials/']);

        return $template;
    }

    /**
     * searches in the Oberwolfach Photo Collection.
     *
     * @param string $term search term
     *
     * @return string $result result link list
     */
    protected function oberwolfachSearch(string $term): string
    {
        $proxy = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Subugoe\Mathematicians\Proxy\OberwolfachProxy::class);

        return $proxy->search($term);
    }

    /**
     * searches in the Genealogy DB.
     *
     * @param string $term search term
     *
     * @return string $result result link list
     */
    protected function genealogySearch(string $term): string
    {
        $proxy = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Subugoe\Mathematicians\Proxy\GenealogyProxy::class);

        return $proxy->search($term);
    }

    /**
     * searches in the file mactut.txt for links containing the search term.
     *
     * @param string $term search term
     *
     * @return string $result result link list
     */
    protected function mactut_search(string $term): string
    {
        $proxy = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Subugoe\Mathematicians\Proxy\MactutProxy::class);

        return $proxy->search($term);
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mathematicians/pi1/class.tx_mathematicians_pi1.php']) {
    include_once $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mathematicians/pi1/class.tx_mathematicians_pi1.php'];
}
