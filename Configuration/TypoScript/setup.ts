
 # Module configuration
module.tx_gencrosschecker_tools_gencrosscheckergencrosschecker {
	persistence {
		storagePid = {$module.tx_gencrosschecker_gencrosschecker.persistence.storagePid}
	}
	view {
		templateRootPaths.0 = EXT:gen_crosschecker/Resources/Private/Backend/Templates/
		templateRootPaths.1 = {$module.tx_gencrosschecker_gencrosschecker.view.templateRootPath}
		partialRootPaths.0 = EXT:gen_crosschecker/Resources/Private/Backend/Partials/
		partialRootPaths.1 = {$module.tx_gencrosschecker_gencrosschecker.view.partialRootPath}
		layoutRootPaths.0 = EXT:gen_crosschecker/Resources/Private/Backend/Layouts/
		layoutRootPaths.1 = {$module.tx_gencrosschecker_gencrosschecker.view.layoutRootPath}
	}
}

