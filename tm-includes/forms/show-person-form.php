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
        $( '#add-cast-row' ).on('click', function() {
            var row = $( '.empty-cast-row' ).clone(true);
            row.removeClass( 'empty-cast-row screen-reader-text' );
            row.insertBefore( '#repeatable-fieldset-one tbody>tr:last' );
            return false;
        });

        $( '.remove-row' ).on('click', function() {
            $(this).parents('tr').remove();
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
            <p style="font-weight: bold;">Members must be added to the members tab before adding them to a show!</p>
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
                                    <td><input type="text" autocomplete="off" class="widefat" name="role[]" value="<?php echo esc_attr( $item ); ?>" /></td>
                                    <td><select class="widefat tm-searchable" name="actor[]">
                                            <?php foreach ($names as $id => $name){

                                                echo '<option value="' . $id . '"';
                                                if ($id == $key) echo " selected";
                                                echo '>' . $name .'</option>';
                                            }?>
                                        </select> </td>
                                    <td><a class="button remove-row" href="#">Remove</a></td>
                                </tr>
                                <?php
                            }
                        }
                    } else {
                        foreach ($repeatable_fields as $key => $value) {
                            foreach ($value as $item){?>
                                <tr>
                                    <td><input type="text" autocomplete="off" class="widefat" name="role[]" value="<?php echo esc_attr( $item ); ?>" /></td>
                                    <td><input type="text" autocomplete="off" class="widefat" name="actor[]" value="<?php echo esc_attr($key) ?>" /></td>
                                    <td><a class="button remove-row" href="#">Remove</a></td>
                                </tr>
                                <?php
                            }
                        }
                    }
                }?>

                    <!-- empty hidden one for jQuery -->
                    <tr id="empty-cast-row screen-reader-text">
                        <td><input type="text" class="widefat" name="role[]" /></td>
                        <?php if ($people) { ?>
                            <td><select class="widefat tm-searchable" name="actor[]">
                                    <?php foreach ($names as $id => $name){

                                        echo '<option value="' . $id . '">' . $name .'</option>';
                                    }?>
                            </select> </td>
                        <?php } else { ?>
                            <td><input type="text" class="widefat" name="actor[]" value="" /></td>
                        <?php } ?>
                        <td><a class="button remove-row" href="#">Remove</a></td>
                    </tr>
            </tbody>
        </table>

        <p><a id="add-cast-row" class="button" href="#" style="margin-left: -80px;">Add Cast Member</a></p>
    </div>
</div>