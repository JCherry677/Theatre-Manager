<?php
//if people enabled, do stuff differently
$options = get_option( 'tm_settings' );
$people = false;
if (isset($options['tm_people']) && $options['tm_people'] == 1){
    $people = true;
}?>
<script type="text/javascript">
    var th_ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
    jQuery(document).ready(function( $ ){
        $( '#add-row' ).on('click', function() {
            var row = $( '.empty-cast-row.screen-reader-text' ).clone(true).on('focus', function(){
            $(this).suggest(th_ajax_url + '?action=th_person_lookup', {minchars:1});
            return false;
        });
            row.removeClass( 'empty-cast-row screen-reader-text' );
            row.insertBefore( '#repeatable-fieldset-one tbody>tr:last' );
            return false;
        });

        $( '.remove-row' ).on('click', function() {
            $(this).parents('tr').remove();
            return false;
        });
        $('.th_person_search_class').on('focus', function(){
            $(this).suggest(th_ajax_url + '?action=th_person_lookup', {minchars:1});
            return false;
        });
    });
</script>
<style scoped>
    .th_show_person_info{
        width: 70%;
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        margin: auto;
        text-align:center;
    }

    .th_show_person_info_row{
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        text-align:center;
    }

    .th_show_person_info_field{
        flex-shrink: 2;
        margin:5px;
        text-align:center;
    }

    .th_show_person_info > .th_show_person_info_field {
        padding-bottom: 5px;
        border-bottom: 1px #ddd solid;
    }

    .th_show_person_info > .th_show_person_info_field > label{
        float: left;
        width: 150px;
        vertical-align: center;
    }

    select{
        float: left;
        width: 100%;
        vertical-align: center;
    }

    .th_show_person_info > .th_show_person_info_field > input {
        width: 280px;
    }

    .nomargin {
        margin:0 0 0 5px;
        font-weight: bold;
    }
</style>
<div class="th_show_person_info">
    <div class="th_show_person_info_field">
        <?php if($people){?>
            <p style="font-weight: bold;">Members must be added first before adding them to a show!</p>
            <p>Member names should be added to the Actors box in the format <code>name (id)</code></p>
            <p>Enter the member's first name and then use the dropdown to ensure this format is correct</p>
        <?php }?>
        <table id="repeatable-fieldset-one" width="100%">
            <thead>
                <tr>
                    <th width="40%">Role</th>
                    <th width="40%">Actor</th>
                    <th width="8%"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $repeatable_fields = get_post_meta($post->ID, 'th_show_person_info_data', true);
                if ( $repeatable_fields ) {
                    if ($people){
                        foreach ($repeatable_fields as $key => $value) {
                            foreach ($value as $item){?>
                                <tr>
                                    <td><input type="text" class="widefat" name="role[]" value="<?php echo esc_attr( $item ); ?>" /></td>
                                    <td><input type="text" class="widefat th_person_search_class" name="actor[]" value="<?php echo esc_attr(get_person_name($key) . " (" . $key . ")" )?>" /></td>
                                    <td><a class="button remove-row" href="#">Remove</a></td>
                                </tr>
                                <?php
                            }
                        } 
                    } else {
                        foreach ($repeatable_fields as $key => $value) {
                            foreach ($value as $item){?>
                                <tr>
                                    <td><input type="text" class="widefat" name="role[]" value="<?php echo esc_attr( $item ); ?>" /></td>
                                    <td><input type="text" class="widefat" name="actor[]" value="<?php echo esc_attr($key) ?>" /></td>
                                    <td><a class="button remove-row" href="#">Remove</a></td>
                                </tr>
                                <?php
                            }
                        } 
                    }
                } ?>

                <!-- empty hidden one for jQuery -->
                <tr class="empty-cast-row screen-reader-text">
                    <td><input type="text" class="widefat" name="role[]" /></td>
                    <td><input type="text" class="widefat <?php if($people){echo ('th_person_search_class');}?>" name="actor[]" value="" /></td>
                    <td><a class="button remove-row" href="#">Remove</a></td>
                </tr>
            </tbody>
        </table>

        <p><a id="add-row" class="button" href="#" style="margin-left: -80px;">Add Cast Member</a></p>
    </div>
</div>