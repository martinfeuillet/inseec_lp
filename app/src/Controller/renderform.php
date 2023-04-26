<?php
namespace Inseec\App\Controller;
class Renderform {
    public static function render_form(){
        $url_ws            = 'https://www.supdepub.com/inseecu/fr/api/form/jpo';
        $translate_day     = array(
            'Monday'    => 'Lundi' ,
            'Tuesday'   => 'Mardi' ,
            'Wednesday' => 'Mercredi' ,
            'Thursday'  => 'Jeudi' ,
            'Friday'    => 'Vendredi' ,
            'Saturday'  => 'Samedi' ,
            'Sunday'    => 'Dimanche'
        );
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer"      => false ,
                "verify_peer_name" => false ,
            ) ,
        );
        $data              = file_get_contents( $url_ws , false , stream_context_create( $arrContextOptions ) );
        $data              = json_decode( $data , true );

        $campus         = array();
        $date_of_events = array();
        foreach ( $data['fields'] as $event ) {
            if ( $event['name'] === "campus" ) {
                foreach ( $event['values'] as $value ) {
                    $campus[ $value["id"] ] = $value['label'];
                }
            }
            if ( $event['name'] === "event" ) {
                foreach ( $event['values'] as $value ) {
                    $date_of_events[ $value['start_date'] ] = date( 'd/m' , strtotime( $value['start_date'] ) );
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
                            foreach ( $campus as $id => $city ) {
                                echo '<option value="' . $id . '">' . $city . '</option>';
                            }
                            ?>
                        </select>
                        <select name="of_day" id="omnes_form">
                            <option selected="true" disabled="disabled" value="">Jour</option>
                            <?php
                            foreach ( $date_of_events as $date => $date_format ) {
                                echo "<option value='$date'>$date_format</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="of-result">
                        <?php
                        foreach ( $data['fields'] as $event ) {
                            if ( $event['name'] === "event" ) {
                                foreach ( $event['values'] as $value ) :
                                    $day_start_in_letters = $translate_day[ date( 'l' , strtotime( $value['start_date'] ) ) ];
                                    $day_start_formated   = date( 'd/m' , strtotime( $value['start_date'] ) );
                                    $start_and_end_time   = date( 'H:i' , strtotime( $value['start_time'] ) ) . ' - ' . date( 'H:i' , strtotime( $value['end_time'] ) );
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
                    $field_order  = array('lastName' , 'firstName' , 'mobilePhoneNumber' , 'email' , 'backToSchool' , 'admissionLevel' , 'educationLevel' , 'consent' , 'source');
                    $text_input   = array('lastName' , 'firstName' , 'mobilePhoneNumber' , 'email');
                    $select_input = array('backToSchool' , 'admissionLevel' , 'educationLevel');
                    foreach ( $field_order as $field_name ) {
                        $field = null;
                        foreach ( $data['fields'] as $f ) {
                            if ( $f['name'] === $field_name ) {
                                $field = $f;
                                break;
                            }
                        }
                        if ( $field ) {
                            if ( in_array( $field_name , $text_input ) ) {
                                echo '<input type="text" name="champ_' . $field['name'] . '" id="champ_' . $field['name'] . '" placeholder="' . $field['label'] . '*">';
                            }
                            if ( in_array( $field_name , $select_input ) ) {
                                echo '<select name="champ_' . $field['name'] . '" id="champ_' . $field['name'] . '"><option selected="true" disabled="disabled" value="">' . $field['label'] . '</option>';
                                foreach ( $field['values'] as $value ) {
                                    echo '<option value="' . $value['id'] . '">' . $value['label'] . '</option>';
                                }
                                echo '</select>';
                            }
                            if ( $field_name === 'consent' ) {
                                echo '<div class="consent"><input type="checkbox" name="champ_' . $field['name'] . '" value="1"><p>' . $field['label'] . '</p></div>';
                            }
                            if ( $field_name === 'source' ) {
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



