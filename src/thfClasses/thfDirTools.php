<?php


trait thfDirTools{
	//use thfHeader;

	
	public static function  getAllSubdirs($path,$level=0,$maxlevels=1){
		
		if($maxlevels==$level || !is_dir($path)){return NULL;}
		$path=rtrim($path,'\/'); //remove ending /
		
		$level++;
		$myDirectory = opendir($path);	// open this directory 
		$dirArray=array();
		while($entryName = readdir($myDirectory)) {
			if(filetype($path.$entryName)=='dir' && $entryName!='.' && $entryName!='..'){
	//			echo $entryName."\r\n";
				$dirArray[$entryName]=self::getAllSubdirs($path.$entryName.DIRECTORY_SEPARATOR,$level,$maxlevels);
				}
			}
		closedir($myDirectory);	// close directory
		if(is_array($dirArray)){ksort($dirArray);} 
		return $dirArray;
		}
	
	public static function  getDirFileList($dir_path,$filter_extension=array()){
		if(!is_dir($dir_path)){return NULL;}	$myDirectory = opendir($dir_path); $files=array();
		while($entry = readdir($myDirectory)) {
			$tf=rtrim($dir_path,'\/').DIRECTORY_SEPARATOR.$entry;		
			if($entry != '.' && $entry != '..' && is_file($tf) && is_readable($tf)){
				if(is_array($filter_extension) && count($filter_extension)){
					foreach($filter_extension as $filter){
						if(substr(strtolower($entry), - strlen($filter))==strtolower($filter)){$files[] = $entry; break;}
						}
					}
				else{$files[] = $entry;}		
				}
			}
		closedir($myDirectory); natsort($files); return $files;
		}
	public static function  hasSubdirs($dirpath){
		if(!is_dir($dirpath)){return false;}
		$dirCount=0;
		$myDirectory = opendir($dirpath);	// open this directory 
		while($entryName = readdir($myDirectory)) {
			if(filetype($dirpath.$entryName)=='dir' && $entryName!='.' && $entryName!='..'){ $dirCount++;	}
			}
		closedir($myDirectory);	return $dirCount;
		}
	
	
	public static function  thf_vers_date($time=NULL,$mod=1){
		if(!$time){$time=time();}
		if($mod==1){return date('y.m.d',$time).chr((date('H',$time)*60+date('i',$time))*(25/(60*24)) +65);}
		elseif($mod==2){return date('y.m.d',$time).chr((date('H',$time)*60+date('i',$time))*(25/(60*24)) +65).chr(date('s',$time)*(25/59) +65);}//sec
		else{return date('Y-m-d H:i:s',$time);}
		}
	public static function  dir_vers($dir_path,$return_timestamp=false,$max_subdirs=1,$date_mod=1){	$v=0;
		if(is_array($dir_path) && count($dir_path)){//array of file and dir paths
			foreach($dir_path as $i=>$path){$v=max($v,dir_vers($path,true,$max_subdirs));}
			}
		elseif(is_file($dir_path) && is_readable($dir_path)){$v=filemtime($dir_path);}
		elseif(is_dir($dir_path) && is_readable($dir_path)){
			$myDirectory = opendir($dir_path);
			while($entry = readdir($myDirectory)) {
				$tf=rtrim($dir_path,'/').'/'.$entry;		
				if($entry != '.' && $entry != '..' && is_file($tf) && is_readable($tf)){ $mtime=filemtime($tf);	$v=max($v,$mtime);	}
				elseif($entry != '.' && $entry != '..'  && is_readable($tf)  && is_dir($tf) && $max_subdirs>0){	$v=max($v,dir_vers($tf,true,($max_subdirs-1)));}
				}
			closedir($myDirectory);
			}
		if($return_timestamp){return $v;}
		return thf_vers_date($v,$date_mod);
		}

	public static function  dir_hash($dir_path,$complex=0,$max_subdirs=1){	$v=NULL;
		if(is_array($dir_path)){//array of file and dir paths
			if(count($dir_path)){foreach($dir_path as $i=>$path){$v.=dir_hash($path,$complex,$max_subdirs);}}
			}
		elseif(is_file($dir_path) && is_readable($dir_path)){
			if($complex==1){$v=md5(file_get_contents($dir_path));}
			else{$v=filesize($dir_path).basename($dir_path);}
			}
		elseif(is_dir($dir_path) && is_readable($dir_path)){
			$myDirectory = opendir($dir_path);
			while($entry = readdir($myDirectory)) {
				$tf=rtrim($dir_path,'/').'/'.$entry;
				if($entry != '.' && $entry != '..' && is_file($tf) && is_readable($tf)){						$v.=dir_hash($tf,$complex);}
				elseif($entry != '.' && $entry != '..'  && is_readable($tf)  && is_dir($tf) && $max_subdirs>0){	$v.=dir_hash($tf,$complex,$max_subdirs-1);}
				}
			closedir($myDirectory);
			}

		if($v){return md5($v);}	return NULL;
		}
	
	
}
