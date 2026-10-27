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

/**
 * Esindab üht columnconnector küsimust, kui õppija seda lahendab.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_columnconnector_question extends question_graded_automatically {
    /** @var int tulpade (või ridade) arv, 2–7. */
    public $numcolumns;
    /** @var string 'columns' või 'rows'. */
    public $layoutmode;
    /** @var string 'curved' või 'straight'. */
    public $linestyle;
    /** @var int kas näidata nuppu "Tühista ühendused". */
    public $showclear;
    /** @var int kas näidata puuduvaid ühendusi õppijale pärast esitamist. */
    public $showmissing;
    /** @var float punktid õige ühenduse eest. */
    public $correctpoints;
    /** @var float punktid vale ühenduse eest. */
    public $incorrectpoints;
    /** @var float punktid puuduva ühenduse eest. */
    public $missingpoints;

    /** @var array tulbad: [['title' => '', 'cells' => [['text' => .., 'imageurl' => .., ..], ..]], ..]. */
    public $columns = [];
    /** @var array õigete ühenduste võti: [['from' => ['col', 'row'], 'to' => ['col', 'row']], ..]. */
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
     * Kontrolli, kas katset saab teise küsimuseversiooniga ümber hinnata.
     *
     * Katse olek ('_order<tulp>') ja vastused viitavad lahtritele reaindeksiga.
     * Ümberhindamine on lubatud ainult siis, kui igas tulbas on samad lahtrid
     * samas järjekorras. Lahtreid tuvastatakse püsiva id järgi (lahtri sisu
     * parandamine on lubatud). Kui mõnel lahtril pole usaldusväärset id-d (vana
     * sisu või asukohapõhine id, vt qtype_columnconnector::is_reliable_cell_id()),
     * nõutakse, et lahtrite sisu oleks samades kohtades identne.
     *
     * @param question_definition $otherversion
     * @return string|null
     */
    public function validate_can_regrade_with_other_version(question_definition $otherversion): ?string {
        $basemessage = parent::validate_can_regrade_with_other_version($otherversion);
        if ($basemessage) {
            return $basemessage;
        }

        if (count($this->columns) != count($otherversion->columns)) {
            return get_string('regradeissuestructurechanged', 'qtype_columnconnector');
        }
        foreach ($this->columns as $colindex => $column) {
            $cells = array_values($column['cells'] ?? []);
            $othercells = $otherversion->columns[$colindex]['cells'] ?? null;
            if (!is_array($othercells) || count($cells) != count($othercells)) {
                return get_string('regradeissuestructurechanged', 'qtype_columnconnector');
            }
            $othercells = array_values($othercells);
            foreach ($cells as $row => $cell) {
                if (!self::same_cell($cell, $othercells[$row])) {
                    return get_string('regradeissuecellorderchanged', 'qtype_columnconnector');
                }
            }
        }

        return null;
    }

    /**
     * Kas kahe versiooni samal kohal olevad lahtrid on sama lahter.
     *
     * @param mixed $a
     * @param mixed $b
     * @return bool
     */
    protected static function same_cell($a, $b) {
        $a = is_array($a) ? $a : [];
        $b = is_array($b) ? $b : [];
        $ida = trim((string)($a['id'] ?? ''));
        $idb = trim((string)($b['id'] ?? ''));
        if (qtype_columnconnector::is_reliable_cell_id($ida) && qtype_columnconnector::is_reliable_cell_id($idb)) {
            return $ida === $idb;
        }
        // Usaldusväärne id puudub: võrdle sisu.
        return qtype_columnconnector::cell_content_signature($a) === qtype_columnconnector::cell_content_signature($b);
    }

    /**
     * Tagasta tulba lahtrite kuvamisjärjekord.
     *
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
     * Oodatavad vastuse väljad.
     *
     * @return array
     */
    public function get_expected_data() {
        return ['answer' => PARAM_RAW];
    }

    /**
     * Kas vastus on täielik (vähemalt üks ühendus).
     *
     * @param array $response
     * @return bool
     */
    public function is_complete_response(array $response) {
        return count($this->decode_response($response)) > 0;
    }

    /**
     * Kas vastust saab hinnata.
     *
     * @param array $response
     * @return bool
     */
    public function is_gradable_response(array $response) {
        return $this->is_complete_response($response);
    }

    /**
     * Veateade, kui vastus pole hinnatav.
     *
     * @param array $response
     * @return string
     */
    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseconnect', 'qtype_columnconnector');
    }

    /**
     * Kas kaks vastust on samad (suunast ja järjekorrast sõltumata).
     *
     * @param array $prevresponse
     * @param array $newresponse
     * @return bool
     */
    public function is_same_response(array $prevresponse, array $newresponse) {
        return self::connection_keys($this->decode_response($prevresponse))
            == self::connection_keys($this->decode_response($newresponse));
    }

    /**
     * Täispunkte andev vastus.
     *
     * @return array
     */
    public function get_correct_response() {
        return ['answer' => json_encode(array_values($this->answerkey))];
    }

    /**
     * Inimloetav kokkuvõte vastusest.
     *
     * @param array $response
     * @return string
     */
    public function summarise_response(array $response) {
        $connections = $this->decode_response($response);
        if (empty($connections)) {
            return '';
        }
        $parts = [];
        foreach ($connections as $c) {
            $parts[] = $this->describe_connection($c);
        }
        return implode('; ', $parts);
    }

    /**
     * Klassifitseeri vastus statistika jaoks.
     *
     * Iga naabertulpade lahtripaar on eraldi osa, millel on kaks vastuseklassi:
     * "connected" (ühendus tehti) ja "notconnected" (ühendust ei tehtud). Osade ja
     * klasside id-d vastavad qtype_columnconnector::get_possible_responses() väljundile.
     * Vastuseta katse korral on iga osa "vastus puudub".
     *
     * @param array $response
     * @return question_classified_response[]
     */
    public function classify_response(array $response) {
        $made = [];
        foreach ($this->decode_response($response) as $conn) {
            $made[self::connection_key($conn)] = true;
        }

        $classified = [];
        foreach ($this->get_response_pairs() as $key => $pair) {
            if (empty($made)) {
                $classified[$key] = question_classified_response::no_response();
                continue;
            }
            $class = isset($made[$key]) ? 'connected' : 'notconnected';
            [$label, $fraction] = $this->get_pair_class_details($pair['conn'], $class);
            $classified[$key] = new question_classified_response($class, $label, $fraction);
        }
        return $classified;
    }

    /**
     * Kõik naabertulpade lahtripaarid (statistika osad).
     *
     * @return array võti => ['conn' => ühendus]
     */
    public function get_response_pairs() {
        $pairs = [];
        $numcolumns = count($this->columns);
        for ($c = 0; $c < $numcolumns - 1; $c++) {
            foreach (array_keys($this->columns[$c]['cells'] ?? []) as $r1) {
                foreach (array_keys($this->columns[$c + 1]['cells'] ?? []) as $r2) {
                    $conn = ['from' => ['col' => $c, 'row' => $r1], 'to' => ['col' => $c + 1, 'row' => $r2]];
                    $pairs[self::connection_key($conn)] = ['conn' => $conn];
                }
            }
        }
        return $pairs;
    }

    /**
     * Lahtripaari vastuseklassi silt ja murdosa.
     *
     * Ühendatud paar: õige eest correctpoints, vale eest incorrectpoints.
     * Ühendamata paar: õige ühenduse puudumise eest missingpoints, muidu 0.
     * Murdosa on punktid jagatud maksimumpunktidega.
     *
     * @param array $conn
     * @param string $class 'connected' või 'notconnected'
     * @return array [silt, murdosa]
     */
    public function get_pair_class_details(array $conn, $class) {
        $correct = isset($this->get_answerkey_map()[self::connection_key($conn)]);
        if ($class === 'connected') {
            $points = $correct ? (float)$this->correctpoints : (float)$this->incorrectpoints;
            $string = 'statsconnected';
        } else {
            $points = $correct ? (float)$this->missingpoints : 0.0;
            $string = 'statsnotconnected';
        }
        $maxpoints = $this->get_max_points();
        $label = get_string($string, 'qtype_columnconnector', $this->describe_connection($conn));
        return [$label, $maxpoints > 0 ? $points / $maxpoints : 0];
    }

    /**
     * Hinda vastus.
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
     * Ligipääsu kontroll küsimuse failidele.
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
        if ($component == 'qtype_columnconnector' && $filearea == 'cellimages') {
            // Lahtrite pildid on seotud selle küsimuse id-ga.
            return reset($args) == $this->id;
        }
        // Küsimuse tekst ja üldtagasiside käsitleb põhiklass.
        return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
    }

    /**
     * Õppija ühenduste olek: kas iga ühendus on õige või vale.
     *
     * @param array $connections normaliseeritud ühendused
     * @return array [['from' => .., 'to' => .., 'status' => 'correct'|'incorrect'], ..]
     */
    public function get_connection_feedback(array $connections) {
        $correctmap = $this->get_answerkey_map();
        $out = [];
        foreach ($connections as $conn) {
            $conn['status'] = isset($correctmap[self::connection_key($conn)]) ? 'correct' : 'incorrect';
            $out[] = $conn;
        }
        return $out;
    }

    /**
     * Õiged ühendused, mida õppija ei teinud.
     *
     * @param array $connections normaliseeritud ühendused
     * @return array
     */
    public function get_missing_connections(array $connections) {
        $made = array_flip(self::connection_keys($connections));
        $missing = [];
        foreach ($this->answerkey as $conn) {
            if (!isset($made[self::connection_key($conn)])) {
                $missing[] = $conn;
            }
        }
        return $missing;
    }

    /**
     * Ühenduse lühikirjeldus, nt "Tallinn – Eesti".
     *
     * @param array $conn
     * @return string
     */
    public function describe_connection(array $conn) {
        return $this->describe_cell($conn['from']) . ' – ' . $this->describe_cell($conn['to']);
    }

    /**
     * Lahtri lühikirjeldus (tekst, alt-tekst või asukoht).
     *
     * @param array $pos ['col' => .., 'row' => ..]
     * @return string
     */
    public function describe_cell($pos) {
        $col = $pos['col'];
        $row = $pos['row'];
        if (isset($this->columns[$col]['cells'][$row])) {
            $cell = $this->columns[$col]['cells'][$row];
            $text = trim(html_to_text($cell['text'] ?? '', 0, false));
            if ($text !== '') {
                return $text;
            }
            if (trim((string)($cell['alt'] ?? '')) !== '') {
                return trim($cell['alt']);
            }
        }
        $a = (object)['col' => $col + 1, 'row' => $row + 1];
        return get_string($this->layoutmode === 'rows' ? 'cellpositionrows' : 'cellposition', 'qtype_columnconnector', $a);
    }

    /**
     * Kas lahter on selles küsimuses olemas.
     *
     * @param array $pos ['col' => .., 'row' => ..]
     * @return bool
     */
    protected function cell_exists($pos) {
        return isset($this->columns[$pos['col']]['cells'][$pos['row']]);
    }

    /**
     * Õigete ühenduste võtmed.
     *
     * @return array võti => true
     */
    protected function get_answerkey_map() {
        $map = [];
        foreach ($this->answerkey as $conn) {
            $map[self::connection_key($conn)] = true;
        }
        return $map;
    }

    /**
     * Maksimumpunktid (õigete ühenduste arv × punktid õige eest).
     *
     * @return float
     */
    protected function get_max_points() {
        return max(0, count($this->get_answerkey_map()) * (float)$this->correctpoints);
    }

    /**
     * Dekodeeri esitatud vastus normaliseeritud ühenduste massiiviks.
     *
     * @param array $response
     * @return array [['from' => ['col', 'row'], 'to' => ['col', 'row']], ..]
     */
    public function decode_response(array $response) {
        if (empty($response['answer'])) {
            return [];
        }
        return self::decode_connections($response['answer']);
    }

    /**
     * Dekodeeri ühendused JSON-stringist või massiivist (duplikaadid eemaldatakse).
     *
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
     * Vii üks ühendus kujule ['from' => ['col', 'row'], 'to' => ['col', 'row']].
     *
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
        if (!is_array($from) || !is_array($to) || !isset($from['col'], $from['row'], $to['col'], $to['row'])) {
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
     * Suunast sõltumatu võti ühenduse jaoks.
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
     * Ühenduste sorditud võtmed.
     *
     * @param array $connections
     * @return array
     */
    public static function connection_keys($connections) {
        $keys = array_map([self::class, 'connection_key'], $connections);
        sort($keys);
        return $keys;
    }

    /**
     * Puhas hindamisloogika (samaväärne H5P.ColumnConnector valemiga).
     *
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
}
