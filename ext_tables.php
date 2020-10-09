<?php
if (! defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    
    /**
     * Registers a Backend Module
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'GENE.GenCrosschecker',
        'tools', // Make module a submodule of 'tools'
        'gencrosschecker', // Submodule key
        '', // Position
        [
        'ContentChecker' => 'list,show'
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:gen_crosschecker/ext_icon.gif',
            'labels' => 'LLL:EXT:gen_crosschecker/Resources/Private/Language/locallang_gencrosschecker.xlf'
        ]
    );
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'gen_crosschecker',
    'Configuration/TypoScript',
    'Content Cross Checker'
);