<?php


trait thfFileTools{
	use thfHeader;
	public static function hDownFile($f,$mode=1,$downloadFilename=''){
		// $mode=0 just the mime type and the length
		// $mode=1 attachement: If you want to encourage the client to download it instead of following the default behaviour. ; 
		// $mode=2 inline: With inline, the browser will try to open the file within the browser.
		// $mode=2.5 inline with the file name header;
		// $mode=3 attachement with application/force-download
		//For example, if you have a PDF file and Firefox/Adobe Reader, an inline disposition will open the PDF within Firefox, whereas attachment will force it to download. If you're serving a .ZIP file, browsers won't be able to display it inline, so for inline and attachment dispositions, the file will be downloaded.
		if(!is_file($f)){self::h503('File not found!');exit;}
		if(!is_readable($f)){self::h503('File not readable!');exit;}
		if(!$downloadFilename){
			$downloadFilename=basename($f);//get original filename
		}
		$handle = @fopen($f, 'r');
		if(!$handle){self::h503('The requested file can\'t be open!');}

		header('Content-Type: '.get_mime_type($f));
		header('Content-Length: ' . filesize($f));
		header('Last-Modified: '.gmdate("D, d M Y H:i:s",filemtime($f) ).' GMT');
		if($mode==2){	header('Content-Disposition: inline');   }
		if($mode==2.5){	header('Content-Disposition: inline; filename="'.urlencode($downloadFilename).'"; filename*=UTF-8\'\''.rawurlencode($downloadFilename));   }
		if($mode==1 || $mode==3){
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename="'.urlencode($downloadFilename).'"; filename*=UTF-8\'\''.rawurlencode($downloadFilename));
		}
		if($mode==3){		header('Content-Type: application/force-download');   }

		while (($buffer = fgets($handle, 4096)) !== false){
			ob_start(); 	echo $buffer;		ob_end_flush();
			}
		if (!feof($handle)){self::h503("Error: unexpected file read fail\r\n");}
		fclose($handle);
		exit;
	}
	
	
	
}
