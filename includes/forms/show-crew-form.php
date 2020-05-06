<script type="text/javascript">
    jQuery(document).ready(function( $ ){
        $( '#add-crew-row' ).on('click', function() {
            var row = $( '.empty-crew-row.screen-reader-text' ).clone(true).on('focus', function(){
            $(this).suggest(th_ajax_url + '?action=th_person_lookup', {minchars:1});
            return false;
        });
            row.removeClass( 'empty-crew-row screen-reader-text' );
            row.insertBefore( '#repeatable-fieldset-two tbody>tr:last' );
            return false;
        });

        $( '.remove-row' ).on('click', function() {
            $(this).parents('tr').remove();
            return false;
        });
    });
</script>
<div class="th_show_person_info">
    <div class="th_show_person_info_field">
        <?php if($people){?>
            <p style="font-weight: bold;">Members must be added first before adding them to a show!</p>
            <p>Member names should be added to the Actors box in the format <code>name (id)</code></p>
            <p>Enter the member's first name and then use the dropdown to ensure this format is correct</p>
        <?php } ?>
        <table id="repeatable-fieldset-two" width="100%">
            <thead>
                <tr>
                    <th width="40%">Role</th>
                    <th width="40%">Person</th>
                    <th width="8%"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $repeatable_fields = get_post_meta($post->ID, 'th_show_crew_info_data', true);
                if ( $repeatable_fields ){
                    if ($people){
                        foreach ( $repeatable_fields as $key => $value ) {
                            foreach ($value as $item){?>
                                <tr>
                                    <td><input type="text" class="widefat" name="crew-job[]" value="<?php echo esc_attr( $item ); ?>" /></td>
                                    <td><input type="text" class="widefat th_person_search_class" name="crew-person[]" value="<?php echo esc_attr(tm_name_lookup($key, 'theatre_person') . " (" . $key . ")" )?>" /></td>
                                    <td><a class="button remove-row" href="#">Remove</a></td>
                                </tr>
                            <?php }
                        }
                    } else {
                        foreach ( $repeatable_fields as $key => $value ) {
                            foreach ($value as $item){?>
                                <tr>
                                    <td><input type="text" class="widefat" name="crew-job[]" value="<?php echo esc_attr( $item ); ?>" /></td>
                                    <td><input type="text" class="widefat" name="crew-person[]" value="<?php echo esc_attr($key)?>" /></td>
                                    <td><a class="button remove-row" href="#">Remove</a></td>
                                </tr>
                            <?php }
                        }
                    }
                }
                ?>

                <!-- empty hidden one for jQuery -->
                <tr class="empty-crew-row screen-reader-text">
                    <td><input type="text" class="widefat" name="crew-job[]" /></td>
                    <td><input type="text" class="widefat <?php if($people){echo ('th_person_search_class');}?>" name="crew-person[]" value="" /></td>
                    <td><a class="button remove-row" href="#">Remove</a></td>
                </tr>
            </tbody>
        </table>

        <p><a id="add-crew-row" class="button" href="#" style="margin-left: -80px;">Add Crew Member</a></p>
    </div>
</div>