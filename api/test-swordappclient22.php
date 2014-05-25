<?php
    // Test the V2 PHP client implementation using the Simple SWORD Server (SSS)

	// The URL of the service document
	$testurl = "http://dspace.linti.unlp.edu.ar/sword";
	
	// The user (if required)
	$testuser = "mcharnelli@mail.linti.unlp.edu.ar";
	
	// The password of the user (if required)
	$testpw = "burbuja";
	

	// The URL of the example deposit collection
	$testdepositurl = "http://dspace.linti.unlp.edu.ar/swordv2/deposit/123456789/84";

	// The test atom entry to deposit
	$testatomentry = "test-files/atom_multipart/atom";


	// The test content zip file to deposit
	$testzipcontentfile = "test/test-files/sword-article.zip";



	// The content type of the test file
	$testcontenttype = "application/zip";

        $packageformat="http://purl.org/net/sword-types/METSDSpaceSIP";
	
	require("../swordappclient.php");
    $testsac = new SWORDAPPClient();
    
	print("Hola // // ");
	
	if (false) {
		print "AAAAAAbout to request servicedocument from " . $testurl . "\n";
		if (empty($testuser)) {
            print "As: anonymous\n";
        } else {
            print "As: " . $testuser . "\n";
        }
		$testsdr = $testsac->servicedocument($testurl, $testuser, $testpw, $testobo);
		print "Received HTTP status code: " . $testsdr->sac_status . " (" . $testsdr->sac_statusmessage . ")\n";

		if ($testsdr->sac_status == 200) {
            $testsdr->toString();
        }

        print "\n\n";
	}
	
	if (true) {
		print "AAAAbbbbout to deposit file (" . $testmultipart . ") to " . $testdepositurl . "\n";
		if (empty($testuser)) {
            print "As: anonymous\n";
        } else {
            print "As: " . $testuser . "\n";
        }
		$testdr = $testsac->deposit($testdepositurl, $testuser, $testpw, ��, $testzipcontentfile, $packageformat,$testcontenttype, false);

		print "Received HTTP status code: " . $testdr->sac_status . " (" . $testdr->sac_statusmessage . ")\n";
		
		if (($testdr->sac_status >= 200) || ($testdr->sac_status < 300)) {
            $testdr->toString();
        }

        print "\n\n";

        $edit_iri = $testdr->sac_edit_iri;
        $cont_iri = $testdr->sac_content_src;
        $edit_media = $testdr->sac_edit_media_iri;
        $statement_atom = $testdr->sac_state_iri_atom;
        $statement_ore = $testdr->sac_state_iri_ore;
    }

 

?>
