<script type="text/javascript">
    jQuery(document).ready(function( $ ){
        $( '#add-warning-row' ).on('click', function() {
            var row = $( '.empty-warning-row.screen-reader-text' ).clone(true);
            row.removeClass( 'empty-warning-row screen-reader-text' );
            row.insertBefore( '#repeatable-fieldset-warning tbody>tr:last' );
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
        <table id="repeatable-fieldset-warning" width="100%">
            <thead>
                <tr>
                    <th width="60%">Warning</th>
                    <th width="8%"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $repeatable_fields = get_post_meta($post->ID, 'th_show_content_warning_data', true);
                if ( $repeatable_fields ){

                    foreach ( $repeatable_fields as $field ) {
                        if($field['warning'] != ''){
                            $author_id = $field['warning'];
                        }
                    ?>
                        <tr>
                            <td><?php 
                                echo "<select id='th_show_warning' name='content[]'>";
                                // Query the authors here
                                $args = array(
                                    'posts_per_page'    => -1,
                                    'post_type'         => 'theatre_warning',
                                    'orderby'           => 'title',
                                    'order'             => 'ASC',
                                );
                                $query = new WP_Query($args);
                                while ( $query->have_posts() ) {
                                    $query->the_post();
                                    $id = get_the_ID();
                                    $selected = "";

                                    if($id == $author_id){
                                        echo '<option selected value=' . $id . '>' . get_the_title() . '</option>';
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
                }?>

                <!-- empty hidden one for jQuery -->
                <tr class="empty-warning-row screen-reader-text">
                    <td><?php 
                        echo "<select id='th_show_warning' name='content[]'>";
                        // Query the authors here
                        $args = array(
                            'posts_per_page'    => -1,
                            'post_type'         => 'theatre_warning',
                            'orderby'           => 'title',
                            'order'             => 'ASC',
                        );
                        $query = new WP_Query($args);
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

        <p><a id="add-warning-row" class="button" href="#" style="margin-left: -115px;">Add Content Warning</a></p>
    </div>
</div>