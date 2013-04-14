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
			if(isset($xml->channel->language)) {
				$channel->language = strval($xml->channel->language);
			}
			if(isset($xml->channel->copyright)) {
				$channel->copyright = strval($xml->channel->copyright);
			}
			if(isset($xml->channel->managingEditor)) {
				$channel->editor = strval($xml->channel->managingEditor);
			}
			if(isset($xml->channel->webMaster)) {
				$channel->webmaster = strval($xml->channel->webMaster);
			}
			if(isset($xml->channel->ttl)) {
				$channel->ttl = strval($xml->channel->ttl);
			}
			if(isset($xml->channel->image)) {
				$channel->logo_url = strval($xml->channel->image->url);
				$channel->logo_width = isset($xml->channel->image->width) ? strval($xml->channel->image->width) : 88;
				$channel->logo_height = isset($xml->channel->image->height) ? strval($xml->channel->image->height) : 31;
			}
			
			$channel->save();
			
			$this->logSection('channel-id', $channel->id);
			
			$tz = new DateTimeZone(date_default_timezone_get());
			
			$i = count($xml->channel->item);
			
			while($i)
			{
				$node = $xml->channel->item[--$i];
				
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
					if(isset($node->link)) {
						$item->link = strval($node->link);
					}
					if(isset($node->guid)) {
						$item->guid = strval($node->guid);
					}
					if(isset($node->pubDate)) {
						$item->pubdate = date_create_from_format(DATE_RSS, $node->pubDate)->setTimezone($tz)->format('Y-m-d H:i:s');
					}

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
		

		foreach ($html->nodes as $n => $node)
		{
			switch($node->tag)
			{
				case 'text':
				case 'root':
				case 'br':
				case 'i':
				case 'b':
				case 'u':
				case 's':
				case 'ul':
				case 'ol':
				case 'li':
				case 'span':
				case 'strong':
				case 'strike':
				case 'table':
				case 'tr':
				case 'td':
				case 'th':
					$this->cleanHTMLAttr($node);
					break;
				case 'center':
					$this->cleanHTMLAttr($node);
					$node->style = 'text-align: center';
					$node->tag = 'div';
					break;
				case 'xml:namespace':
					$node->outertext = $node->innertext;
					break;
					
				case 'iframe':
					$this->cleanHTMLAttr($node, array('src', 'width', 'height', 'frameborder'));
					if(isset($node->src)) {
						
						$valid_src = array('http://www.liveleak.com/ll_embed?', 'http://www.youtube.com/embed/');
						foreach ($valid_src as $src) {
							if(preg_match('/^'.preg_quote($src, '/').'.*/', $node->src)) {
								break 2;
							}
						}
						$this->logSection('IFRAME', $node->src);
					}
					$this->logSection('tag', $node->tag);
					$node->outertext = '';
					break;						
					
				case 'a':
					$this->cleanHTMLAttr($node, array('href', 'target'));
					$node->quoteAll();
					$node->target = "_blank";					
					break;
					
				case 'font':
					$this->cleanHTMLAttr($node, array('size'));
					break;
					
				case 'p':
				case 'div':
					$this->cleanHTMLAttr($node, array('style'));
					break;
					
				case 'img':
					$this->cleanHTMLAttr($node, array('src', 'width', 'height', 'title', 'alt', 'border'));
					$node->quoteAll();
					if(!isset($node->alt)) {
						$node->alt = '[IMAGE]';
					}
					break;
					
				default:
					$this->logSection('tag', $node->tag);
					$node->outertext = '';
					break;
			}
		}
		return strval($html);
	}
	
	protected function cleanHTMLAttr(&$node, array $valid = array())
	{
		foreach(array_keys($node->attr) as $i)
		{
			if(!in_array($i, $valid))
			{
				$this->logSection('attr', "{$node->tag}['{$i}']");
				$node->$i = null;
			}
		}		
	}
}
