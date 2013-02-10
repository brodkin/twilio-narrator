<?php

/**
 * Narrator Markdown Reader for Twilio
 *
 * PHP Version 5.3
 *
 * @category  Controller
 * @package   Laravel
 * @author    Brodkin CyberArts <support@brodkinca.com>
 * @copyright 2012 Brodkin CyberArts.
 * @license   All rights reserved.
 * @version   GIT: $Id$
 * @link      http://narrator.pagodabox.com/
 */

use dflydev\markdown\MarkdownParser;

/**
 * Narrator Voice Controller
 *
 * @category  Controller
 * @package   Laravel
 * @author    Brodkin CyberArts <support@brodkinca.com>
 * @copyright 2012 Brodkin CyberArts.
 * @license   All rights reserved.
 * @version   GIT: $Id$
 * @link      http://narrator.pagodabox.com/
 */
class Voice extends BaseController
{
    /**
     * Source Document
     *
     * @var DomDocument
     */
    private $_doc;

    /**
     * Constructor
     */
    public function __construct()
    {
        $sid = Input::get('AccountSid');
        $incoming = Input::get('To');

        $data_raw = file_get_contents('https://raw.github.com/cappuccino/cappuccino/master/README.markdown');

        $parser = new MarkdownParser();
        $data_html = $parser->transformMarkdown($data_raw);

        $dom = new DOMDocument();
        $dom->loadHTML($data_html);

        $this->_doc = $dom;
    }

    /**
     * Return Main Menu TwiML
     *
     * @return void
     */
    public function start()
    {
        $response = new Services_Twilio_Twiml();
        $response->say('You have reached narrator, a PHP-based markdown document reader for Twilio.');
        $response->play('https://api.twilio.com/cowbell.mp3');
        $response->say('Press # to skip to the menu at any time.');
        $response->redirect('/voice/content/'.$this->_getDocTitle());
        print $response;
    }

    /**
     * Return Content TwiML
     *
     * @param mixed $h1 Heading 1
     * @param mixed $h2 Heading 2
     * @param mixed $h3 Heading 3
     * @param mixed $h4 Heading 4
     * @param mixed $h5 Heading 5
     *
     * @return void
     */
    public function content(
        $h1, $h2=false, $h3=false, $h4=false, $h5=false
    ) {
        $param_a = func_get_args();
        $content = $this->_getContent(count($param_a), end($param_a));

        $response = new Services_Twilio_Twiml();
        $gather = $response->gather(
            array(
                'action' => Request::url(),
                'method' => 'POST',
                'numDigits' => 2
            )
        );
        $gather->say($content);
        $response->play('https://api.twilio.com/cowbell.mp3');

        $url = '/voice/menu/'.implode('/', $param_a);
        $response->redirect($url);

        print $response;
    }

    /**
     * Return Menu TwiML
     *
     * @param mixed $h1 Heading 1
     * @param mixed $h2 Heading 2
     * @param mixed $h3 Heading 3
     * @param mixed $h4 Heading 4
     * @param mixed $h5 Heading 5
     *
     * @return void
     */
    public function menu(
        $h1, $h2=false, $h3=false, $h4=false, $h5=false
    ) {
        $param_a = func_get_args();
        $menu_a = $this->_getMenuItems(count($param_a), end($param_a));

        $response = new Services_Twilio_Twiml();

        if (count($menu_a) >= 2) {
            // Read Menu
            $gather = $response->gather(
                array(
                    'action' => '/voice/process/'.implode('/', $param_a),
                    'method' => 'POST',
                    'numDigits' => 2
                )
            );
            $gather->say('To return to the previous menu, press star.');
            foreach ($menu_a as $key => $option) {
                $gather->say('For '.$option.', press '.$key.'.');
            }
            $gather->say('To return to the main menu, press pound.');
        } elseif (count($menu_a) === 1) {
            // Redirect to Content
            $url = '/voice/content/'.implode('/', $param_a).'/'.end($menu_a);

            return Redirect::to($url);
        } else {
            // Error
            $response->say('This section has no further options.');
            $response->say('Returning to previous menu.');
            $response->redirect('..');
        }
        $response->redirect('/voice');
        print $response;
    }

    /**
     * Process Menu Request
     *
     * @param mixed $h1 Heading 1
     * @param mixed $h2 Heading 2
     * @param mixed $h3 Heading 3
     * @param mixed $h4 Heading 4
     * @param mixed $h5 Heading 5
     *
     * @return void
     */
    public function processMenu(
        $h1, $h2=false, $h3=false, $h4=false, $h5=false
    ) {
        $param_a = func_get_args();
        $menu_a = $this->_getMenuItems(count($param_a), end($param_a));

        $option = Input::get('Digits');

        $response = new Services_Twilio_Twiml();

        if (isset($menu_a[$option])) {
            $url = '/voice/content/'.implode('/', $param_a).'/'.$menu_a[$option];

            return Redirect::to($url);
        } elseif ($option == '*') {
            $param_a_mod = array_slice($param_a, 0, -1);
            $url = '/voice/menu/'.implode('/', $param_a_mod).'/'.$menu_a[$option];
            $response->say('Previous menu.');
            $response->redirect($url);
        }
        print $response;
    }

    /**
     * Get Array of Menu Items for a Subheading
     *
     * @param integer $heading Heading number
     * @param string  $value   Header text
     *
     * @return array
     */
    private function _getMenuItems($heading, $value)
    {
        $source = $this->_doc->saveHTML();

        $start_node = $this->_getNode($heading, $value);
        $start_str = $start_node->ownerDocument->saveHTML($start_node);
        $start_pos = strpos($source, $start_str);

        $content_partial = substr($source, $start_pos+strlen($start_str));

        if ($end_pos = strpos($content_partial, '<h'.$heading)) {
            $content_src = substr($content_partial, 0, $end_pos);
        } else {
            $content_src = $content_partial;
        }

        $dom = new DOMDocument();
        $dom->loadHTML($content_src);
        $xpath = new DOMXpath($dom);

        $headings_nodes = $xpath->query('//h'.($heading+1));

        $headings_a = array();
        foreach ($headings_nodes as $key => $node) {
            $headings_a[$key+1] = $node->textContent;
        }

        return $headings_a;
    }

    /**
     * Get Content for a Subheading
     *
     * @param integer $heading Heading number
     * @param string  $value   Header text
     *
     * @return string
     */
    private function _getContent($heading, $value)
    {
        $source = $this->_doc->saveHTML();

        $start_node = $this->_getNode($heading, $value);
        $start_str = $start_node->ownerDocument->saveHTML($start_node);
        $start_pos = strpos($source, $start_str);

        $content_partial = substr($source, $start_pos);

        $end_pos = strpos($content_partial, '<h', 4);

        $content_src = substr($content_partial, 0, $end_pos);

        $content = new DOMDocument();
        $content->loadHTML($content_src);

        return $content->textContent;
    }

    /**
     * Get Title of Document
     *
     * @return string
     */
    private function _getDocTitle()
    {
        return $this->_doc->getElementsByTagName('h1')->item(0)->textContent;
    }

    /**
     * Get HTML Node
     *
     * @param integer $heading Heading number
     * @param string  $value   Header text
     *
     * @return DOMElement
     */
    private function _getNode($heading, $value)
    {
        $xpath = new DOMXpath($this->_doc);
        $headings = $xpath->query('//h'.$heading);

        if (count($headings) > 0) {
            foreach ($headings as $node) {
                if ($node->textContent == $value) {
                    return $node;
                }
            }
        }

        return false;
    }

}
