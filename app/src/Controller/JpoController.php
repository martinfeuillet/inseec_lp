<?php

namespace Inseec\App\Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class JpoController
{

    public function __construct() {
        define( 'IMG_PATH' , 'http://localhost:8888/lp_inseec/app/src/assets/img' );
    }

    public function index() {
        // Create a new Twig environment with the path to your templates directory
        $loader = new FilesystemLoader( dirname( __DIR__ ) . '/View' );
        $twig   = new Environment( $loader , [
            'cache' => false ,
        ] );

        // Render the homecontroller.html.twig template with any necessary data
        $template = $twig->load( 'jpo-controller.html.twig' );
        $data     = array(
            'img_path'             => 'http://localhost:8888/lp_inseec/app/src/assets/img' ,
            'ours_campus'          => $this->ours_campus() ,
            'render_form'          => Renderform::render_form() ,
            'three_block_programs' => $this->three_block_programs() ,
            'slider'               => $this->slider() ,
            'next_event'           => $this->guess_the_next_event() ,
        );
        echo $template->render( $data );
    }

    public function three_block_programs() {
        return [
            array(
                "title"       => "Présentation des programmes" ,
                "img"         => IMG_PATH . "/three-block-programs/programs1.jpg" ,
                "description" => "Découverte des 25 spécialisations et discussion avec les équipes" ,
                "icon"        => IMG_PATH . "/three-block-programs/icon3.svg"
            ) ,
            array(
                "title"       => "Visite du campus" ,
                "img"         => IMG_PATH . "/three-block-programs/programs2.jpg" ,
                "description" => "Découverte de nos salles de classes, studios de tournage, etc." ,
                "icon"        => IMG_PATH . "/three-block-programs/icon2.svg"
            ) ,
            array(
                "title"       => "Rencontre avec les étudiant·es" ,
                "img"         => IMG_PATH . "/three-block-programs/programs3.jpg" ,
                "description" => "Discussion avec les étudiant·es pour connaître leur expérience à Sup de Pub." ,
                "icon"        => IMG_PATH . "/three-block-programs/icon1.svg"
            ) ,
        ];
    }

    public function slider() {
        return [
            array(
                "title" => "<span>35</span> ans d'expérience" ,
                "img"   => IMG_PATH . "/slider/slider1.jpg" ,
            ) ,
            array(
                "title" => "<span>4</span> campus en France" ,
                "img"   => IMG_PATH . "/slider/slider2.jpg" ,
            ) ,
            array(
                "title" => "<span>23</span> spécialités" ,
                "img"   => IMG_PATH . "/slider/slider3.jpg" ,
            ) ,
        ];
    }

    public function guess_the_next_event() {
        $url_ws            = 'https://www.supdepub.com/inseecu/fr/api/form/jpo';
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer"      => false ,
                "verify_peer_name" => false ,
            ) ,
        );
        $data              = file_get_contents( $url_ws , false , stream_context_create( $arrContextOptions ) );
        $data              = json_decode( $data , true );
        $date_of_events    = array();
        foreach ( $data['fields'] as $event ) {
            if ( $event['name'] === "event" ) {
                foreach ( $event['values'] as $value ) {
                    $datetime = $value['start_date'] . " " . $value['start_time'] . ":00";
                    $date_of_events[$value['start_date']] = $datetime ;
                }
            }
        }
        asort( $date_of_events );
        foreach ( $date_of_events as $key => $value ) {
            if ( strtotime( $value ) < time() ) {
                unset( $date_of_events[ $key ] );
            }
        }
        $next_event = array_shift( $date_of_events );
        $counter_to_go = array(
            "days"    => 0 ,
            "hours"   => 0 ,
            "minutes" => 0 ,

        );
        $counter_to_go['days']    = floor( ( strtotime( $next_event ) - time() ) / 86400 );
        if( $counter_to_go['days'] > 0 ) {
            $counter_to_go['hours']   = floor( ( strtotime( $next_event ) - time() ) / 3600 ) - ( $counter_to_go['days'] * 24 );
            $counter_to_go['minutes'] = floor( ( strtotime( $next_event ) - time() ) / 60 ) - ( $counter_to_go['days'] * 24 * 60 ) - ( $counter_to_go['hours'] * 60 );
        } else {
            $counter_to_go['hours']   = floor( ( strtotime( $next_event ) - time() ) / 3600 );
            $counter_to_go['minutes'] = floor( ( strtotime( $next_event ) - time() ) / 60 ) - ( $counter_to_go['hours'] * 60 );
        }
        $days_to_go    = $counter_to_go['days'];
        $hours_to_go   = $counter_to_go['hours'];
        $minutes_to_go = $counter_to_go['minutes'];
        return [
            "next_event" => $next_event ,
            "days_to_go" => $days_to_go,
            "hours_to_go" => $hours_to_go,
            "minutes_to_go" => $minutes_to_go
        ];
    }


    public function ours_campus() {
        return [
            "Bordeaux" => "/" ,
            "Lyon"     => "/" ,
            "Paris"    => "/" ,
            "Rennes"   => "/"
        ];
    }

}

