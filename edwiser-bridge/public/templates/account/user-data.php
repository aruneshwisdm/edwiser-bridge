            <aside class="eb-user-picture">
                <?php echo $user_avatar;?>
            </aside>
            <div class="eb-user-data">
                <div>
                    <?php
                    printf(esc_attr__('Hello %s%s%s (not %2$s? %sSign out%s)', 'eb-textdomain'), '<strong>', esc_html($user->display_name), '</strong>', '<a href="'.esc_url(wp_logout_url(get_permalink())).'">', '</a>');
        ?>
                </div>
            </div>
