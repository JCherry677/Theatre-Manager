<script type="text/javascript">
    jQuery(document).ready(function( $ ){
        $( '#add-row' ).on('click', function() {
            var row = $( '.empty-cast-row.screen-reader-text' ).clone(true);
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
        width: 50%;
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
    }

    .th_show_person_info_row{
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
    }

    .th_show_person_info_field{
        flex-shrink: 2;
        margin:5px;
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
                if ( $repeatable_fields ) :

                foreach ( $repeatable_fields as $field ) {
                    if($field['actor'] != ''){
                        $author_id = $field['actor'];
                    }
                ?>
                <tr>
                    <td><input type="text" class="widefat" name="role[]" value="<?php echo esc_attr( $field['role'] ); ?>" /></td>
                    <td><?php 
                        echo "<select id='th_show_person' name='actor[]'>";
                        // Query the authors here
                        $query = new WP_Query( 'post_type=theatre_person' );
                        while ( $query->have_posts() ) {
                            $query->the_post();
                            $id = get_the_ID();
                            $selected = "";

                            if($id == $author_id){
                                echo '<option selected="selected" value=' . $id . '>' . get_the_title() . '</option>';
                            } else {
                                echo '<option value=' . $id . '>' . get_the_title() . '</option>';
                            }
                            
                        }
                        echo "</select>";
                    ?></td>
                    <td><a class="button remove-row" href="#">Remove</a></td>
                </tr>
                <?php
                } 
                endif; ?>

                <!-- empty hidden one for jQuery -->
                <tr class="empty-cast-row screen-reader-text">
                    <td><input type="text" class="widefat" name="role[]" /></td>
                    <td><?php 
                        echo "<select id='th_show_person' name='actor[]'>";
                        // Query the authors here
                        $query = new WP_Query( 'post_type=theatre_person' );
                        while ( $query->have_posts() ) {
                            $query->the_post();
                            $id = get_the_ID();

                            echo '<option value=' . $id . '>' . get_the_title() . '</option>';
                        }
                        echo "</select>";
                    ?></td>
                    <td><a class="button remove-row" href="#">Remove</a></td>
                </tr>
            </tbody>
        </table>

        <p><a id="add-row" class="button" href="#">Add Cast Member</a></p>
    </div>
</div>