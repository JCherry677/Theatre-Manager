<script type="text/javascript">
    jQuery(document).ready(function( $ ){
        $( '#add-row' ).on('click', function() {
            var row = $( '.empty-row.screen-reader-text' ).clone(true);
            row.removeClass( 'empty-row screen-reader-text' );
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
    .th_person_info{
        width: 33%;
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
    }

    .th_person_info_row{
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
    }

    .th_person_info_field{
        flex-shrink: 2;
        margin:5px;
    }

    .th_person_info > .th_person_info_field {
        padding-bottom: 5px;
        border-bottom: 1px #ddd solid;
    }

    .th_person_info > .th_person_info_field > label{
        float: left;
        width: 150px;
        vertical-align: center;
    }

    .th_person_info > .th_person_info_field > input {
        width: 280px;
    }

    .nomargin {
        margin:0 0 0 5px;
        font-weight: bold;
    }
</style>
<div class="th_person_info_row">
    <div class="th_person_info">
        <h2 class="nomargin">Course History</h2>
        <div class="th_person_info_field">
            <table id="repeatable-fieldset-one" width="100%">
            <thead>
                <tr>
                    <th width="40%">Course</th>
                    <th width="40%">Graduation Year</th>
                    <th width="8%"></th>
                </tr>
            </thead>
            <tbody>
            <?php
            $repeatable_fields = get_post_meta($post->ID, 'th_person_info_data', true);
            if ( $repeatable_fields ) :

            foreach ( $repeatable_fields as $field ) {
            ?>
            <tr>
                <td><input type="text" class="widefat" name="course[]" value="<?php if($field['course'] != '') echo esc_attr( $field['course'] ); ?>" /></td>

                <td><input type="text" class="widefat" name="grad[]" value="<?php if ($field['grad'] != '') echo esc_attr( $field['grad'] ); else echo 'http://'; ?>" /></td>

                <td><a class="button remove-row" href="#">Remove</a></td>
            </tr>
            <?php
            } else :
            // show a blank one
            ?>
            <tr>
                <td><input type="text" class="widefat" name="course[]" /></td>

                <td><input type="text" class="widefat" name="grad[]" value="" /></td>

                <td><a class="button remove-row" href="#">Remove</a></td>
            </tr>
            <?php endif; ?>

            <!-- empty hidden one for jQuery -->
            <tr class="empty-row screen-reader-text">
                <td><input type="text" class="widefat" name="course[]" /></td>

                <td><input type="text" class="widefat" name="grad[]" value="" /></td>

                <td><a class="button remove-row" href="#">Remove</a></td>
            </tr>
            </tbody>
            </table>

            <p><a id="add-row" class="button" href="#">Add another</a></p>
        </div>
    </div>
</div>