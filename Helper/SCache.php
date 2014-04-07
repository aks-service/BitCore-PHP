<?php
    
    class SCache {

	static function setCache($label, $data)
	{
		file_put_contents(ROOT.'/cache/'. preg_replace('/[^0-9a-z\.\_\-]/i','', strtolower($label)) .'.cache', '<?php $data =\''.  serialize($data).'\';');
	}

	static function getCache($label,$object = false,$time = 0)
	{
            $filename = ROOT.'/cache/' . preg_replace('/[^0-9a-z\.\_\-]/i','', strtolower($label)) .'.cache';
            if(file_exists($filename) && ($time == 0 || ($time && (filemtime($filename) + $time >= time())))){
                include $filename;
                return unserialize($data);
            }

            return null;
	}
    }
