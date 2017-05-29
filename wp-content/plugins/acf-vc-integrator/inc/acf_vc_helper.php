<?php
class acf_vc_helper {

  public function text($custom_field, $acf_version) {
    return $custom_field["value"];
  }

  public function image($custom_field, $acf_version) {
    $img_details = $custom_field["value"];
    if($acf_version >= 5) {
      if($custom_field["return_format"] == "array") {
        if(isset($img_details["url"])) {
          $ret_val = '<img title="'.$img_details["title"].'" src="'.$img_details["url"].'" alt="'.$img_details["alt"].'" width="'.$img_details["width"].'" height="'.$img_details["height"].'" />';
        } else {
          $ret_val = 'data-mismatch';
        }
      } else {
        $ret_val = $custom_field["value"];
      }
    } else {
      if($custom_field["save_format"] == "object" ) {
      	if(isset($img_details["url"])) {
      		$ret_val = '<img title="'.$img_details["title"].'" src="'.$img_details["url"].'" alt="'.$img_details["alt"].'" width="'.$img_details["width"].'" height="'.$img_details["height"].'" />';
      	} else {
      		$ret_val = 'data-mismatch';
      	}
      } else {
      	$ret_val = $custom_field["value"];
      }
    }
    return $ret_val;
  }

  public function file($custom_field, $acf_version, $link_text) {
    $file_details = $custom_field["value"];
    if($acf_version >= 5) {
      if($custom_field["return_format"] == "array" ) {
        if(isset($file_details["url"])) {
          $ret_val = '<a title="Download '.$file_details["title"].'" href="'.$file_details["url"].'">'.$link_text.'</a>';
        } else {
          $ret_val = 'data-mismatch';
        }
      } else {
        $ret_val = $custom_field["value"];
      }
    } else {
      if($custom_field["save_format"] == "object" ) {
      	if(isset($file_details["url"])) {
      		$ret_val = '<a title="Download '.$file_details["title"].'" href="'.$file_details["url"].'">'.$link_text.'</a>';
      	} else {
      		$ret_val = 'data-mismatch';
      	}
      } else {
      	$ret_val = $custom_field["value"];
      }
    }
    return $ret_val;
  }

  public function select($custom_field, $acf_version) {
    if ( $custom_field["multiple"] === 1 ) {
      if ( !empty($custom_field["value"]) ) {
        $ret_val = '<ul>';
        foreach ($custom_field["value"] as $key => $value) {
            $ret_val .= '<li class="'.$custom_field["name"].' '.$custom_field["name"].'_'.$key.'">'.$value.'</li>';
        }
        $ret_val .= '</ul>';
      }
    } else {
      $ret_val .=  $custom_field["value"];
    }
    return $ret_val;
  }

  public function checkbox($custom_field, $acf_version) {
    $check_values = $custom_field["value"];
    if(is_array($check_values)) {
      $ret_val = implode(", ", $check_values);
    } else {
      $ret_val = '';
    }
    return $ret_val;
  }

  public function user($custom_field, $acf_version) {
    $user_details = $custom_field["value"];
    $ret_val = $user_details["display_name"];
    return $ret_val;
  }

  public function page_link($custom_field, $acf_version, $link_text) {
    $page_link = $custom_field["value"];
    $ret_val = '<a href="'.$page_link.'">'.$link_text.'</a>';
    return $ret_val;
  }

  public function google_map($custom_field, $acf_version) {
    $map_details = $custom_field["value"];
    $ret_val = '<iframe width="100%" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q='.$map_details["lat"].','.$map_details["lng"].'&hl=es;z=14&amp;output=embed"></iframe>';
    return $ret_val;
  }

  public function color_picker($custom_field, $acf_version) {
    $ret_val = '<div style="display: inline-block; height: 15px; width: 15px; margin: 0px 5px 0px 0px; background-color: '.$custom_field["value"].'"></div>'.$custom_field["value"];
    return $ret_val;
  }

  public function true_false($custom_field, $acf_version) {
    if(1 == $custom_field["value"]) $ret_val = 'True'; else $ret_val = "False";
    return $ret_val;
  }

  public function taxonomy($custom_field, $acf_version) {
    $terms = $custom_field["value"];
    if(!empty($terms)) {
      $ret_val = "<ul>";
      foreach($terms as $term) {
        $term_details = get_term( $term, 'category', ARRAY_A );
        $ret_val .= '<li><a href="'.get_term_link( $term_details["term_id"], 'category' ).'" title="'.$term_details["name"].'">'.$term_details["name"].'</a></li>';
      }
      $ret_val .= "</ul>";
    }
    return $ret_val;
  }

  public function post_object($custom_field, $acf_version) {
    $post_obj = $custom_field["value"];
    $post_id = $post_obj->ID;
    $ret_val .= '<a href="'.get_permalink($post_id).'" title="'.get_the_title($post_id).'">'.get_the_title($post_id).'</a>';
    return $ret_val;
  }

  public function relationship($custom_field, $acf_version) {
    $posts = $custom_field["value"];
    $ret_val = "<ul>";
    foreach($posts as $post_details) {
      $post_id = $post_details->ID;
      $ret_val .= '<li><a href="'.get_permalink($post_id).'" title="'.get_the_title($post_id).'">'.get_the_title($post_id).'</a></li>';
    }
    $ret_val .= "</ul>";

    return $ret_val;
  }

  public function url($custom_field, $acf_version) {
    $url = $custom_field["value"];
      $ret_val .= '<a href="'.$url.'">'.$url.'</a>';
    return $ret_val;
  }

}

class acf_vc_pro_helper extends acf_vc_helper {

  public function repeater($custom_field, $acf_version, $link_text) {

    $ret_val = '<td id="'.$custom_field["key"].'" class="'.$custom_field["key"].' '.$custom_field["_name"].'">';
      if ( 'text' === $custom_field["type"] ) {
        if ( !empty($custom_field["value"]) ) {
          $ret_val .= parent::text($custom_field, $acf_version);
        }
      } elseif ( 'image' === $custom_field["type"]) {
        if ( !empty($custom_field["value"]) ) {
          $ret_val .= parent::image($custom_field, $acf_version);
        }
      } elseif('file' === $custom_field["type"]) {
        if ( !empty($custom_field["value"]) ) {
          $ret_val .= parent::file($custom_field, $acf_version, $link_text);
        }
			} elseif ( 'select' === $custom_field["type"]) {
        if ( !empty($custom_field["value"]) ) {
          $ret_val .= parent::select($custom_field, $acf_version);
        }
      } elseif ( 'checkbox' === $custom_field["type"] ) {
        if ( !empty($custom_field["value"]) ) {
          $ret_val .= parent::checkbox($custom_field, $acf_version);
        }
			} elseif ( 'user' === $custom_field["type"] ) {
        if ( !empty($custom_field["value"]) ) {
          $ret_val .= parent::user($custom_field, $acf_version);
        }
			} elseif ( 'page_link' === $custom_field["type"] ) {
        if ( !empty($custom_field["value"]) ) {
          $ret_val .= parent::page_link($custom_field, $acf_version, $link_text);
        }
			} elseif ( 'google_map' === $custom_field["type"] ) {
        if ( !empty($custom_field["value"]) ) {
          $ret_val .= parent::google_map($custom_field, $acf_version);
        }
			} elseif ('color_picker' === $custom_field["type"]) {
        if ( !empty($custom_field["value"]) ) {
          $ret_val .= parent::color_picker($custom_field, $acf_version);
        }
      } elseif ('true_false' === $custom_field["type"]) {
          $ret_val .= parent::true_false($custom_field, $acf_version);
      } elseif ('taxonomy' === $custom_field["type"]) {
          $ret_val .= parent::taxonomy($custom_field, $acf_version);
      } elseif('post_object' === $custom_field["type"]) {
        if ( !empty($custom_field["value"]) ) {
          $ret_val .= parent::post_object($custom_field, $acf_version);
        }
			} elseif('relationship' === $custom_field["type"]) {
        if ( !empty($custom_field["value"]) ) {
          $ret_val .= parent::relationship($custom_field, $acf_version);
        }
			} elseif('url' === $custom_field["type"]) {
        $ret_val .= parent::url($custom_field, $acf_version);
			} else {
        $ret_val .= $custom_field;
      }
    $ret_val .= '</td>';

    return $ret_val;
  }

}
