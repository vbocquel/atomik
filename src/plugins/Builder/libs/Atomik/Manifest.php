<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Reads and writes plugin's Manifest.xml files
 *
 * @package Atomik
 */
class Atomik_Manifest
{
	/**
	 * @var string
	 */
	public $authorName = '';
	
	/**
	 * @var string
	 */
	public $authorEmail = '';
	
	/**
	 * @var string
	 */
	public $authorWebsite = '';
	
	/**
	 * @var string
	 */
	public $name = '';
	
	/**
	 * @var string
	 */
	public $displayName = '';
	
	/**
	 * @var string
	 */
	public $version = '1.0';
	
	/**
	 * @var string
	 */
	public $category = 'Miscellaneous';
	
	/**
	 * @var string
	 */
	public $description = '';
	
	/**
	 * @var string
	 */
	public $longDescription = '';
	
	/**
	 * @var string
	 */
	public $link = '';
	
	/**
	 * @var string
	 */
	public $directory = '/';
	
	/**
	 * @var string
	 */
	public $minAtomikVersion = '2.2';
	
	/**
	 * Filename of the XSchema to validate Manifest.xml files
	 * 
	 * @var string
	 */
	public $schemaLocation = 'http://www.atomikframework.com/docs/manifest/manifest.xsd';
	
	/**
	 * Loads and validates a Manifest.xml file
	 * 
	 * @param 	string 	$filename
	 * @return 	boolean				Return false if the an error occur or the validation failed
	 */
	public function load($filename)
	{
		return $this->loadXml(file_get_contents($filename));
	}
	
	/**
	 * Fill the properties using an XML string. The string
	 * will be validated against the schema
	 * 
	 * @param 	string 	$string
	 * @return 	boolean				Return false if the an error occur or the validation failed
	 */
	public function loadXml($string)
	{
		$dom = new DOMDocument();
		
		try {
			@$dom->loadXml($string);
			if (!empty($this->schemaLocation) && !@$dom->schemaValidate($this->schemaLocation)) {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
		
		foreach ($dom->documentElement->childNodes as $child) {
			if ($child->nodeType == XML_ELEMENT_NODE) {
			    if ($child->localName == 'author') {
			        
			    } else if ($child->localName == 'dependencies') {
			        
			    } else {
				    $this->{$child->localName} = strip_tags(html_entity_decode($child->nodeValue));
			    }
			}
		}
		
		return true;
	}
	
	/**
	 * Fill the properties using an array
	 * 
	 * @param array $array
	 */
	public function loadArray($array)
	{
		foreach ($array as $name => $value) {
			if (property_exists($this, $name)) {
				$this->{$name} = $value;
			}
		}
	}
	
	/**
	 * Saves the properties to an XML file
	 * 
	 * @param string $filename
	 */
	public function save($filename)
	{
		file_put_contents($filename, $this->toXml());
	}
	
	/**
	 * Returns a valid XML representation of the manifest
	 * 
	 * @return string
	 */
	public function toXml()
	{
		$ns = 'http://www.atomikframework.com/manifest';
		$dom = new DOMDocument('1.0', 'utf-8');
		$dom->formatOutput = true;
		
		$root = $dom->appendChild($dom->createElementNS($ns, 'm:manifest'));
		$root->setAttribute('xmlns:m', $ns);
		
		$author = $root->appendChild($dom->createElementNS($ns, 'm:author'));
		$author->appendChild($dom->createElementNS($ns, 'm:name', $this->authorName));
		$author->appendChild($dom->createElementNS($ns, 'm:email', $this->authorEmail));
		$author->appendChild($dom->createElementNS($ns, 'm:website', $this->authorWebsite));
		
		$root->appendChild($dom->createElementNS($ns, 'm:name', $this->name));
		$root->appendChild($dom->createElementNS($ns, 'm:displayName', $this->displayName));
		$root->appendChild($dom->createElementNS($ns, 'm:minAtomikVersion', $this->minAtomikVersion));
		$root->appendChild($dom->createElementNS($ns, 'm:version', $this->version));
		$root->appendChild($dom->createElementNS($ns, 'm:category', $this->category));
		$root->appendChild($dom->createElementNS($ns, 'm:description', $this->description));
		$root->appendChild($dom->createElementNS($ns, 'm:longDescription', $this->longDescription));
		$root->appendChild($dom->createElementNS($ns, 'm:link', $this->link));
		$root->appendChild($dom->createElementNS($ns, 'm:directory', $this->directory));
		
		return $dom->saveXML();
	}
	
	/**
	 * Returns the properties as an array
	 * 
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'authorName'		=> $this->authorName,
			'authorEmail'		=> $this->authorEmail,
			'authorWebsite'		=> $this->authorWebsite,
			'name' 				=> $this->name,
			'displayName' 		=> $this->displayName,
			'version' 			=> $this->version,
			'category' 			=> $this->category,
			'description' 		=> $this->description,
			'longDescription' 	=> $this->longDescription,
			'link' 				=> $this->link,
			'directory' 		=> $this->directory,
		    'minAtomikVersion'  => $this->minAtomikVersion
		);
	}
	
	/**
	 * Returns the object as an XML string
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->toXml();
	}
}
