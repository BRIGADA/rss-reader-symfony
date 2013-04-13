<?php

class readerAddTask extends sfBaseTask
{
	protected function configure()
	{
		// add your own arguments here
		$this->addArguments(array(
				new sfCommandArgument('url', sfCommandArgument::REQUIRED, 'RSS URL'),
		));

		$this->addOptions(array(
				new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
				new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
				new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
				// add your own options here
		));

		$this->namespace        = 'reader';
		$this->name             = 'add';
		$this->briefDescription = '';
		$this->detailedDescription = <<<EOF
The [reader:add|INFO] task does things.
Call it with:

  [php symfony reader:add|INFO]
EOF;
	}

	protected function execute($arguments = array(), $options = array())
	{
		// initialize the database connection
		$databaseManager = new sfDatabaseManager($this->configuration);
		$connection = $databaseManager->getDatabase($options['connection'])->getConnection();

		$channel = Doctrine::getTable('Channel')->findOneBy('url', $arguments['url']);

		if($channel === false) {
			$channel = new Channel();
			$channel->url = $arguments['url'];
		}
		
		
		libxml_use_internal_errors(true);

		$xml = simplexml_load_file($arguments['url']);
		
		if($xml && $xml->getName() == 'rss') {
			$channel->title = strval($xml->channel->title);
			$channel->link = strval($xml->channel->link);
			$channel->description = strval($xml->channel->description);
			if(isset($xml->channel->language))
			{
				$channel->language = strval($xml->channel->language);
			}
			if(isset($xml->channel->copyright))
			{
				$channel->copyright = strval($xml->channel->copyright);
			}
			if(isset($xml->channel->managingEditor))
			{
				$channel->editor = strval($xml->channel->managingEditor);
			}
			if(isset($xml->channel->webMaster))
			{
				$channel->webmaster = strval($xml->channel->webMaster);
			}
			if(isset($xml->channel->ttl))
			{
				$channel->ttl = strval($xml->channel->ttl);
			}
			if(isset($xml->channel->image))
			{
				$channel->logo_url = strval($xml->channel->image->url);
				$channel->logo_width = isset($xml->channel->image->width) ? strval($xml->channel->image->width) : 88;
				$channel->logo_height = isset($xml->channel->image->height) ? strval($xml->channel->image->height) : 31;
			}
			
			$channel->save();
			$this->logSection('channel-id', $channel->id);
			
			$tz = new DateTimeZone(date_default_timezone_get());
			foreach($xml->channel->item as $i => $node)
			{
				$query = Doctrine::getTable('Item')
					->createQuery()
					->where('channel_id = ?', $channel->id);
				
				if(isset($node->link)) $query->andWhere('link = ?', strval($node->link));
				if(isset($node->guid)) $query->andWhere('guid = ?', strval($node->guid));
				
				if($query->count() == 0)
				{

					$this->logBlock($node->title, 'INFO');
					$item = new Item();
					$item->channel_id = $channel->id;
					$item->title = strval($node->title);
					$item->description = $this->cleanHTML(strval($node->description));
					$item->link = strval($node->link);
					$item->guid = strval($node->guid);

					$dateFormat = DATE_RSS;
//					if(!preg_match('/\+\d{4}/', substr(strval($node->pubDate), -5) ))
//					{
//						$dateFormat = 'D, d M Y H:i:s e';
//					}
					$item->pubdate = date_create_from_format($dateFormat, $node->pubDate)->setTimezone($tz)->format('Y-m-d H:i:s');
//					$this->log(sprintf('%s -> %s', strval($node->pubDate), $item->pubdate), 'INFO');
					$item->save();
				}
			}
		}
		else
		{
			foreach (libxml_get_errors() as $error) {
				$this->logBlock($error->message, 'ERROR');
			}
			libxml_clear_errors();			
		}
	}
	protected function cleanHTML($content)
	{
		$html = SimpleHTML::fromString(strval($content));
		$tags = array();
		$tags['root'] = array();
		$tags['text'] = array();
		$tags['br'] = array();
		$tags['p'] = array('style');
		$tags['div'] = array('style');
		$tags['img'] = array('src', 'width', 'height', 'title', 'alt', 'border');
		$tags['a'] = array('href', 'target');
		$tags['i'] = array();
		$tags['b'] = array();
		$tags['strong'] = array();
		$tags['ol'] = array();
		$tags['ul'] = array();
		$tags['li'] = array();
		$tags['span'] = array();
		$tags['font'] = array('size');
		$tags['center'] = array();

		foreach ($html->nodes as $n => $node)
		{
			if(array_key_exists($node->tag, $tags))
			{
				foreach(array_keys($node->attr) as $i)
				{
					if(!in_array($i, $tags[$node->tag]))
					{
						$this->logSection('attr', "{$node->tag}['{$i}']");
						unset($node->attr[$i]);
					}
				}

				switch($node->tag)
				{
				case 'a':
					$node->target = '_blank';
					break;
				case 'img':
					if(!isset($node->alt))
					{
						$node->alt = '';
					}
				}

			}
			else
			{
				$this->logSection('tag', $node->tag);
				$node->outertext = '';
				unset($html->nodes[$n]);
			}
		}
		return strval($html);
	}

}

