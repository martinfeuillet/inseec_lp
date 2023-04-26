<?php

namespace Inseec\App\Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class JpoController {

    public function __construct() {
        define('IMG_PATH', '/app/src/assets/img');
    }

    public function index() {
        // Create a new Twig environment with the path to your templates directory
        $loader = new FilesystemLoader(dirname(__DIR__) . '/View');
        $twig   = new Environment($loader, [
            'cache' => false,
        ]);

        // Render the homecontroller.html.twig template with any necessary data
        $template = $twig->load('jpo-controller.html.twig');
        $data     = array(
            'img_path'             => '/app/src/assets/img',
            'ours_campus'          => $this->ours_campus(),
            'render_form'          => $this->render_form(),
            'three_block_programs' => $this->three_block_programs(),
            'slider'               => $this->slider(),
            'next_event'           => $this->guess_the_next_event(),
        );
        echo $template->render($data);
    }

    public function three_block_programs() {
        return [
            array(
                "title"       => "Présentation des programmes",
                "img"         => IMG_PATH . "/three-block-programs/programs1.jpg",
                "description" => "Découverte des 25 spécialisations et discussion avec les équipes",
                "icon"        => IMG_PATH . "/three-block-programs/icon3.svg"
            ),
            array(
                "title"       => "Visite du campus",
                "img"         => IMG_PATH . "/three-block-programs/programs2.jpg",
                "description" => "Découverte de nos salles de classes, studios de tournage, etc.",
                "icon"        => IMG_PATH . "/three-block-programs/icon2.svg"
            ),
            array(
                "title"       => "Rencontre avec les étudiant·es",
                "img"         => IMG_PATH . "/three-block-programs/programs3.jpg",
                "description" => "Discussion avec les étudiant·es pour connaître leur expérience à Sup de Pub.",
                "icon"        => IMG_PATH . "/three-block-programs/icon1.svg"
            ),
        ];
    }

    public function slider() {
        return [
            array(
                "title" => "<span>35</span> ans d'expérience",
                "img"   => IMG_PATH . "/slider/slider1.jpg",
            ),
            array(
                "title" => "<span>4</span> campus en France",
                "img"   => IMG_PATH . "/slider/slider2.jpg",
            ),
            array(
                "title" => "<span>23</span> spécialités",
                "img"   => IMG_PATH . "/slider/slider3.jpg",
            ),
        ];
    }

    public function guess_the_next_event() {
        $url_ws            = 'https://www.supdepub.com/inseecu/fr/api/form/jpo';
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer"      => false,
                "verify_peer_name" => false,
            ),
        );
        $data              = file_get_contents($url_ws, false, stream_context_create($arrContextOptions));
        $data              = json_decode($data, true);
        $date_of_events    = array();
        foreach ($data['fields'] as $event) {
            if ($event['name'] === "event") {
                foreach ($event['values'] as $value) {
                    $datetime = $value['start_date'] . " " . $value['start_time'] . ":00";
                    $date_of_events[$value['start_date']] = $datetime;
                }
            }
        }
        asort($date_of_events);
        foreach ($date_of_events as $key => $value) {
            if (strtotime($value) < time()) {
                unset($date_of_events[$key]);
            }
        }
        $next_event = array_shift($date_of_events);
        $counter_to_go = array(
            "days"    => 0,
            "hours"   => 0,
            "minutes" => 0,

        );
        $counter_to_go['days']    = floor((strtotime($next_event) - time()) / 86400);
        if ($counter_to_go['days'] > 0) {
            $counter_to_go['hours']   = floor((strtotime($next_event) - time()) / 3600) - ($counter_to_go['days'] * 24);
            $counter_to_go['minutes'] = floor((strtotime($next_event) - time()) / 60) - ($counter_to_go['days'] * 24 * 60) - ($counter_to_go['hours'] * 60);
        } else {
            $counter_to_go['hours']   = floor((strtotime($next_event) - time()) / 3600);
            $counter_to_go['minutes'] = floor((strtotime($next_event) - time()) / 60) - ($counter_to_go['hours'] * 60);
        }
        $days_to_go    = $counter_to_go['days'];
        $hours_to_go   = $counter_to_go['hours'];
        $minutes_to_go = $counter_to_go['minutes'];
        return [
            "next_event" => $next_event,
            "days_to_go" => $days_to_go,
            "hours_to_go" => $hours_to_go,
            "minutes_to_go" => $minutes_to_go
        ];
    }


    public function ours_campus() {
        return [
            "Bordeaux" => "/",
            "Lyon"     => "/",
            "Paris"    => "/",
            "Rennes"   => "/"
        ];
    }

    public function render_form() {
        $url_ws            = 'https://www.supdepub.com/inseecu/fr/api/form/jpo';
        $translate_day     = array(
            'Monday'    => 'Lundi',
            'Tuesday'   => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday'  => 'Jeudi',
            'Friday'    => 'Vendredi',
            'Saturday'  => 'Samedi',
            'Sunday'    => 'Dimanche'
        );
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer"      => false,
                "verify_peer_name" => false,
            ),
        );
        $data              = file_get_contents($url_ws, false, stream_context_create($arrContextOptions));
        $data              = json_decode($data, true);

        $campus         = array();
        $date_of_events = array();
        foreach ($data['fields'] as $event) {
            if ($event['name'] === "campus") {
                foreach ($event['values'] as $value) {
                    $campus[$value["id"]] = $value['label'];
                }
            }
            if ($event['name'] === "event") {
                foreach ($event['values'] as $value) {
                    $date_of_events[$value['start_date']] = date('d/m', strtotime($value['start_date']));
                }
            }
        }
        ob_start();
?>
        <div class="omnes_form">
            <form id="omnes_form" action="https://www.supdepub.com/inseecu/fr/api/form/jpo" method="post">
                <div class="part1">
                    <h3>Inscris-toi à nos prochaines Journées Portes Ouvertes</h3>
                    <p>Choisir une ville et une date</p>
                    <p class="msg_error"></p>
                    <div class="of-select">
                        <select name="of_city" id="omnes_form">
                            <option selected="true" disabled="disabled" value="">Ville</option>
                            <?php
                            foreach ($campus as $id => $city) {
                                echo '<option value="' . $id . '">' . $city . '</option>';
                            }
                            ?>
                        </select>
                        <select name="of_day" id="omnes_form">
                            <option selected="true" disabled="disabled" value="">Jour</option>
                            <?php
                            foreach ($date_of_events as $date => $date_format) {
                                echo "<option value='$date'>$date_format</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="of-result">
                        <?php
                        foreach ($data['fields'] as $event) {
                            if ($event['name'] === "event") {
                                foreach ($event['values'] as $value) :
                                    $day_start_in_letters = $translate_day[date('l', strtotime($value['start_date']))];
                                    $day_start_formated   = date('d/m', strtotime($value['start_date']));
                                    $start_and_end_time   = date('H:i', strtotime($value['start_time'])) . ' - ' . date('H:i', strtotime($value['end_time']));
                                    echo '<div class="omnes_form_event" data-campus="' . $value["campus"] . '" data-day="' . $value["start_date"] . '" data-id="' . $value["id"] . '" >';
                                    echo '<div class="date_and_city" data-date="' . $value['start_date'] . '" data-city="' . $value['city'] . '">';
                                    // echo the day of the week in all letters
                                    echo '<p class="day">' . $day_start_in_letters . '</p>';
                                    echo '<p class="date">' . $day_start_formated . '</p>';
                                    echo '<p class="city">' . $value['campus'] . '</p>';
                                    echo '</div>';
                                    echo '<div class="label_and_hours">';
                                    echo '<p class="label">' . $value['label'] . '</p>';
                                    echo '<p class="hours"><span class="dashicons dashicons-clock"></span> de ' . $start_and_end_time . '</p>';
                                    echo '</div>';
                                    echo '</div>';
                                endforeach;
                            }
                        }
                        ?>
                    </div>
                    <div class="step1">
                        <p>Étape 1/2</p>
                        <button class="go_to_part_two">Suivant &#8594;</button>
                    </div>
                </div>
                <div class="part2">
                    <p>Remplis tes coordonnées</p>
                    <p class="msg_error"></p>
                    <?php
                    $field_order  = array('lastName', 'firstName', 'mobilePhoneNumber', 'email', 'backToSchool', 'admissionLevel', 'educationLevel', 'consent', 'source');
                    $text_input   = array('lastName', 'firstName', 'mobilePhoneNumber', 'email');
                    $select_input = array('backToSchool', 'admissionLevel', 'educationLevel');
                    foreach ($field_order as $field_name) {
                        $field = null;
                        foreach ($data['fields'] as $f) {
                            if ($f['name'] === $field_name) {
                                $field = $f;
                                break;
                            }
                        }
                        if ($field) {
                            if (in_array($field_name, $text_input)) {
                                echo '<input type="text" name="champ_' . $field['name'] . '" id="champ_' . $field['name'] . '" placeholder="' . $field['label'] . '*">';
                            }
                            if (in_array($field_name, $select_input)) {
                                echo '<select name="champ_' . $field['name'] . '" id="champ_' . $field['name'] . '"><option selected="true" disabled="disabled" value="">' . $field['label'] . '</option>';
                                foreach ($field['values'] as $value) {
                                    echo '<option value="' . $value['id'] . '">' . $value['label'] . '</option>';
                                }
                                echo '</select>';
                            }
                            if ($field_name === 'consent') {
                                echo '<div class="consent"><input type="checkbox" name="champ_' . $field['name'] . '" value="1"><p>' . $field['label'] . '</p></div>';
                            }
                            if ($field_name === 'source') {
                                $source_value = $_GET['utm_source'] ?? '';

                                echo '<input type="hidden" name="champ_' . $field['name'] . '" value="' . $source_value . '">';
                            }
                        }
                    }

                    ?>
                    <div class="step1">
                        <p>Étape 2/2</p>
                        <button class="submit_omnes_form">Envoyer &#8594;</button>
                    </div>
                </div>
            </form>
        </div>
<?php
        return ob_get_clean();
    }
}
