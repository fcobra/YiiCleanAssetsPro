<?php

# пример использования
# php /cconsoleyiicommands.php cleanassets --exclude=/_thumbnails/catalog/,/_thumbnails/catalogstructure/,.gitignore  --auto

class YiiCleanAssetsProCommand extends CConsoleCommand {


    protected $_delete_list = array();
    protected $_exclude_list = array('full_path' => array(), 'mask'=>array());
    protected $asset_base_path = "";

    public function actionIndex($auto=false,$dir="",$exclude=null) {

        defined('DS') or define('DS',DIRECTORY_SEPARATOR);

        if($exclude){

            foreach (explode(",", $exclude) as $item) {
                if(substr($item, 0, 1) == DS)
                    $this->_exclude_list['full_path'][] = $item;
                else
                    $this->_exclude_list['mask'][] = $item;
            }
        }

		if(empty($dir)) {
			$AM  = new CAssetManager;
			$this->asset_base_path = $AM->getBasePath();
		} else {
            $this->asset_base_path = $dir;
        }

		if(file_exists($this->asset_base_path))
		{
            # создадим список папок, подлежащих удалению
            $this->_delete_list = $this->GetFolderList($this->asset_base_path.DS);

			if(!$auto)
			{
				echo "Assets path is ".$this->asset_base_path.PHP_EOL;
				echo "Directory structure:".PHP_EOL;

				foreach ($this->_delete_list as $item)
				{
					echo date("Y-m-d    H:i:s",filectime($item['full_path']))."    ";
					if($item['is_dir']){
						echo "<DIR>                ";
					} else {
						echo "        ";
						echo sprintf("% 12s  ",number_format(filesize($item['full_path']),0,'.',' '));
					}
                    echo $item['name'];
                    echo PHP_EOL;
				}
				echo "Do you want to clean assets folder? (Y/N) ";
				$handle = fopen ("php://stdin","r");
				$line = fgets($handle);
				if(strtoupper(trim($line)) !== 'Y'){
					echo "Aborted".PHP_EOL;
					exit;
				}
				fclose($handle);
			}

			foreach($this->_delete_list as $item_delete)
			{
				if($item_delete['is_dir']){
					$this->rmDirRec($item_delete);
				} else {
					unlink($item_delete['full_path']);
				}
			}
			if(!$auto) echo "finished!".PHP_EOL;
		} else {
			echo "Assets directory not exists";

		}
		Yii::app()->end();
    }
	
	private function rmDirRec($item_delete)
    {
        $no_delete = false;

        $objs = $this->GetFolderList($item_delete['full_path']);

        if($objs){

            foreach($objs as $obj){
                if($obj['is_dir']){
                    $no_delete = $this->rmDirRec($obj);
                } else unlink($obj['full_path']);
            }
        }

        if(!in_array(substr($item_delete['full_path'], strlen($this->asset_base_path)), $this->_exclude_list['full_path'] ) && !$this->GetFolderList($item_delete['full_path']))
            rmdir($item_delete['full_path']);

        return $no_delete;
    }

    protected function GetFolderList($path){

        if( substr($path, -1) != DS )
            $path .= DS;

        $ret_array = array();

        $items = scandir($path);

        foreach ($items as $name) {

            if(in_array($name, CMap::mergeArray(array('.','..'), $this->_exclude_list['mask'])))
                continue;

            $ret_array[] =  array(
                'full_path' => $path.$name . (is_dir($path.$name) ? DS : null),
                'name' => $name,
                'is_dir' => (is_dir($path.$name) ? true : false)
            );
        }

        return $ret_array;
    }
	
	public function getHelp()
	{
		echo 'console command to clean '.DIRECTORY_SEPARATOR.'assets folder'.PHP_EOL;
		echo 'Usage: yiic cleanassets [--auto] [--dir=/path/to/dir]'.PHP_EOL;
		echo PHP_EOL.'--auto makes script to run in silent mode.'.PHP_EOL;
		echo 'Warning! Confirmation question will not be asked!'.PHP_EOL;
		echo PHP_EOL.'--dir=/path/to/dir you can specify dir to clean.'.PHP_EOL;
		echo PHP_EOL.'--exclude=file1,/dir1/dir2_in_dir_1/,file2 you can specify folders, which no must be deleted.'.PHP_EOL;
		echo 'If blank - sustem default path will be applied'.PHP_EOL;
		echo 'Warning! Be carefull with this param not to loose data!'.PHP_EOL;
	}
}
?>