<div class="th_show_info">
    <style scoped>
        .th_show_info{
            width: 33%;
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
        }

        .th_show_info_row{
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
        }

        .th_show_info_field{
            flex-shrink: 2;
            margin:5px;
        }

        .th_show_info > .th_show_info_field {
            padding-bottom: 5px;
            border-bottom: 1px #ddd solid;
        }

        .th_show_info > .th_show_info_field > label{
            float: left;
            width: 150px;
            vertical-align: center;
        }

        .th_show_info > .th_show_info_field > input {
            width: 280px;
        }

        .nomargin {
            margin:0 0 0 5px;
        }
    </style>
    <div class="meta-options th_show_info_field">
        <label for="th_show_info_author">Playwrite</label>
        <input type="text" id="th_show_info_author" name="th_show_info_author" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'th_show_info_author', true ) ); ?>">
    </div>
    <p class="nomargin">Performance Dates</p>
    <div class="th_show_info_field th_show_info_row">
        <div class="meta-options th_show_info_field">
            <label for="th_show_info_start_date">Start</label>
            <input type="date" id="th_show_info_start_date" name="th_show_info_start_date" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'th_show_info_start_date', true ) ); ?>">
        </div>
        <div class="meta-options th_show_info_field">
            <label for="th_show_info_end_date">End</label>
            <input type="date" id="th_show_info_end_date" name="th_show_info_end_date" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'th_show_info_end_date', true ) ); ?>">
        </div>
    </div>
</div>
