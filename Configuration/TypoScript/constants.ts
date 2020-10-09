
module.tx_gencrosschecker_gencrosschecker {
	view {
		# cat=module.tx_gencrosschecker_gencrosschecker/file; type=string; label=Path to template root (BE)
		templateRootPath = EXT:gen_crosschecker/Resources/Private/Backend/Templates/
		# cat=module.tx_gencrosschecker_gencrosschecker/file; type=string; label=Path to template partials (BE)
		partialRootPath = EXT:gen_crosschecker/Resources/Private/Backend/Partials/
		# cat=module.tx_gencrosschecker_gencrosschecker/file; type=string; label=Path to template layouts (BE)
		layoutRootPath = EXT:gen_crosschecker/Resources/Private/Backend/Layouts/
	}
	persistence {
		# cat=module.tx_gencrosschecker_gencrosschecker//a; type=string; label=Default storage PID
		storagePid =
	}
}
