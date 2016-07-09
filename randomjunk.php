<?php
class RandomJunk

{
	public static $extensions = ['xlsx', 'xlsx', 'doc', 'docx', 'php', 'txt', 'css', 'js', 'psd', 'ai', 'jpg', 'png', 'zip', 'pdf', 'mp3'];

	public static $random_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	public static $count_dir = 100;

	public static $count_file = 300000;

	public static function run()
	{
		$curpath = realpath(dirname(__FILE__)) . "/junk";
		$folders = [$curpath];

		// clean up folder

		self::rrmdir($curpath);

		// prepare folders

		for ($i = 0; $i < self::$count_dir; $i++)
		{
			$rand = mt_rand(0, 2);
			if ($rand === 0)
			{
				$curpath = $curpath . "/" . self::randomName();
			}
			else
			if ($rand === 1 && $curpath != realpath(dirname(__FILE__)) . "/junk")
			{
				$curpath = substr($curpath, 0, strrpos($curpath, "/"));
			}
			else
			{
				$curpath = $curpath;
			}

			$folders[] = $curpath;
		}

		// create folders

		foreach($folders as $folder)
		{
			@mkdir($folder);
		}

		// create files in folders

		foreach($folders as $count => $folder)
		{
			for ($i = 0; $i < (self::$count_file / self::$count_dir); $i++)
			{
				$filename = $folder . "/" . self::randomName() . "." . self::randomExtension();
				$content = self::randomContent();
				file_put_contents($filename, $content);
				chmod($filename, 0777);
			}

			echo "Loading... " . round(($count / count($folders)) * 100) . "%\r";
		}
	}

	public static function randomName($length = null)
	{
		if ($length === null)
		{
			$length = mt_rand(10, 20);
		}

		$characters = self::$random_chars;
		$characters_length = strlen($characters);
		$random = '';
		for ($i = 0; $i < $length; $i++)
		{
			$random.= $characters[rand(0, $characters_length - 1) ];
		}

		return $random;
	}

	public static function randomExtension()
	{
		return self::$extensions[array_rand(self::$extensions) ];
	}

	public static function randomContent()
	{
		$content = "";
		$length = mt_rand(1, 10);
		for ($y = 0; $y < $length; $y++)
		{
			$content.= self::randomName(10);
			$content.= "\n";
		}

		return $content;
	}

	public static function rrmdir($dir)
	{
		if (is_dir($dir))
		{
			$objects = scandir($dir);
			foreach($objects as $object)
			{
				if ($object != "." && $object != "..")
				{
					if (filetype($dir . "/" . $object) == "dir") self::rrmdir($dir . "/" . $object);
					else unlink($dir . "/" . $object);
				}
			}

			reset($objects);
			rmdir($dir);
		}
	}
}

RandomJunk::run();