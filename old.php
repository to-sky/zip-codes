if ($postTypes) {
                foreach ($postTypes as $postType) {  
                    if ( $postType != 'attachment' && $postType != 'revision' && $postType != 'nav_menu_item' && $postType != 'acf') {               
                        if ( $postType == get_option('selected_post_type') ) {
                            echo '<option name="selected_post_type" selected="selected">' . $postType;
                            continue;
                        } 
                        echo '<option name="selected_post_type">' . $postType;
                    }
                }
                echo '<input id="hiddenPostypes" type="hidden" name="selected_post_type" value=""/>';        
            }