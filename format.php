<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * XHTML+ question exporter with complete solution support.
 *
 * @package    qformat_xhtmlplus
 * @copyright  2026 Giovanni Licari
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * XHTML+ question exporter with complete metadata and solutions.
 *
 * Exports questions as static HTML with all metadata including answers,
 * feedback, scores, tags, and question types for all question types.
 *
 * @copyright  2005 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_xhtmlplus extends qformat_default {

    public function provide_export() {
        return true;
    }

    protected function repchar($text) {
        return $text;
    }

    /**
     * Render question metadata section.
     */
    protected function render_metadata($question) {
        $output = "<div class=\"question-metadata\">\n";

        $output .= "  <div class=\"meta-row\">\n";
        $output .= "    <span class=\"meta-label\">ID:</span>\n";
        $output .= "    <span class=\"meta-value\">" . s($question->id) . "</span>\n";
        $output .= "  </div>\n";

        $output .= "  <div class=\"meta-row\">\n";
        $output .= "    <span class=\"meta-label\">" . get_string('name','qformat_xhtmlplus') . ":</span>\n";
        $output .= "    <span class=\"meta-value\">" . s($question->name) . "</span>\n";
        $output .= "  </div>\n";

        $output .= "  <div class=\"meta-row\">\n";
        $output .= "    <span class=\"meta-label\">" . get_string('type','qformat_xhtmlplus') . ":</span>\n";
        $output .= "    <span class=\"meta-value\">" . s($question->qtype) . "</span>\n";
        $output .= "  </div>\n";

        if (isset($question->defaultmark) && $question->defaultmark > 0) {
            $output .= "  <div class=\"meta-row\">\n";
            $output .= "    <span class=\"meta-label\">" . get_string('defaultmark','qformat_xhtmlplus') . ":</span>\n";
            $output .= "    <span class=\"meta-value\">" . s($question->defaultmark) . "</span>\n";
            $output .= "  </div>\n";
        }

        $output .= "</div>\n";
        return $output;
    }

    /**
     * Get tags for a question.
     */
    protected function get_question_tags($question) {
        global $DB;
        $tags = $DB->get_records('tag',
            array('name' => $question->name), 'id', 'name');
        return $tags;
    }

    /**
     * Render general feedback.
     */
    protected function render_general_feedback($question) {
        if (empty($question->generalfeedback)) {
            return '';
        }

        $text = question_rewrite_question_preview_urls($question->generalfeedback, $question->id,
                $question->contextid, 'question', 'generalfeedback', $question->id,
                $question->contextid, 'qformat_xhtmlplus');

        $output = "<div class=\"general-feedback\">\n";
        $output .= "  <strong class=\"feedback-title\">" . get_string('generalfeedback', 'question') . ":</strong>\n";
        $output .= "  <div class=\"feedback-content\">\n";
        $output .= "    " . format_text($text, $question->generalfeedbackformat, array('noclean' => true)) . "\n";
        $output .= "  </div>\n";
        $output .= "</div>\n";
        return $output;
    }

    protected function writequestion($question) {
        global $OUTPUT;

        if ($question->qtype=='category') {
            return '';
        }

        $id = $question->id;
        $expout = "<!-- question: {$id}  name: {$question->name}  type: {$question->qtype} -->\n";
        $expout .= "<div class=\"question\" id=\"q-{$id}\">\n";

        // Metadata section
        $expout .= $this->render_metadata($question);

        // Question text
        $text = question_rewrite_question_preview_urls($question->questiontext, $id,
                $question->contextid, 'question', 'questiontext', $id,
                $question->contextid, 'qformat_xhtmlplus');
        $expout .= '<div class="question-text">';
        $expout .= "  <h3>{$question->name}</h3>\n";
        $expout .= '  <div class="questiontext">' . format_text($text,
                $question->questiontextformat, array('noclean' => true)) . "</div>\n";
        $expout .= "</div>\n";

        // Selection depends on question type.
        switch($question->qtype) {
            case 'truefalse':
                $expout .= $this->write_truefalse($question);
                break;
            case 'multichoice':
                $expout .= $this->write_multichoice($question);
                break;
            case 'shortanswer':
                $expout .= $this->write_shortanswer($question);
                break;
            case 'numerical':
                $expout .= $this->write_numerical($question);
                break;
            case 'match':
                $expout .= $this->write_match($question);
                break;
            case 'essay':
                $expout .= $this->write_essay($question);
                break;
            case 'multianswer':
                $expout .= $this->write_multianswer($question);
                break;
            case 'calculated':
            case 'calculatedmulti':
            case 'calculatedsimple':
                $expout .= $this->write_calculated($question);
                break;
            case 'ddimageortext':
            case 'ddmarker':
            case 'ddwtos':
                $expout .= $this->write_dragdrop($question);
                break;
            case 'gapselect':
                $expout .= $this->write_gapselect($question);
                break;
            case 'description':
                break;
            default:
                $expout .= "  <div class=\"unsupported-type\">\n";
                $expout .= "    <p>" . get_string('exportnotimplemented', 'qformat_xhtmlplus', $question->qtype) . "</p>\n";
                $expout .= "  </div>\n";
        }

        // General feedback
        $expout .= $this->render_general_feedback($question);

        $expout .= "</div>\n\n";
        return $expout;
    }

    /**
     * Write true/false question.
     */
    protected function write_truefalse($question) {
        $id = $question->id;
        $sttrue = get_string('true', 'qtype_truefalse');
        $stfalse = get_string('false', 'qtype_truefalse');
        $correct_tf = '';

        foreach ($question->options->answers as $answer) {
            if ($answer->fraction > 0) {
                $correct_tf = trim(strtolower($answer->answer));
            }
        }

        $true_class  = ($correct_tf == 'true')  ? ' class="correct-answer"' : '';
        $false_class = ($correct_tf == 'false') ? ' class="correct-answer"' : '';
        $true_badge  = ($correct_tf == 'true')  ? ' <span class="correct-badge">✔</span>' : '';
        $false_badge = ($correct_tf == 'false') ? ' <span class="correct-badge">✔</span>' : '';

        $expout = "<div class=\"question-content truefalse\">\n";
        $expout .= "<ul class=\"truefalse\">\n";
        $expout .= "  <li{$true_class}><input name=\"quest_{$id}\" type=\"radio\" value=\"{$sttrue}\" />{$sttrue}{$true_badge}</li>\n";
        $expout .= "  <li{$false_class}><input name=\"quest_{$id}\" type=\"radio\" value=\"{$stfalse}\" />{$stfalse}{$false_badge}</li>\n";
        $expout .= "</ul>\n";

        foreach ($question->options->answers as $answer) {
            if (!empty($answer->feedback)) {
                $expout .= "<div class=\"answer-feedback\">\n";
                $expout .= "  <strong>" . ($answer->fraction > 0 ? $sttrue : $stfalse) . ":</strong> ";
                $expout .= format_text($answer->feedback, $answer->feedbackformat, array('noclean' => true)) . "\n";
                $expout .= "</div>\n";
            }
        }

        $expout .= "</div>\n";
        return $expout;
    }

    /**
     * Write multichoice question.
     */
    protected function write_multichoice($question) {
        $id = $question->id;
        $expout = "<div class=\"question-content multichoice\">\n";

        if (!$question->options->single) {
            $expout .= "  <p class=\"question-info\">" . get_string('multipleanswers', 'qformat_xhtmlplus') . "</p>\n";
        }

        $expout .= "<ul class=\"multichoice\">\n";
        foreach ($question->options->answers as $answer) {
            $answertext = $this->repchar($answer->answer);
            $is_correct = ($answer->fraction > 0);
            $li_class = $is_correct ? ' class="correct-answer"' : '';
            $badge = $is_correct ? ' <span class="correct-badge">✔</span>' : '';

            if ($question->options->single) {
                $expout .= "  <li{$li_class}><input name=\"quest_{$id}\" type=\"radio\" value=\""
                        . s($answertext) . "\" />{$answertext}{$badge}</li>\n";
            } else {
                $expout .= "  <li{$li_class}><input name=\"quest_{$id}\" type=\"checkbox\" value=\""
                        . s($answertext) . "\" />{$answertext}{$badge}</li>\n";
            }

            if (!empty($answer->feedback)) {
                $expout .= "  <li class=\"answer-feedback\">" . format_text($answer->feedback, $answer->feedbackformat, array('noclean' => true)) . "</li>\n";
            }
        }
        $expout .= "</ul>\n";
        $expout .= "</div>\n";
        return $expout;
    }

    /**
     * Write short answer question.
     */
    protected function write_shortanswer($question) {
        $id = $question->id;
        $expout = "<div class=\"question-content shortanswer\">\n";
        $expout .= html_writer::start_tag('ul', array('class' => 'shortanswer'));
        $expout .= html_writer::start_tag('li');
        $expout .= html_writer::label(get_string('answer'), 'quest_'.$id, false, array('class' => 'accesshide'));
        $expout .= html_writer::empty_tag('input', array('id' => "quest_{$id}", 'name' => "quest_{$id}", 'type' => 'text'));
        $expout .= html_writer::end_tag('li');
        $expout .= html_writer::end_tag('ul');

        $accepted_sa = [];
        $feedback_list = [];
        foreach ($question->options->answers as $answer) {
            if ($answer->fraction > 0) {
                $accepted_sa[] = '<strong>' . s($answer->answer) . '</strong>';
                if (!empty($answer->feedback)) {
                    $feedback_list[] = '<div class="answer-feedback">' . format_text($answer->feedback, $answer->feedbackformat, array('noclean' => true)) . '</div>';
                }
            }
        }

        if (!empty($accepted_sa)) {
            $expout .= '<p class="answer-key">✔ ' . get_string('correctanswers', 'qtype_shortanswer') . ': ' . implode(' &nbsp;|&nbsp; ', $accepted_sa) . '</p>' . "\n";
        }

        if (!empty($feedback_list)) {
            $expout .= implode("\n", $feedback_list);
        }

        $expout .= "</div>\n";
        return $expout;
    }

    /**
     * Write numerical question.
     */
    protected function write_numerical($question) {
        $id = $question->id;
        $expout = "<div class=\"question-content numerical\">\n";
        $expout .= html_writer::start_tag('ul', array('class' => 'numerical'));
        $expout .= html_writer::start_tag('li');
        $expout .= html_writer::label(get_string('answer'), 'quest_'.$id, false, array('class' => 'accesshide'));
        $expout .= html_writer::empty_tag('input', array('id' => "quest_{$id}", 'name' => "quest_{$id}", 'type' => 'text'));
        $expout .= html_writer::end_tag('li');
        $expout .= html_writer::end_tag('ul');

        $accepted_num = [];
        $feedback_list = [];
        foreach ($question->options->answers as $answer) {
            if ($answer->fraction > 0) {
                $tol = isset($answer->tolerance) && $answer->tolerance > 0
                     ? ' ±' . s($answer->tolerance) : '';
                $accepted_num[] = '<strong>' . s($answer->answer) . $tol . '</strong>';
                if (!empty($answer->feedback)) {
                    $feedback_list[] = '<div class="answer-feedback">' . format_text($answer->feedback, $answer->feedbackformat, array('noclean' => true)) . '</div>';
                }
            }
        }

        if (!empty($accepted_num)) {
            $expout .= '<p class="answer-key">✔ ' . get_string('answer', 'question') . ': ' . implode(' &nbsp;|&nbsp; ', $accepted_num) . '</p>' . "\n";
        }

        if (!empty($feedback_list)) {
            $expout .= implode("\n", $feedback_list);
        }

        $expout .= "</div>\n";
        return $expout;
    }

    /**
     * Write match question.
     */
    protected function write_match($question) {
        $id = $question->id;
        $expout = "<div class=\"question-content match\">\n";
        $expout .= html_writer::start_tag('ul', array('class' => 'match'));

        $answerlist = array();
        $correct_answers = array();
        foreach ($question->options->subquestions as $subquestion) {
            $answerlist[] = $this->repchar($subquestion->answertext);
            $correct_answers[$subquestion->id] = $this->repchar($subquestion->answertext);
        }
        shuffle($answerlist);

        $selectoptions = array();
        foreach ($answerlist as $ans) {
            $selectoptions[s($ans)] = s($ans);
        }

        $option = 0;
        foreach ($question->options->subquestions as $subquestion) {
            $questiontext = $this->repchar($subquestion->questiontext);
            if ($questiontext != '') {
                $correct_answer = $correct_answers[$subquestion->id];
                $dropdown = html_writer::label(get_string('answer', 'qtype_match', $option+1), 'quest_'.$id.'_'.$option,
                        false, array('class' => 'accesshide'));
                $dropdown .= html_writer::select($selectoptions, "quest_{$id}_{$option}", s($correct_answer), false,
                        array('id' => "quest_{$id}_{$option}"));
                $expout .= html_writer::tag('li', $questiontext);
                $expout .= $dropdown;
                $expout .= '<p class="answer-key match-answer">✔ ' . get_string('answer', 'qtype_match') . ': <strong>' . s($correct_answer) . '</strong></p>';
                $option++;
            }
        }
        $expout .= html_writer::end_tag('ul');
        $expout .= "</div>\n";
        return $expout;
    }

    /**
     * Write essay question.
     */
    protected function write_essay($question) {
        $expout = "<div class=\"question-content essay\">\n";

        if (isset($question->options)) {
            $expout .= "  <div class=\"essay-settings\">\n";
            if (isset($question->options->responseformat)) {
                $expout .= "    <p><strong>" . get_string('responseformat', 'qtype_essay') . ":</strong> " .
                    s($question->options->responseformat) . "</p>\n";
            }
            if (isset($question->options->responserequired)) {
                $expout .= "    <p><strong>" . get_string('responserequired', 'qtype_essay') . ":</strong> " .
                    ($question->options->responserequired ? get_string('yes') : get_string('no')) . "</p>\n";
            }
            if (isset($question->options->attachments)) {
                $expout .= "    <p><strong>" . get_string('attachments', 'qformat_xhtmlplus') . ":</strong> " .
                    s($question->options->attachments) . "</p>\n";
            }
            $expout .= "  </div>\n";
        }

        $expout .= "</div>\n";
        return $expout;
    }

    /**
     * Write multianswer (cloze) question.
     */
    protected function write_multianswer($question) {
        $expout = "<div class=\"question-content multianswer\">\n";

        if (isset($question->options->questions) && is_array($question->options->questions)) {
            $expout .= "  <div class=\"multianswer-questions\">\n";
            foreach ($question->options->questions as $subq) {
                if ($subq && isset($subq->options->answers)) {
                    $expout .= "    <div class=\"subquestion\">\n";
                    foreach ($subq->options->answers as $answer) {
                        if ($answer->fraction > 0) {
                            $expout .= "      <div class=\"answer-item correct-answer\">\n";
                            $expout .= "        <strong>" . s($answer->answer) . "</strong> <span class=\"correct-badge\">✔</span>\n";
                            if (!empty($answer->feedback)) {
                                $expout .= "        <div class=\"answer-feedback\">" . format_text($answer->feedback, $answer->feedbackformat, array('noclean' => true)) . "</div>\n";
                            }
                            $expout .= "      </div>\n";
                        }
                    }
                    $expout .= "    </div>\n";
                }
            }
            $expout .= "  </div>\n";
        }

        $expout .= "</div>\n";
        return $expout;
    }

    /**
     * Write calculated question.
     */
    protected function write_calculated($question) {
        $expout = "<div class=\"question-content calculated\">\n";

        if (isset($question->options->answers)) {
            $expout .= "  <div class=\"answers\">\n";
            foreach ($question->options->answers as $answer) {
                if ($answer->fraction > 0) {
                    $expout .= "    <div class=\"answer-item correct-answer\">\n";
                    $expout .= "      <strong>" . s($answer->answer) . "</strong> <span class=\"correct-badge\">✔</span>\n";
                    if (!empty($answer->feedback)) {
                        $expout .= "      <div class=\"answer-feedback\">" . format_text($answer->feedback, $answer->feedbackformat, array('noclean' => true)) . "</div>\n";
                    }
                    $expout .= "    </div>\n";
                }
            }
            $expout .= "  </div>\n";
        }

        $expout .= "</div>\n";
        return $expout;
    }

    /**
     * Write drag and drop question.
     */
    protected function write_dragdrop($question) {
        $expout = "<div class=\"question-content dragdrop\">\n";
        $expout .= "  <p class=\"question-info\">" . get_string($question->qtype, 'qtype_' . $question->qtype) . "</p>\n";

        if (isset($question->options)) {
            if (isset($question->options->answers)) {
                $expout .= "  <div class=\"answers\">\n";
                foreach ($question->options->answers as $answer) {
                    $expout .= "    <div class=\"answer-item\">" . s($answer->answer) . "</div>\n";
                }
                $expout .= "  </div>\n";
            }
        }

        $expout .= "</div>\n";
        return $expout;
    }

    /**
     * Write gap select question.
     */
    protected function write_gapselect($question) {
        $expout = "<div class=\"question-content gapselect\">\n";
        $expout .= "  <p class=\"question-info\">" . get_string('gapselect', 'qtype_gapselect') . "</p>\n";

        if (isset($question->options->answers)) {
            $expout .= "  <div class=\"answers\">\n";
            foreach ($question->options->answers as $answer) {
                if ($answer->fraction > 0) {
                    $expout .= "    <div class=\"answer-item correct-answer\">" . s($answer->answer) . " <span class=\"correct-badge\">✔</span></div>\n";
                }
            }
            $expout .= "  </div>\n";
        }

        $expout .= "</div>\n";
        return $expout;
    }


    protected function presave_process($content) {
        global $CFG;

        $cssfile = "{$CFG->dirroot}/question/format/xhtmlplus/xhtml.css";
        $css = '';
        if (file_exists($cssfile)) {
            $csslines = file($cssfile);
            $css = implode(' ', $csslines);
        }

        $xp = "<!DOCTYPE html>\n";
        $xp .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"en\">\n";
        $xp .= "<head>\n";
        $xp .= "  <meta charset=\"UTF-8\" />\n";
        $xp .= "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n";
        $xp .= "  <title>" . get_string('exportedquestions', 'qformat_xhtmlplus') . "</title>\n";
        $xp .= "  <style type=\"text/css\">\n";
        $xp .= "    /* Moodle XHTML+ Export Styles */\n";
        $xp .= $css;
        $xp .= "  </style>\n";
        $xp .= "</head>\n";
        $xp .= "<body>\n";
        $xp .= "  <div class=\"questions-container\">\n";
        $xp .= "    <h1>" . get_string('exportedquestions', 'qformat_xhtmlplus') . "</h1>\n";
        $xp .= "    <p class=\"export-date\">Exported: " . date('Y-m-d H:i:s') . "</p>\n";
        $xp .= $content;
        $xp .= "  </div>\n";
        $xp .= "</body>\n";
        $xp .= "</html>\n";

        return $xp;
    }

    public function export_file_extension() {
        return '.html';
    }
}