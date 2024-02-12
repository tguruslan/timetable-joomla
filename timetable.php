<?php
// No direct access
defined('_JEXEC') or die;

class plgContentTimetable extends JPlugin
{
    public function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        if (strpos($article->text, '{tmtbl_stud}') !== false) {
            $article->text = $this->processShortcodes($article->text);
        }
    }

    private function processShortcodes($content)
    {
        $pattern = '/\[tmtbl_stud(.*?)\]/i';
        preg_match_all($pattern, $content, $matches);

        if (!empty($matches[0])) {
            foreach ($matches[0] as $key => $match) {
                $shortcode = $matches[0][$key];
                $attributes = shortcode_parse_atts($matches[1][$key]);

                // Extract shortcode attributes
                $faculty_id = isset($attributes['faculty_id']) ? $attributes['faculty_id'] : '';
                $okr = isset($attributes['okr']) ? $attributes['okr'] : '';
                $education_form_id = isset($attributes['education_form_id']) ? $attributes['education_form_id'] : '';
                $lang = isset($attributes['lang']) ? $attributes['lang'] : '';

                // Enqueue scripts and styles (use Joomla methods)
                JHtml::_('script', 'https://unpkg.com/slim-select@latest/dist/slimselect.min.js');
                JHtml::_('stylesheet', 'https://unpkg.com/slim-select@latest/dist/slimselect.css');
                JHtml::_('stylesheet', 'https://rozklad.udpu.edu.ua/css/stud.css');
                JHtml::_('script', 'https://rozklad.udpu.edu.ua/js/stud.js', array('slimselect'), null);

                // Build HTML for the shortcode
                $hide = [];
                if ($faculty_id) {
                    array_push($hide, "faculty_id");
                }
                if ($education_form_id) {
                    array_push($hide, "education_form_id");
                }
                if ($okr) {
                    array_push($hide, "okr");
                }

                $html = '<div id="timetable" hide="' . implode(';', $hide) . '" faculty_id="' . $faculty_id . '" education_form_id="' . $education_form_id . '" okr="' . $okr . '" lang="' . $lang . '"></div>';

                // Replace the shortcode in the content
                $content = str_replace($shortcode, $html, $content);
            }
        }

        return $content;
    }
}
