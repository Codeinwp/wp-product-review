<?php

require_once dirname(__FILE__) . "/config.php";

class ABTesting
{

    public function __construct()
    {
        $this->loadHooks();
    }

    private function loadHooks()
    {
        global $ABTESTING_PLUGIN_SLUG, $ABTESTING_CONFIG;

        foreach ($ABTESTING_CONFIG as $section=>$values) {
            add_filter($ABTESTING_PLUGIN_SLUG . "_" . $section . "_upsell_text", array($this, "getUpsellText"), 10, 2);
        }
    }

    public function getUpsellText($default, $escapeHTML=false)
    {
        global $ABTESTING_PLUGIN_SLUG, $ABTESTING_CONFIG;

        $filter     = current_filter();
        if (strpos($filter, $ABTESTING_PLUGIN_SLUG) !== false) {
            $attr       = explode("_", str_replace($ABTESTING_PLUGIN_SLUG . "_", "", $filter));
            if (is_array($attr) && !empty($attr)) {
                $section    = $attr[0];
                if (array_key_exists($section, $ABTESTING_CONFIG)) {
                    $values     = $ABTESTING_CONFIG[$section];
                    $html       = $values[rand(0, count($values) - 1)];
                    $html       = __($html, $ABTESTING_PLUGIN_SLUG);
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

$abtesting = new ABTesting();