<?php
//if people enabled, do stuff differently
$options = get_option( 'tm_settings' );
$people = false;
if (isset($options['tm_people']) && $options['tm_people'] == 1){
    $people = true;
}
$names = tm_get_names_array();
?>
<script type="text/javascript">
    jQuery(document).ready(function( $ ){
        $( '#add-crew-row' ).on('click', function() {
            var row = $( '.empty-crew-row.screen-reader-text' ).clone(true)
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
            <p style="font-weight: bold;">Members must be added to the members tab before adding them to a show!</p>
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
                                    <td><input type="text" class="widefat" autocomplete="off" name="crew-job[]" value="<?php echo esc_attr( $item ); ?>" /></td>
                                    <td><select class="widefat tm-searchable" name="crew-person[]">
			                                <?php foreach ($names as $id => $name){
				                                echo '<option value="' . $id . '"';
				                                if ($id == $key) echo "selected";
				                                echo '>' . $name .'</option>';
			                                }?>
                                        </select> </td>
                                    <td><a class="button remove-row" href="#">Remove</a></td>
                                </tr>
                            <?php }
                        }
                    } else {
                        foreach ( $repeatable_fields as $key => $value ) {
                            foreach ($value as $item){?>
                                <tr>
                                    <td><input type="text" class="widefat" name="crew-job[]" autocomplete="off" value="<?php echo esc_attr( $item ); ?>" /></td>
                                    <td><input type="text" class="widefat" name="crew-person[]" autocomplete="off" value="<?php echo esc_attr($key)?>" /></td>
                                    <td><a class="button remove-row" href="#">Remove</a></td>
                                </tr>
                            <?php }
                        }
                    }
                }
                if ($people) { ?>
                    <!-- empty hidden one for jQuery -->
                    <tr class="empty-cast-row screen-reader-text">
                        <td><input type="text" class="widefat" name="role[]" /></td>
                        <td><select class="widefat tm-searchable" name="crew-person[]">
				                <?php foreach ($names as $id => $name){

					                echo '<option value="' . $id . '">' . $name .'</option>';
				                }?>
                            </select> </td>
                        <td><a class="button remove-row" href="#">Remove</a></td>
                    </tr>
                <?php } else { ?>
                    <!-- empty hidden one for jQuery -->
                    <tr class="empty-crew-row screen-reader-text">
                        <td><input type="text" class="widefat" name="crew-job[]" /></td>
                        <td><input type="text" class="widefat" name="crew-person[]" value="" /></td>
                        <td><a class="button remove-row" href="#">Remove</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <p><a id="add-crew-row" class="button" href="#" style="margin-left: -80px;">Add Crew Member</a></p>
    </div>
</div>