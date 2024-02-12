<?php
// No direct access
defined('_JEXEC') or die;

class plgContentTimetable extends JPlugin
{
    public function onContentPrepare($context, &$article, &$params, $page = 0){
        if (strpos($article->text, '[tmtbl_stud') !== false) {
            $article->text = $this->processShortcodes($article->text);
        }
    }
    private function shortcode_parse_atts($text) {
        $atts = array();
        $pattern = '/(\w+)\s*=\s*[\'"]([^\'"]*)[\'"]/';
        if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
            foreach ($match as $m) {$atts[$m[1]]=$m[2];}
        }
        return $atts;
    }
    private function processShortcodes($content){
        $pattern = '/\[tmtbl_stud(.*?)\]/i';
        preg_match_all($pattern, $content, $matches);
        if (!empty($matches[0])) {
            foreach ($matches[0] as $key => $match) {
                $shortcode = $matches[0][$key];
                $attributes = $this->shortcode_parse_atts($matches[1][$key]);
                $faculty_id = isset($attributes['faculty_id']) ? $attributes['faculty_id'] : '';
                $okr = isset($attributes['okr']) ? $attributes['okr'] : '';
                $education_form_id = isset($attributes['education_form_id']) ? $attributes['education_form_id'] : '';
                $lang = isset($attributes['lang']) ? $attributes['lang'] : '';
                JHtml::_('script', 'https://unpkg.com/slim-select@latest/dist/slimselect.min.js', [], null, true);
                JHtml::_('stylesheet', 'https://unpkg.com/slim-select@latest/dist/slimselect.css');
                JHtml::_('stylesheet', 'https://rozklad.udpu.edu.ua/css/stud.css');
                JFactory::getDocument()->addScriptDeclaration('
                  document.addEventListener("DOMContentLoaded", function() {
                    var script = document.createElement("script");
                    script.src = "https://rozklad.udpu.edu.ua/js/stud.js";
                    document.body.appendChild(script);
                  });
                ');
                $hide = [];
                if ($faculty_id) {array_push($hide, "faculty_id");}
                if ($education_form_id) {array_push($hide, "education_form_id");}
                if ($okr) {array_push($hide, "okr");}
                $html = '<div id="timetable" hide="' . implode(';', $hide) . '" faculty_id="' . $faculty_id . '" education_form_id="' . $education_form_id . '" okr="' . $okr . '" lang="' . $lang . '"></div>';
                $content = str_replace($shortcode, $html, $content);
            }
        }
        return $content;
    }
}
