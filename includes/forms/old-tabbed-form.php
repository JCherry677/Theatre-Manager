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
    * {box-sizing: border-box}
    .category-tabs {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
    }
    /* Style the buttons that are used to open the tab content */
    .category-tabs li a{
        background-color: inherit;
        float: left;
        border-right: 1px solid #ccc;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
        text-decoration: none;
    }
    /* Style the tab content */
    .tabcontent {
        display: none;
        margin-top: -25px;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-top: none;
    }
    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #ddd;
    }
    /* Create an active/current tablink class */
    .tab button.active {
        background-color: #ccc;
    }
</style>
<div id="committeetabs">
    <ul class="category-tabs">
        <li><a href="#chair">Chair</a></li>
        <li><a href="#ivc">IVC</a></li>
        <li><a href="#evc">EVC</a></li>
        <li><a href="#treas">Treasurer</a></li>
        <li><a href="#sec">Secretary</a></li>
        <li><a href="#barn">Barn Manager</a></li>
        <li><a href="#tech">Tech Manager</a></li>
        <li><a href="#c&p">Costume & Props Manager</a></li>
        <li><a href="#work">Workshop Director</a></li>
        <li><a href="#mark">Marketing and Events</a></li>
        <li><a href="#odn">ODN Rep</a></li>
        <li><a href="#new" style="margin-top: -18px;">New Works Rep</a></li>
    </ul>
    <br class="clear" />
    <div class="tabcontent" id="chair">
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
    <div class="tabcontent hidden" id="ivc">
        <p>#2 - Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
    </div>
    <div class="tabcontent hidden" id="evc">
        <p>#3 - Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
    </div>
    <div class="tabcontent hidden" id="treas">
        <p>#3 - Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
    </div>
    <div class="tabcontent hidden" id="sec">
        <p>#3 - Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
    </div>
    <div class="tabcontent hidden" id="barn">
        <p>#3 - Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
    </div>
    <div class="tabcontent hidden" id="tech">
        <p>#3 - Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
    </div>
    <div class="tabcontent hidden" id="c&p">
        <p>#3 - Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
    </div>
    <div class="tabcontent hidden" id="work">
        <p>#3 - Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
    </div>
    <div class="tabcontent hidden" id="mark">
        <p>#3 - Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
    </div>
    <div class="tabcontent hidden" id="odn">
        <p>#3 - Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
    </div>
    <div class="tabcontent hidden" id="new">
        <p>#3 - Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
    </div>
</div>