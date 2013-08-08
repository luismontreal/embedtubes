<?php
// Call me like "cake sitemap sitemap"
App::uses('CakeTime', 'Utility');
class SitemapShell extends AppShell {
    public $uses = array('Nodes.Node');
    
	
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);
    }

	public function sitemap() {
		Configure::write('debug', 2);
		
		$this->Node->recursive = -1;
		$nodes = $this->Node->find('all', array(
			'status' => 1,
			'type' => 'video'
		));				
		
		$sitemap = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
		
		foreach ($nodes as $node) {
			$sitemap .= '<url>'."\n";
			$sitemap .= '<loc>' . 'http://www.elmacanon.com' . $node['Node']['path'] . '</loc>'."\n";
			$sitemap .= '<lastmod>' . CakeTime::toAtom($node['Node']['updated']) . '</lastmod>'."\n";
			$sitemap .= '<changefreq>yearly</changefreq>'."\n";
			$sitemap .= '</url>'."\n";
		}
    
		$sitemap .= '</urlset>'."\n";       
		
		file_put_contents(WWW_ROOT . 'sitemap.xml', $sitemap);
	}
}