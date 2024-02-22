<?php

class nc_tpl_stylesheet_rule {

    /** @var array  */
    protected $selectors;
    /** @var  string|nc_tpl_stylesheet_rule */
    protected $declarations;

    /**
     * @param array $selectors
     * @param $declarations
     */
    public function __construct(array $selectors, $declarations) {
        $this->selectors = $selectors;
        $this->declarations = $declarations;
    }

    /**
     * @param $string
     * @return string
     */
    protected function normalize_whitespace($string) {
        return trim(preg_replace('/\s+/', ' ', $string));
    }

    /**
     * @param string $declaration
     * @param string $url_prefix
     * @return string
     */
    protected function add_url_prefix($declaration, $url_prefix) {
        if (!stripos($declaration, 'url')) {
            return $declaration;
        }

        $declaration = preg_replace(
            '#\burl\s*\(\s*([\'"]?)(?!data:)([^/\'"].+?)\1\s*\)#is',
            "url($1$url_prefix$2$1)",
            $declaration
        );

        return $declaration;
    }

    /**
     * @param string $block_class
     * @param string $url_prefix
     * @return string
     */
    public function transform($block_class, $url_prefix) {
        $selectors = array();
        $add_selectors_to_output = true;
        foreach ($this->selectors as $selector) {
            if ($selector[0] == '@') {
                if (strpos($selector, 'nc-container') === 1) {
                    $block_class .= $this->parse_container_rule($selector);
                    $add_selectors_to_output = false;
                } else if (strpos($selector, 'nc-list-object') === 1) {
                    $block_class .= ' .tpl-block-list-objects > *' . $this->parse_container_rule($selector);
                    $add_selectors_to_output = false;
                }
                $selectors[] = $this->add_url_prefix($selector, $url_prefix);
            } else if (strpos($selector, '&') !== false) {
                $selectors[] = str_replace('&', ".$block_class", $selector);
            } else {
                $selectors[] = ".$block_class $selector";
            }
        }
        $selectors = $this->normalize_whitespace(join(', ', $selectors));

        if ($this->declarations instanceof nc_tpl_stylesheet) {
            $declarations = "\n" . $this->declarations->transform($block_class, $url_prefix);
        } else {
            $declarations = $this->declarations;
            $declarations = $this->add_url_prefix($declarations, $url_prefix);
            $declarations = $this->normalize_whitespace($declarations);
        }

        if ($add_selectors_to_output) {
            return $declarations ? "$selectors { $declarations }\n" : '';
        } else {
            return $declarations . "\n";
        }
    }

    /**
     * Разбор определения правила вида "@nc-container (min-width: 123px)"
     * @param string $rule
     * @return string
     */
    protected function parse_container_rule($rule) {
        $block_selector_refinement = '';

        if (preg_match_all('/((?:min|max)-width)\s*:\s*(\d+)px/', $rule, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $block_selector_refinement .= "[$match[1]~=\"$match[2]px\"]";
            }
        }

        return $block_selector_refinement;
    }

    /**
     * @return string
     */
    public function __toString() {
        $selectors = $this->normalize_whitespace(join(', ', $this->selectors));
        $declarations = $this->normalize_whitespace($this->declarations);
        return $selectors .
               ($declarations ? " { $declarations } " : "") .
               "\n";
    }

}