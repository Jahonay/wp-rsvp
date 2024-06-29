<?php

/**
 * Plugin Name: rsvp-plugin
 * Plugin URI: https://www.jahonay.github.io/
 * First mock up of a plugin which will create an rsvp reeader
 * Version: 0.1
 * Author: John-Mackey
 * Author URI: https://www.johnmackeydesigns.com/
 **/




function simple_form_enqueue_scripts()
{
    wp_enqueue_script('simple-form-ajax', plugin_dir_url(__FILE__) . 'simple-form-ajax.js', array('jquery'), null, true);
    wp_localize_script('simple-form-ajax', 'simpleFormAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('simple_form_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'simple_form_enqueue_scripts');

function simple_form_create_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'rsvp_text';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        text text NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'simple_form_create_table');

function simple_form_ajax_handler()
{

    try {

        check_ajax_referer('simple_form_nonce', 'nonce');


        $a = new PDF2Text();
        $a->setFilename($_FILES['file']['name']);
        $a->decodePDF();



        global $wpdb;
        $table_name = $wpdb->prefix . 'rsvp_text';

        $input_text = $a->output();

        if ($input_text == '' || null) {
            $input_text = 'cats';
        }

        $wpdb->insert($table_name, array('text' => $input_text));

        wp_send_json_success($input_text);
    } catch (exception $e) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'rsvp_text';

        $input_text = $a->output();


        $input_text = 'fail';


        $wpdb->insert($table_name, array('text' => $input_text));

        wp_send_json_success($input_text);
    }
}
add_action('wp_ajax_simple_form', 'simple_form_ajax_handler');
add_action('wp_ajax_nopriv_simple_form', 'simple_form_ajax_handler');


function rsvp_form()
{

    $html = '
    
     <div class="container rsvp-cont">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <form id="rsvp_form" method="post" enctype="application/x-www-form-urlencoded">
                    <div class="form-group">
                        <input class="form-control" type="file" name="pdf" id="pdf">
                        <button class="btn btn-primary" type="submit" accept="application/pdf">Submit</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
        ';
    return $html;
}
add_shortcode('rsvp_form', 'rsvp_form');
?>


<script>
    function reading_helper($string, $interval = 4000) {

        var words = $string;
        var box = document.querySelector("#box");
        var button = document.querySelector("#play_btn");
        var range = document.querySelector("input[type=\"range\"]");
        var pause = false;
        var stopped_num = 0;

        var i = 0;
        if (words && button) {
            function read(interval) {
                setInterval(function() {

                    box.innerHTML = words[i];
                    if (pause == true) {
                        stopped_num = i;
                        clearInterval();
                        return false;
                    }

                    i++;
                }, 4000);
            }
            button.onclick = function() {
                if (pause) {
                    pause = false;
                    read();
                } else {
                    pause = true;
                }

            }

            range.onchange = function() {
                clearInterval();
                interval = range.value;

                read();
            }
            read();
        }
    }
</script>

<?php
