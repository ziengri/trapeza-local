<?php

/**
 * $Id: robots.php 8366 2012-11-07 16:30:14Z aix $
 */

/**
 * Работа с robots.txt 
 * (разнообразная ненадёжная текстовая магия вместо нормального парсера)
 */
class nc_search_robots {

    protected $start_text;
    protected $end_text = "End of auto-generated section.";
    protected $start_regexp;
    protected $end_regexp;
    protected $section_regexp;
    protected $robots_txt = array();
    protected $robots_txt_changes = array();

    /**
     * 
     */
    public function __construct() {
        $this->start_text = "Auto-generated section. Do not change. ".CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ROBOTS_DONT_CHANGE;
        $this->start_regexp = "# ".preg_quote($this->start_text, '/');
        $this->end_regexp = "# ".preg_quote($this->end_text, '/');
        $this->section_regexp = "/".
                $this->start_regexp.'\s*\n'.
                '(.*)\n'.
                $this->end_regexp.
                "\s*/s";
    }

    /**
     *
     */
    public function get_robots_txt($site_id) {
        if (!isset($this->robots_txt[$site_id])) {
            $this->robots_txt[$site_id] = nc_Core::get_object()->catalogue->get_by_id($site_id, 'Robots');
        }
        return $this->robots_txt[$site_id];
    }

    /**
     * 
     */
    public function set_robots_txt($site_id, $robots_txt) {
        $this->robots_txt[$site_id] = $robots_txt;
        $this->robots_txt_changes[$site_id] = true;
    }

    /**
     * 
     */
    public function save_robots_txt($site_id) {
        if (!$this->robots_txt_changes[$site_id]) {
            return;
        }
        $robots_txt = trim($this->robots_txt[$site_id])."\n";
        $db = nc_Core::get_object()->db;
        $db->query("UPDATE `Catalogue` 
                   SET `Robots` = '".$db->escape($robots_txt)."'
                 WHERE `Catalogue_ID` = ".(int) $site_id);
    }

    /**
     * 
     */
    public function get_directives($site_id, $which_directive = null) {
        $robots_txt = preg_split("/\r?\n/", $this->get_robots_txt($site_id));

        $directives = array(
                'allow' => array(),
                'disallow' => array(),
                'crawl-delay' => null,
        );

        $ignore = true;
        $robot_names = array("*", nc_search::get_setting('CrawlerUserAgent'));
        $line_regexp = "/^\s*(?:(?P<directive>[\w-]+)\s*:\s*(?P<value>[^#]+))?(?P<comment>\s*#.+)?$/";

        foreach ($robots_txt as $line) {
            if (!nc_preg_match($line_regexp, $line, $parts) || !isset($parts['directive'])) {
                continue; // go to next line
            }

            $directive = strtolower($parts["directive"]);
            $value = trim($parts["value"]);
            //$comment = isset($parts["comment"]) ? $parts["comment"] : "";

            if (!$ignore) {
                if ($directive == 'allow' || $directive == 'disallow') {
                    $directives[$directive][] = $value ? $value : '/';
                } else if ($directive != 'user-agent') {
                    $directives[$directive] = $value;
                }
            }

            if ($directive == 'user-agent') {
                $ignore = !in_array($value, $robot_names);
            }
        }

        return ($which_directive ? $directives[$which_directive] : $directives);
    }

    /**
     * 
     */
    public function add_directive($site_id, $directive, $at_top = true) {
        // Wikipedia: "In order to be compatible to all robots, if one wants to allow 
        // single files inside an otherwise disallowed directory, it is necessary to 
        // place the Allow directive(s) first, followed by the Disallow".

        $robots_txt = $this->get_robots_txt($site_id);

        // add section if it's not there yet
        if (!nc_preg_match($this->section_regexp, $robots_txt, $matches)) {
            $new_robots_txt = trim($robots_txt)."\n\n# $this->start_text\nUser-agent: *\n$directive\n# $this->end_text\n";
            $this->set_robots_txt($site_id, $new_robots_txt);
            return;
        }

        $section_text = $matches[0];

        // do nothing if the directive is already there:
        if (nc_preg_match('/^'.preg_quote($directive, '/').'\s*\n/m', $section_text)) {
            return;
        }

        // add to the top
        if ($at_top) {
            $new_section_text = nc_preg_replace('/User-agent: \*\s*\n/', "User-agent: *\n$directive\n", $section_text);
        }
        // add to the bottom
        else {
            $new_section_text = nc_preg_replace("/$this->end_regexp/", "$directive\n# $this->end_text", $section_text);
        }

        $new_robots_txt = str_replace($section_text, $new_section_text, $robots_txt);
        $this->set_robots_txt($site_id, $new_robots_txt);
    }

    /**
     * 
     */
    public function remove_directive($site_id, $directive_regexp) {
        $robots_txt = $this->get_robots_txt($site_id);

        if (!nc_preg_match($this->section_regexp, $robots_txt, $matches)) {
            return;
        }

        $section_text = $matches[0];
        $new_section_text = nc_preg_replace($directive_regexp, "", $section_text);

        if ($new_section_text == $section_text) {
            return;
        } // $count почему-то не работает, хз
        // remove empty section
        if (nc_preg_match("/$this->start_regexp\s*\nUser-agent: \*\s*\n$this->end_regexp/", $new_section_text)) {
            $new_section_text = "";
        }

        $new_robots_txt = str_replace($section_text, $new_section_text, $robots_txt);
        $this->set_robots_txt($site_id, $new_robots_txt);
    }

    // ---------------------------------------------------------------------------
    // EVENT LISTENERS
    // ---------------------------------------------------------------------------
    // @event addCatalogue, updateCatalogue
    public function update_site($site_id) {
        $disallow_indexing = nc_Core::get_object()->catalogue->get_by_id($site_id, 'DisallowIndexing', true);
        if ($disallow_indexing == 1) {
            $this->add_directive($site_id, "Disallow: /", false);
        } else {
            $this->remove_directive($site_id, "!Disallow: /\s*\n!u");
        }
        $this->save_robots_txt($site_id);
    }

    // @event addSubdivision, updateSubdivision
    public function update_sub($site_id, $sub_ids) {
        // $sub_id can be array
        $sub_ids = (array) $sub_ids;
        // remove old entries
        $this->delete_sub($site_id, $sub_ids);

        $nc_core = nc_Core::get_object();
        foreach ($sub_ids as $sub_id) {
            $disallow = $nc_core->db->get_var("SELECT `DisallowIndexing` 
                                           FROM `Subdivision` 
                                          WHERE `Subdivision_ID` = ".(int) $sub_id);
            if ($disallow == -1) {
                continue;
            }
            $path = $nc_core->SUB_FOLDER.$nc_core->subdivision->get_by_id($sub_id, 'Hidden_URL');
            $path = nc_search_util::encode_path($path); // encode non-latin symbols
            if ($disallow == 1) {
                $this->add_directive($site_id, "Disallow: $path #$sub_id#", false);
            } elseif ($disallow == 0) {
                $this->add_directive($site_id, "Allow: $path #$sub_id#");
            }
        }
        $this->save_robots_txt($site_id);
    }

    // @event dropSubdivision
    public function delete_sub($site_id, $sub_ids) {
        // $sub_ids can be array
        $sub_ids = (array) $sub_ids;
        foreach ($sub_ids as $sub_id) {
            $this->remove_directive($site_id, "!(?:Dis)?[Aa]llow:.+?#$sub_id#\s*\n!u");
        }
        $this->save_robots_txt($site_id);
    }

}