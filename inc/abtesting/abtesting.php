<?php

class TIABTesting
{
    private $config;
    private $slug;

    public function __construct($slug = "")
    {
        $this->loadHooks($slug);
    }

    private function loadHooks($slug)
    {
        $this->slug     = $slug;
        $this->config   = apply_filters($this->slug . "_upsell_config", array());

        foreach ($this->config as $section=>$values) {
            add_filter($this->slug . "_" . $section . "_upsell_text", array($this, "getUpsellText"), 10, 2);
        }
    }

    public function getUpsellText($default="", $escapeHTML=false)
    {
        $filter     = current_filter();
        if (strpos($filter, $this->slug) !== false) {
            $attr       = explode("_", str_replace($this->slug . "_", "", $filter));
            if (is_array($attr) && !empty($attr)) {
                $section    = $attr[0];
                if (array_key_exists($section, $this->config)) {
                    $values     = $this->config[$section];
                    $html       = $values[rand(0, count($values) - 1)];
                    return $escapeHTML ? esc_html($html) : $html;
                }
            }
        }
        return $default;
    }

    public static function writeDebug($msg)
    {
        @mkdir(dirname(__FILE__) . "/tmp");
        file_put_contents(dirname(__FILE__) . "/tmp/log.log", date("F j, Y H:i:s", current_time("timestamp")) . " - " . $msg."\n", FILE_APPEND);
    }

}
