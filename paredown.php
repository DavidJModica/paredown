<?php
    /**
     * Plugin Name: Tag Pare Down
     * Plugin URl: DavidModica.net
     * Description: Merges tags and optionally deletes redundant ones
     * Author: David
     * Version: 1.0.0
     * Author URL: DavidModica.net
     * 
     * Merges tags and optionally deletes redundant ones
     *
     * ## OPTIONS
     *
     * <new_tag>
     * : The name of the tag to migrate to.
     *
     * <source_tag>
     * : The name of the tag to migrate from.
     * 
     * <remove_old>
     * : Deletes the source_tag if set to 'delete' otherwise leaves the source_tag
     *
     * ## EXAMPLES
     *
     *     wp paredown elon elonmusk delete
     *
     * @when after_wp_load
     */
if(defined('WP_CLI') && WP_CLI)
{
    class dm_cli
   {
        function tagparedown($args, $assoc_args)
        {
            $new_tag = $args[0];
            $source_tag = $args[1];
            $remove_old = $args[2];
            
            if(!$new_tag)
            {
                WP_CLI::log('Missing new_tag - Usage: wp paredown tagparedown new_tag source_tag [remove_old]');
                return;
            }
            elseif (!$source_tag)
            {
                WP_CLI::log('Missing source_tag - Usage: wp paredown tagparedown new_tag source_tag [remove_old]');
                return;
            }
            
            $new_tag_obj = get_term_by('name', $source_tag, 'post_tag');
            $source_tag_obj = get_term_by('name', $source_tag, 'post_tag');
            /*if(!$source_tag_obj)
            {
               /WP_CLI::log('Could not find '.$new_tag.' in the list of tags.');
            }
            elseif (!$source_tag_obj)
            {
               // WP_CLI::log('Could not find '.$source_tag.' in the list of tags.');
            }
            */
            $new_tag_id = $new_tag_obj->term_id;
            $source_tag_id = $source_tag_obj->term_id;
            if($source_tag_id == '')
            {
                WP_CLI::log('Could not find '.$source_tag.' in the list of tags.');
                return;
            }
            $query = new WP_Query(['tag_id' => $source_tag_id]);
            //$posts = $query->posts;
            $num_posts = $query->found_posts;
            //WP_CLI::log('Source tag ID = ' . $source_tag_id);
            WP_CLI::log('Found ' . $num_posts . ' posts with tag ' . $source_tag);
            $err_posts = 0;
            /*
            if(!$posts)
            {

                return "Could not find any posts with $source_tag in the list of tags.";
            }
            */
            //$progress = \WP_CLI\Utils\make_progress_bar( 'Tagging Posts', $num_posts );
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    try
                {
                    $post_id = get_the_ID();
                    wp_add_post_tags($post_id , $new_tag);
                    WP_CLI::log('Added tag '. $new_tag . ' to post ' . get_the_title());
                    //$progress->tick();
                }
                catch(exception $e)
                {
                    $num_posts--;
                    $err_posts++;
                    WP_CLI::log('Could not add '.$new_tag.' to post '. $post_id);
                }
                }
            }
           
            //$progress->finish();
            if($remove_old == 'delete')
            {
                $remove_err = false;
                try
                {
                    WP_CLI::confirm('Are you sure you want to delete '.$source_tag, $assoc_args);
                    wp_delete_term($source_tag_id, 'post_tag');
                    WP_CLI::log('Deleted tag '.$source_tag);

                }
                catch(exception $e)
                {
                    $remove_err = true;
                    WP_CLI::log('Could not deleted tag '.$source_tag);                }
            }

            $return_string = "Finished Paredown. "; 
            $return_string.=$num_posts ? "Successfully updated $num_posts posts. " : '';
            $return_string.=$err_posts ? "Failed to update $err_posts posts. " : '';
            WP_CLI::log($return_string);
       }

    }
    WP_CLI::add_command( 'paredown', 'dm_cli' );

}
?>
