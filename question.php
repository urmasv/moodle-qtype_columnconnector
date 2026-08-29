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
 * columnconnector küsimuse definitsioon ja hindamisloogika.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Esindab üht columnconnector küsimust, kui õppija seda lahendab.
 */
class qtype_columnconnector_question extends question_graded_automatically {

    /** @var int tulpade arv (2-4). */
    public $numcolumns;
    /** @var string 'columns' või 'rows'. */
    public $layoutmode;
    /** @var string 'curved' või 'straight'. */
    public $linestyle;
    /** @var int kas näidata nupu "Tühista ühendused". */
    public $showclear;

    /** @var int kas näidata puuduvaid ühendusi õppijale pärast esitamist. */
    public $showmissing;
    /** @var float punktid õige ühenduse eest. */
    public $correctpoints;
    /** @var float punktid vale ühenduse eest. */
    public $incorrectpoints;
    /** @var float punktid puuduva ühenduse eest. */
    public $missingpoints;
    /** @var string juhis õppijale (HTML). */
    public $instructions;
    /** @var int juhise tekstiformaat. */
    public $instructionsformat;

    /** @var array tulbad: [ ['title' => '', 'cells' => [ ['text'=>..,'imageurl'=>..,..], ..]], .. ]. */
    public $columns = [];
    /** @var array õigete ühenduste võti: [ ['from'=>['col','row'], 'to'=>['col','row']], .. ]. */
    public $answerkey = [];

    /** @var array kuvamisjärjekord tulba kohta, määratakse start_attempt käigus. */
    protected $displayorder = [];

    /**
     * Alusta uut katset: sega iga tulba lahtrite kuvamisjärjekord.
     *
     * @param question_attempt_step $step
     * @param int $variant
     */
    public function start_attempt(question_attempt_step $step, $variant) {
        foreach ($this->columns as $colindex => $column) {
            $order = array_keys($column['cells']);
            shuffle($order);
            $this->displayorder[$colindex] = $order;
            $step->set_qt_var('_order' . $colindex, implode(',', $order));
        }
    }

    /**
     * Taasta katse olek andmebaasist (kuvamisjärjekord).
     *
     * @param question_attempt_step $step
     */
    public function apply_attempt_state(question_attempt_step $step) {
        foreach ($this->columns as $colindex => $column) {
            $raw = $step->get_qt_var('_order' . $colindex);
            if ($raw === null || $raw === '') {
                $this->displayorder[$colindex] = array_keys($column['cells']);
            } else {
                $this->displayorder[$colindex] = array_map('intval', explode(',', $raw));
            }
        }
    }

    /**
     * @param int $colindex
     * @return array reaindeksid kuvamisjärjekorras
     */
    public function get_display_order($colindex) {
        if (isset($this->displayorder[$colindex])) {
            return $this->displayorder[$colindex];
        }
        return isset($this->columns[$colindex]) ? array_keys($this->columns[$colindex]['cells']) : [];
    }

    /**
     * @return array oodatavad esitatud andmed
     */
    public function get_expected_data() {
        return ['answer' => PARAM_RAW];
    }

    /**
     * @param array $response
     * @return bool kas vastus on täielik
     */
    public function is_complete_response(array $response) {
        return count($this->decode_response($response)) > 0;
    }

    /**
     * @param array $response
     * @return bool kas vastust saab hinnata
     */
    public function is_gradable_response(array $response) {
        return $this->is_complete_response($response);
    }

    /**
     * @param array $response
     * @return string veateade, kui vastus pole hinnatav
     */
    public function get_validation_error(array $response) {
        return get_string('pleaseconnect', 'qtype_columnconnector');
    }

    /**
     * @param array $prevresponse
     * @param array $newresponse
     * @return bool kas vastused on samad
     */
    public function is_same_response(array $prevresponse, array $newresponse) {
        return self::connection_keys($this->decode_response($prevresponse))
            == self::connection_keys($this->decode_response($newresponse));
    }

    /**
     * @param array $response
     * @return array täispunkte andev vastus
     */
    public function get_correct_response() {
        return ['answer' => json_encode(array_values($this->answerkey))];
    }

    /**
     * @param array $response
     * @return string inimloetav kokkuvõte vastusest
     */
    public function summarise_response(array $response) {
        $connections = $this->decode_response($response);
        if (empty($connections)) {
            return '';
        }
        $parts = [];
        foreach ($connections as $c) {
            $parts[] = $this->describe_cell($c['from']) . ' – ' . $this->describe_cell($c['to']);
        }
        return implode('; ', $parts);
    }

    /**
     * Hinda vastus. Tagastab murdosa [0,1] ja seisundi.
     *
     * @param array $response
     * @return array [fraction, question_state]
     */
    public function grade_response(array $response) {
        $result = self::grade_connections(
            $this->answerkey,
            $this->decode_response($response),
            (float)$this->correctpoints,
            (float)$this->incorrectpoints,
            (float)$this->missingpoints
        );
        return [$result['fraction'], question_state::graded_state_for_fraction($result['fraction'])];
    }

    /**
     * Ligipääsu kontroll küsimuse failidele (juhis, üldtagasiside).
     *
     * @param question_attempt $qa
     * @param question_display_options $options
     * @param string $component
     * @param string $filearea
     * @param array $args
     * @param bool $forcedownload
     * @return bool
     */
    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'qtype_columnconnector' && in_array($filearea, ['instructions', 'cellimages'])) {
            return true;
        }
        if ($component == 'question' && in_array($filearea, ['questiontext', 'generalfeedback'])) {
            return $this->check_combined_feedback_file_access($qa, $options, $filearea, $args);
        }
        return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
    }

    // ---------------------------------------------------------------------
    // Abimeetodid (osa neist puhas loogika, ilma Moodle sõltuvusteta).
    // ---------------------------------------------------------------------

    /**
     * Dekodeeri esitatud vastus normaliseeritud ühenduste massiiviks.
     *
     * @param array $response
     * @return array [ ['from'=>['col','row'],'to'=>['col','row']], .. ]
     */
    public function decode_response(array $response) {
        if (empty($response['answer'])) {
            return [];
        }
        return self::decode_connections($response['answer']);
    }

    /**
     * @param mixed $raw JSON-string või massiiv
     * @return array normaliseeritud ühendused
     */
    public static function decode_connections($raw) {
        if (is_string($raw)) {
            $data = json_decode($raw, true);
        } else {
            $data = $raw;
        }
        if (!is_array($data)) {
            return [];
        }
        $out = [];
        $seen = [];
        foreach ($data as $item) {
            $conn = self::normalize_connection($item);
            if ($conn === null) {
                continue;
            }
            $key = self::connection_key($conn);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $out[] = $conn;
        }
        return $out;
    }

    /**
     * Vii üks ühendus kujule ['from'=>['col','row'],'to'=>['col','row']].
     * Kontrollib, et tegemist on naabertulpadega. Tagastab null, kui vigane.
     *
     * @param mixed $item
     * @return array|null
     */
    public static function normalize_connection($item) {
        if (!is_array($item) || !isset($item['from']) || !isset($item['to'])) {
            return null;
        }
        $from = $item['from'];
        $to = $item['to'];
        if (!isset($from['col'], $from['row'], $to['col'], $to['row'])) {
            return null;
        }
        $from = ['col' => (int)$from['col'], 'row' => (int)$from['row']];
        $to = ['col' => (int)$to['col'], 'row' => (int)$to['row']];
        if ($from['col'] < 0 || $from['row'] < 0 || $to['col'] < 0 || $to['row'] < 0) {
            return null;
        }
        if (abs($from['col'] - $to['col']) !== 1) {
            return null;
        }
        return ['from' => $from, 'to' => $to];
    }

    /**
     * Suunata sõltumatu (undirected) võti ühenduse jaoks.
     *
     * @param array $conn
     * @return string
     */
    public static function connection_key($conn) {
        $a = $conn['from'];
        $b = $conn['to'];
        if ([$b['col'], $b['row']] < [$a['col'], $a['row']]) {
            [$a, $b] = [$b, $a];
        }
        return $a['col'] . ':' . $a['row'] . '-' . $b['col'] . ':' . $b['row'];
    }

    /**
     * @param array $connections
     * @return array sorditud võtmete massiiv
     */
    public static function connection_keys($connections) {
        $keys = array_map(['self', 'connection_key'], $connections);
        sort($keys);
        return $keys;
    }

    /**
     * Puhas hindamisloogika (samaväärne H5P.ColumnConnector valemiga).
     * Ei sõltu Moodle'ist, et oleks eraldi testitav.
     *
     * @param array $answerkey õiged ühendused
     * @param array $response õppija ühendused (normaliseeritud)
     * @param float $cp punktid õige eest
     * @param float $ip punktid vale eest
     * @param float $mp punktid puuduva eest
     * @return array [fraction, score, incorrect, missing, points, maxpoints]
     */
    public static function grade_connections(array $answerkey, array $response, $cp, $ip, $mp) {
        $correctmap = [];
        foreach ($answerkey as $conn) {
            $correctmap[self::connection_key($conn)] = true;
        }

        $seen = [];
        $score = 0;
        $incorrect = 0;
        foreach ($response as $conn) {
            $key = self::connection_key($conn);
            if (isset($correctmap[$key]) && empty($seen[$key])) {
                $seen[$key] = true;
                $score++;
            } else {
                $incorrect++;
            }
        }

        $maxscore = count($correctmap);
        $missing = 0;
        foreach ($correctmap as $key => $unused) {
            if (empty($seen[$key])) {
                $missing++;
            }
        }

        $rawpoints = ($score * $cp) + ($incorrect * $ip) + ($missing * $mp);
        $points = max(0, $rawpoints);
        $maxpoints = max(0, $maxscore * $cp);

        if ($maxpoints > 0) {
            $fraction = (float) min(1, max(0, $points / $maxpoints));
        } else {
            $fraction = 0.0;
        }

        return [
            'fraction' => $fraction,
            'score' => $score,
            'incorrect' => $incorrect,
            'missing' => $missing,
            'points' => $points,
            'maxpoints' => $maxpoints,
        ];
    }

    /**
     * @param array $pos ['col'=>..,'row'=>..]
     * @return string lühikirjeldus lahtrist (kokkuvõteteks)
     */
    protected function describe_cell($pos) {
        $col = $pos['col'];
        $row = $pos['row'];
        if (isset($this->columns[$col]['cells'][$row])) {
            $cell = $this->columns[$col]['cells'][$row];
            $text = trim(html_to_text($cell['text'] ?? '', 0, false));
            if ($text !== '') {
                return $text;
            }
            if (!empty($cell['alt'])) {
                return $cell['alt'];
            }
        }
        return '(' . ($col + 1) . ',' . ($row + 1) . ')';
    }
}
