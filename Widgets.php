<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('tarteaucitron_Widget'))
{
    class tarteaucitron_Widget extends WP_Widget {
    
        public function __construct() {
            parent::__construct(
                'tarteaucitron_widget',
                'tarteaucitron.js',
                array( 'description' => __( 'Ajout des services.', 'tarteaucitron' ), )
            );
        }
	
        public function widget( $args, $instance ) {
            extract( $args );
		
            echo $before_widget;
            echo '<div id="'.$instance['id_uniq'].'"></div>';
            echo $after_widget;
        }
    
        public function form( $instance ) {
            if ( isset( $instance[ 'title' ] ) ) {
                $title = $instance[ 'title' ];
            } else {
                $title = '';
            }
            
            if ( isset( $instance[ 'id_uniq' ] ) ) {
                $id_uniq = $instance[ 'id_uniq' ];
            } else {
                $id_uniq = '';
            }
            
            if ( isset( $instance[ 'img' ] ) ) {
                $img = $instance[ 'img' ];
            } else {
                $img = '';
            }

            // Choix du service
            if($id_uniq == '') {
                $id_uniq = 'wp_'.uniqid().'-'.$this->id;
                echo '<div id="front_'.$id_uniq.'">'.tarteaucitron_post('id_title='.$this->get_field_id( 'title' ).'&id_submit='.$this->get_field_id( 'savewidget' ).'&id='.$id_uniq.'&getForm=2').'</div>
                <img id="img_'.$id_uniq.'" src="//opt-out.ferank.eu/img/services/000.png" style="max-width:100%" alt="" />';

                
            // Affichage du service
            } else {
                echo '<div id="wid_'.$id_uniq.'"></div>
                <img id="img_'.$id_uniq.'" src="//opt-out.ferank.eu/img/services/'.$img.'.png" width="100%" alt="" />';
            }

            echo '<input class="widefat" id="'.$this->get_field_id( 'title' ).'" name="'.$this->get_field_name( 'title' ).'" type="hidden" value="'.esc_attr( $title ).'" />
            <input class="widefat" id="'.$this->get_field_id( 'id_uniq' ).'" name="'.$this->get_field_name( 'id_uniq' ).'" type="hidden" value="'.esc_attr( $id_uniq ).'" />
            <input class="widefat" id="input_img_'.$id_uniq.'" name="'.$this->get_field_name( 'img' ).'" type="hidden" value="'.esc_attr( $img ).'" />';
        }
    
        public function update( $new_instance, $old_instance ) {
            $instance = array();
            $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
            $instance['id_uniq'] = ( !empty( $new_instance['id_uniq'] ) ) ? strip_tags( $new_instance['id_uniq'] ) : '';
            $instance['img'] = ( !empty( $new_instance['img'] ) ) ? strip_tags( $new_instance['img'] ) : '';
            return $instance;
        }
    }
}

// Enregistrement du formulaire
function tarteaucitron_register() {
     if ( 'post' == strtolower( $_SERVER['REQUEST_METHOD'] ) ) {
          $widget_id = $_POST['widget-id'];
          $id_base = $_POST['id_base'];
          $multi_number = preg_replace('#[^-]+-([0-9]+)$#', '$1', $widget_id);

          if($id_base == 'tarteaucitron_widget') {
              $service = $_POST['wp_tarteaucitron__service'];
              if(isset($_POST['tarteaucitron_send_services']) AND $service != '') {
                  $r = 'service='.$service.'&configure_services='.$_POST['wp_tarteaucitron__configure_services'].'&';
                  foreach ($_POST as $key => $val) {
                      if (preg_match('#^wp_tarteaucitron__'.$service.'#', $key)) {
                          $r .= preg_replace('#^wp_tarteaucitron__#', '', $key).'='.$val.'&';
                      }
                  }
                  tarteaucitron_post(trim($r, '&'));
              } elseif ( isset( $_POST['delete_widget'] ) ) {
                  if ( 1 === (int) $_POST['delete_widget'] ) {
                      $i = 'widget-'.$id_base;
                      $id = $_POST[$i][$multi_number]['id_uniq'];
                      tarteaucitron_post('delete=1&data='.$id);
                  }
              }

          }
     }
}
add_action( 'sidebar_admin_setup', 'tarteaucitron_register' );

// Initialisation du widget
add_action('widgets_init', function() {
    register_widget('tarteaucitron_Widget');
});